<?php

namespace App\Services;

use App\Models\Keyword;
use App\Models\Project;
use App\Models\URL;
use App\Models\UrlData;
use App\Models\UrlKeyword;
use App\Models\UrlKeywordData;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\DimensionValue;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Client;
use Google\Exception;
use Google\Service\AnalyticsReporting\GetReportsResponse;
use Google\Service\Webmasters;
use Google_Client;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_ReportRequest;
use Throwable;
use Google_Service_Webmasters;

class GoogleAnalyticsService
{
    public $analytics;
    public $reportings;
    protected $key_file = 'credentials.json';
    protected $beta_analytics_data_credentials;
    public $scopes = ['https://www.googleapis.com/auth/analytics.readonly'];
    public $appName = "Hello Analytics Reporting";
    public $accountId = '109167922';
    const SERVICE_ACCOUNT = 'starting-account-bfkqmhlvx8j0';
    const PAGE_DIMENSION = 'ga:pagePath';
    const GOOGLE_OAUTH_TOKEN_URL = 'https://accounts.google.com/o/oauth2/token';
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

        public function __construct ()
    {
        $this->key_file = config_path() . DIRECTORY_SEPARATOR . $this->key_file;
        $this->beta_analytics_data_credentials = self::getAnalyticCredsPath();
    }

    /**
     * @return Google_Service_AnalyticsReporting
     * @throws Exception
     */
    protected function getAuthorizedReportingObject (): Google_Service_AnalyticsReporting
    {
        $client = new Google_Client();
        $client->setApplicationName($this->appName);
        $client->setAuthConfig($this->key_file);
        $client->setScopes($this->scopes);

        return new Google_Service_AnalyticsReporting($client);
    }

    /**
     * @param int $property
     * @param array $dimensions
     * @param array $metrics
     * @param string $startDate
     * @param string $endDate
     * @return RunReportResponse
     * @throws ApiException
     * @throws ValidationException
     */
    public function makeGAApiCall (int $property, array $dimensions, array $metrics, string $startDate, string $endDate = 'today'): RunReportResponse
    {
        $client = new BetaAnalyticsDataClient([
            'credentials' => $this->beta_analytics_data_credentials,
        ]);

        $dimensionsData = [];
        $metricsData = [];

        foreach ($dimensions as $name) {
            $dimensionsData[] = new Dimension(['name' => $name]);
        }

        foreach ($metrics as $name) {
            $metricsData[] = new Metric(['name' => $name]);
        }

        return $client->runReport(
            [
                'property' => 'properties/' . $property,
                'dateRanges' => [
                    new DateRange([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]),
                ],
                'dimensions' => $dimensionsData,
                'metrics' => $metricsData,
            ]
        );
    }

    /**
     * @param $propertyId
     * @param $viewId
     * @param $metrics
     * @param $start_date
     * @param $end_date
     * @return GetReportsResponse
     * @throws Exception
     */
    public function getReport($propertyId, $viewId, $metrics, $start_date, $end_date): GetReportsResponse
    {
        $this->reportings = $this->getAuthorizedReportingObject();
        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate($start_date);
        $dateRange->setEndDate($end_date);

        // Create the Metrics Data.
        $metricsData = [];

        foreach ($metrics as $metric){
            $item = new Google_Service_AnalyticsReporting_Metric();
            $item->setExpression($metric['expression']);
            $item->setAlias($metric['alias']);

            $metricsData[] = $item;
        }

        // Create the ReportRequest object.
        $request = new Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($viewId);
        $request->setDateRanges($dateRange);
        $request->setMetrics($metricsData);
        $request->setDimensions(['name' => self::PAGE_DIMENSION]);

        $body = new Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($request));

