<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\DimensionValue;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Exception;
use Google\Service\Analytics\Accounts;
use Google\Service\Analytics\Webproperties;
use Google\Service\Analytics\Webproperty;
use Google\Service\AnalyticsReporting\GetReportsResponse;
use Google_Client;
use Google_Service_Analytics;
use Google_Service_AnalyticsReporting;
use Google_Service_AnalyticsReporting_DateRange;
use Google_Service_AnalyticsReporting_GetReportsRequest;
use Google_Service_AnalyticsReporting_Metric;
use Google_Service_AnalyticsReporting_ReportRequest;

class GoogleAnalyticsService
{
    public $analytics;
    public $reportings;
    protected $key_file = 'credentials.json';
    protected $beta_analytics_data_credentials = 'credentials.json';
    public $scopes = ['https://www.googleapis.com/auth/analytics.readonly'];
    public $appName = "Hello Analytics Reporting";
    public $accountId = '109167922';
    const SERVICE_ACCOUNT = 'starting-account-bfkqmhlvx8j0';
    const PAGE_DIMENSION = 'ga:pagePath';

    /**
     * @throws Exception
     */
    public function __construct ()
    {
        $this->key_file = config_path() . DIRECTORY_SEPARATOR . $this->key_file;
        $this->beta_analytics_data_credentials = config_path() . DIRECTORY_SEPARATOR . $this->beta_analytics_data_credentials;

        $this->analytics = $this->getAuthorizedAnalyticsObject();
        $this->reportings = $this->getAuthorizedReportingObject();
    }

    /**
     * @return Google_Service_Analytics
     * @throws Exception
     */
    protected function getAuthorizedAnalyticsObject (): Google_Service_Analytics
    {
        $client = new Google_Client();
        $client->setApplicationName($this->appName);
        $client->setAuthConfig($this->key_file);
        $client->setScopes($this->scopes);

        return new Google_Service_Analytics($client);
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
     * @return Accounts
     */
    public function getAllGAAccounts (): Accounts
    {
        return $this->analytics->management_accounts->listManagementAccounts();  //var_dump($accounts->getItems());
    }

    /**
     * @return Webproperties
     */
    public function getAllGAProperties (): Webproperties
    {
        return $this->analytics->management_webproperties->listManagementWebproperties('~all');
    }

    /**
     * @param $propertyName
     * @return Webproperty|null
     */
    public function getPropertyByName($propertyName): ?Webproperty
    {
        $properties = $this->getAllGAProperties();

        foreach ($properties->getItems() as $property) {
            if($property->getName() == $propertyName){
                return $property;
            }
        }

        return null;
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
     * @return GetReportsResponse
     */
    public function getReport($propertyId, $viewId, $metrics): GetReportsResponse
    {
        // Create the DateRange object.
        $dateRange = new Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate(Carbon::now()->subMonth(16)->format('Y-m-d'));
        $dateRange->setEndDate("today");

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
    public function parseUAResults(GetReportsResponse $reports): array
    {
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
                            $item[$entry->getName()] = $value;
                        }
                    }

                    $result[$dimension] = $item;
                }
            }
        }

        return $result;
    }

    public function formatGAResponse($response, $domain): array
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
                foreach ($row->getMetricValues() as $k => $v) {
                    $url = rtrim($domain, "/ ") . $dimensionValues[0]->getValue();
                    $result[$url][$metricNames[$k]] = $v->getValue();
                }
            }
        }

        return $result;
    }
}