        return $this->reportings->reports->batchGet( $body );
    }

    /**
     * @throws \Exception
     */
    public function parseUAResults(GetReportsResponse $reports, $date): array
    {
        $keyNames = [
            'conversionRate' => 'ecom_conversion_rate',
            'revenue' => 'revenue',
            'avgOrderValue' => 'avg_order_value',
            'bounceRate' => 'bounce_rate',
        ];

        $result = [];
        foreach ($reports as $report) {
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            foreach ($dimensionHeaders as $key => $dimensionHeader) {
                if ($dimensionHeader === self::PAGE_DIMENSION) {
                    $pageDimensionKey = $key;
                }
            }

            if(!isset($pageDimensionKey)) {
                throw new \Exception('Dimension error');
            }

            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            foreach ($rows as $row) {
                $metrics = $row->getMetrics();
                $dimensions = $row->getDimensions();

                $dimension = $dimensions[$pageDimensionKey];

                if($dimension) {
                    $item = [];
                    foreach ($metrics as $metric) {
                        foreach ($metric->getValues() as $k => $value) {
                            $entry = $metricHeaders[$k];
                            $item[$keyNames[$entry->getName()]] = $value;
                        }
                    }

                    $item['date'] = $date;
                    $result[$dimension] = $item;
                }
            }
        }

        return $result;
    }

    public function formatGAResponse($response, $domain, $date): array
    {
        $result = [];
        $metricHeaders = $response->getMetricHeaders();
        $metricNames = [];

        foreach ($metricHeaders as $metricHeader) {
            $metricNames[] = $metricHeader->getName();
        }

        foreach ($response->getRows() as $row) {
            $dimensionValues = $row->getDimensionValues();
            if(isset($dimensionValues[0]) && $dimensionValues[0] instanceof DimensionValue) {
                $url = rtrim($domain, "/ ") . $dimensionValues[0]->getValue();
                foreach ($row->getMetricValues() as $k => $v) {
                    $result[$url][$metricNames[$k]] = $v->getValue();
                }
                $result[$url]['date'] = $date;
            }
        }

        return $result;
    }

    static function getAccessTokenPath (): string
    {
        return config_path() . DIRECTORY_SEPARATOR . 'GAAccessToken.json';
    }

    static function getRefreshTokenPath (): string
    {
        return config_path() . DIRECTORY_SEPARATOR . 'GARefreshToken.txt';
    }

    static function getAuthCodePath (): string
    {
        return config_path() . DIRECTORY_SEPARATOR . 'GAAuthCode.txt';
    }

    static function getAnalyticCredsPath (): string
    {
        return config_path() . DIRECTORY_SEPARATOR . 'credentials.json';
    }

    static function getGSCOAuthCredsPath (): string
    {
        return config_path() . DIRECTORY_SEPARATOR . 'client_secret_OA.json';
    }

    /**
     * @return Client
     * @throws Exception
     * @throws \Exception
     */
    public function getGSCAuthorizedClient(): Client
    {
        $credentials = self::getGSCOAuthCredsPath();
        if (!file_exists($credentials)) {
            throw new \Exception("Can't find credentials file");
        }

        $client = new Client();
        $client->setAuthConfig($credentials);

        $client->addScope(Webmasters::WEBMASTERS_READONLY);

        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        // Auth Client manually because of bug in Google\Client. Probably unnecessary in future
        return $this->authGoogleClient($client);
    }

    /**
     * @param Client $client
     * @return Client
     * @throws Exception
     */
    public function authGoogleClient(Client $client): Client
    {
        $accessTokenPath = self::getAccessTokenPath();
        if (file_exists($accessTokenPath)) {
            $client->setAccessToken(json_decode(file_get_contents($accessTokenPath), true));
        }

        if ($client->isAccessTokenExpired()) {
            // Refresh the token if possible, else fetch a new one.
            if ($refreshToken = $this->getRefreshToken()) {
                $accessToken = $this->getAccessTokenWithRefreshToken($refreshToken, $client);
            } else {
                if($authCode = $this->getAuthCode()) {
                    $accessToken = $this->fetchAccessTokenWithAuthCode($authCode, $client);
                    if ($res = json_decode($accessToken, true)) {
                        if(isset($res['refresh_token'])) {
                            $refreshTokenPath = self::getRefreshTokenPath();
                            if (!file_exists(dirname($refreshTokenPath))) {
                                mkdir(dirname($refreshTokenPath), 0700, true);
                            }
                            file_put_contents($refreshTokenPath, $res['refresh_token']);
                        }
                    }
                } else {
                    throw new Exception("Can't retrieve Auth code");
                }
            }

            $client->setAccessToken($accessToken);
            $accessToken = $client->getAccessToken();
            $accessToken['created'] = time();

            // Save the token to a file.
            if (!file_exists(dirname($accessTokenPath))) {
                mkdir(dirname($accessTokenPath), 0700, true);
            }
            file_put_contents($accessTokenPath, json_encode($accessToken));
        }

        return $client;
    }

    /**
     * @param string $refreshToken
     * @param Client $client
     * @return bool|string
     */
    protected function getAccessTokenWithRefreshToken(string $refreshToken, Client $client)
    {
        $postFields = 'client_id=' . $client->getClientId();
        $postFields .= '&redirect_uri=' . urlencode($client->getRedirectUri());
        $postFields .= '&client_secret=' . $client->getClientSecret();
        $postFields .= '&grant_type=refresh_token&refresh_token=' . urlencode($refreshToken);

        return $this->sendRawRequest(self::METHOD_POST, self::GOOGLE_OAUTH_TOKEN_URL, $postFields);
    }

    /**
     * @return string
     */
    protected function getRefreshToken(): string
    {
        $refreshTokenPath = self::getRefreshTokenPath();
        if (file_exists($refreshTokenPath)) {
            return file_get_contents($refreshTokenPath);
        }

        return '';
    }

    /**
     * @return string
     */
    protected function getAuthCode(): string
    {
        $authCodePath = self::getAuthCodePath();
        if (file_exists($authCodePath)) {
            return file_get_contents($authCodePath);
        }

        return '';
    }

    /**
     * @param string $authCode
     * @param Client $client
     * @return string
     */
    protected function fetchAccessTokenWithAuthCode(string $authCode, Client $client): string
    {
        $postFields = 'client_id=' . $client->getClientId();
        $postFields .= '&redirect_uri=' . urlencode($client->getRedirectUri());
        $postFields .= '&client_secret=' . $client->getClientSecret();
        $postFields .= '&code=' . $authCode;
        $postFields .= '&grant_type=authorization_code';

        return $this->sendRawRequest(self::METHOD_POST, self::GOOGLE_OAUTH_TOKEN_URL, $postFields);
    }

    /**
     * @param string $method
     * @param string $url
     * @param string $postFields
     * @return bool|string
     */
    protected function sendRawRequest (string $method, string $url, string $postFields = '')
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
//                'Cookie: __Host-GAPS=1:yehutPjWLkKk1sHcXLstjifRWJKo2w:pD8uRL2a_tTy_fh9'
            ),
        ));
        if($method === self::METHOD_POST) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
        }

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    /**
     * @param Project $project
     * @param array $urls
     * @throws Exception
     */
    public function expandGA(Project $project, array $urls)
    {
        $urls = array_unique($urls);
        try {
            if($project->strategy === Project::GA_STRATEGY){
                if($project->ga_property_id) {
                    $dimensions = ['pagePath'];
                    $metrics = ['totalRevenue', 'averagePurchaseRevenue', 'engagementRate', 'conversions', 'sessions'];

                    $period = CarbonPeriod::create(Carbon::now()->subMonth(16)->format('Y-m-d'), Carbon::now());
                    $urlData = [];

                    foreach ($period as $date) {
                        $date = $date->format('Y-m-d');

                        $response = $this->makeGAApiCall($project->ga_property_id, $dimensions, $metrics, $date, $date);
                        $result = $this->formatGAResponse($response, $project->domain, $date);

                        if (!empty($result)) {
                            foreach ($result as $url => $data) {
                                $result[$url]['bounce_rate'] = 1 - $data['engagementRate'];
                                $result[$url]['bounce_rate'] = (string)$result[$url]['bounce_rate'];
                                $result[$url]['ecom_conversion_rate'] = $data['sessions'] == 0 ? 0 : $data['conversions'] / $data['sessions'];
                                $result[$url]['ecom_conversion_rate'] = (string)$result[$url]['ecom_conversion_rate'];

                                if (isset($result[$url]['totalRevenue'])) {
                                    $result[$url]['revenue'] = $result[$url]['totalRevenue'];
                                    unset($result[$url]['totalRevenue']);
                                }

                                if (isset($result[$url]['averagePurchaseRevenue'])) {
                                    $result[$url]['avg_order_value'] = $result[$url]['averagePurchaseRevenue'];
                                    unset($result[$url]['averagePurchaseRevenue']);
                                }

                                unset($result[$url]['engagementRate']);
                                unset($result[$url]['conversions']);
                                unset($result[$url]['sessions']);
                            }

                            $urlData = $this->handleGAResult($urlData, $result, $urls);
                        }
                    }
                }
            } else if($project->strategy === Project::UA_STRATEGY){
                if($project->ua_view_id) {
                    $metrics = [
                        ['expression' => "ga:bounceRate", 'alias' => 'bounceRate'],
                        ['expression' => "ga:transactionsPerSession", 'alias' => 'conversionRate'],
                        ['expression' => "ga:transactionRevenue", 'alias' => 'revenue'],
                        ['expression' => "ga:revenuePerTransaction", 'alias' => 'avgOrderValue'],
                    ];

                    $period = CarbonPeriod::create(Carbon::now()->subMonth(16)->format('Y-m-d'), Carbon::now());
                    $urlData = [];

                    foreach ($period as $date) {
                        $date = $date->format('Y-m-d');
                        $result = $this->parseUAResults($this->getReport($project->ua_property_id, $project->ua_view_id, $metrics, $date, $date), $date);

                        $urlData = $this->handleGAResult($urlData, $result, $urls);
                    }

                }
            }
            if(isset($urlData)) {
                $this->saveGaResults($urlData);
            }
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param $urlData
     * @param $result
     * @param $urls
     * @return array
     */
    protected function handleGAResult ($urlData, $result, $urls): array
    {
        foreach ($result as $url => $urlRow) {
            if (!in_array($url, $urls)) {
                $url2 = $url;

                if (substr($url, -1) === '/') {
                    $url2 = rtrim($url, "/ ");
                } else {
                    $url2 .= '/';
                }

                if (!in_array($url2, $urls)) {
                    unset($result[$url]);
                }
            }
        }

        foreach ($result as $url => $data) {
            if(isset($data['date'])) {
                $urlData[$url][$data['date']] = $data;
            }
        }
        return $urlData;
    }

    /**
     * @param array $result
     * @return void
     */
    protected function saveGaResults(array $result)
    {
        $upsert = [];
        foreach ($result as $k=> $v) {
            $url = URL::whereUrl($k)->first();

            if(!$url) {
                $url = URL::whereUrl($k . '/')->first();
            }

            if(!$url) {
                $url = URL::whereUrl(rtrim($k, "/ "))->first();
            }

            if($url) {
                foreach ($v as $date => $data) {
                    if(empty($data)) {
                        $data = [
                            'ecom_conversion_rate' => 0,
                            'revenue' => 0,
                            'avg_order_value' => 0,
                            'bounce_rate' => 0,
                            'date' => $date,
                        ];
                    }
                    $data['url_id'] = $url->id;
                    $upsert[] = $data;
                }
            }
        }

        UrlData::upsert($upsert, ['url_id', 'date'], ['ecom_conversion_rate', 'revenue', 'avg_order_value', 'bounce_rate']);
    }

    /**
     * @param array $urls
     * @param array $keywords
     * @param Project $project
     * @return array
     * @throws Exception
     */
    public function expandGSC(array $urls, array $keywords, Project $project): array
    {
        try {
            $result = [];

            $client = $this->getGSCAuthorizedClient();

            $serviceWebmasters = new Google_Service_Webmasters( $client );

            if(is_countable($serviceWebmasters->sites->listSites()->getSiteEntry())) {
                foreach ($serviceWebmasters->sites->listSites()->getSiteEntry() as $site) {

                    if($site->getSiteUrl() == $project->domain
                        || $site->getSiteUrl() == $project->domain . '/'
                        || $site->getSiteUrl() == "http://" . $project->domain . '/'
                        || $site->getSiteUrl() == "https://" . $project->domain . '/'
                        || $site->getSiteUrl() == "http://" . $project->domain
                        || $site->getSiteUrl() == "https://" . $project->domain
                    ) {
                        $postBody = new Webmasters\SearchAnalyticsQueryRequest( [
                            'startDate'  => Carbon::now()->subMonth(16)->format('Y-m-d'),
                            'endDate'    => Carbon::now()->format('Y-m-d'),
                            'dimensions' => [
                                'page',   // $row->getKeys()[0]
                                'query',  // $row->getKeys()[1]
                                'date'    // $row->getKeys()[2]
                            ],
                        ] );

                        $searchAnalyticsResponse = $serviceWebmasters->searchanalytics->query($site->getSiteUrl(), $postBody);

                        foreach ($searchAnalyticsResponse->getRows() as $row) {
                            if($row->getKeys() && in_array($row->getKeys()[0], $urls) && in_array($row->getKeys()[1], $keywords)) {
                                $result[$row->getKeys()[0]][$row->getKeys()[1]][$row->getKeys()[2]]['clicks'] = $row->getClicks();
                                $result[$row->getKeys()[0]][$row->getKeys()[1]][$row->getKeys()[2]]['impressions'] = $row->getImpressions();
                                $result[$row->getKeys()[0]][$row->getKeys()[1]][$row->getKeys()[2]]['ctr'] = $row->getCtr();
                                $result[$row->getKeys()[0]][$row->getKeys()[1]][$row->getKeys()[2]]['position'] = $row->getPosition();
                            }
                        }
                    }
                }
            }

            $this->saveGSCResults($result);
        } catch(Throwable $e ) {
            throw new Exception($e->getMessage());
        }

        return $result;
    }

    /**
     * @param array $result
     * @return void
     */
    protected function saveGSCResults(array $result)
    {
        $upsert = [];
        foreach ($result as $site => $item) {
            $url = URL::whereUrl($site)->first();

            if(!$url) {
                $url = URL::whereUrl($site . '/')->first();
            }
            if(!$url) {
                $url = URL::whereUrl(rtrim($site, "/ "))->first();
            }

            if($url) {
                foreach ($item as $keyword => $dates) {
                    $keyword = Keyword::whereKeyword($keyword)->first();
                    if($keyword) {
                        foreach ($dates as $date => $data) {
                            if (empty($data)) {
                                $data = [
                                    'position' => 0,
                                    'clicks' => 0,
                                    'impressions' => 0,
                                    'ctr' => 0,
                                    'date' => $date,
                                ];
                            }

                            $model = UrlKeyword::whereUrlId($url->id)->where('keyword_id', $keyword->id)->first();

                            if($model) {
                                $data['url_keyword_id'] = $model->id;
                                $data['date'] = $date;
                                $upsert[] = $data;
                            }
                        }
                    }
                }
            }
        }

        UrlKeywordData::upsert($upsert, ['url_keyword_id', 'date'], ['position', 'clicks', 'impressions', 'ctr']);
    }
}

