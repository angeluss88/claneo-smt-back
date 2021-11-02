<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Exception;
use Google\Service\Analytics\Accounts;
use Google\Service\Analytics\Profile;
use Google\Service\Analytics\Webproperties;
use Google\Service\Analytics\Webproperty;
use Google_Client;
use Google_Service_Analytics;
use Google_Service_Analytics_Profile;
use Google_Service_Analytics_Webproperty;
use Throwable;

class GoogleAnalyticsService
{
    public $analytics;
    protected $key_file = 'service-account-credentials.json';
    protected $beta_analytics_data_credentials = 'ga.json';
    public $scopes = ['https://www.googleapis.com/auth/analytics.edit'];
    public $appName = "Hello Analytics Reporting";
    public $accountId = '211582088';

    /**
     * @throws Exception
     */
    public function __construct ()
    {
        $this->key_file = config_path() . DIRECTORY_SEPARATOR . $this->key_file;
        $this->beta_analytics_data_credentials = config_path() . DIRECTORY_SEPARATOR . $this->beta_analytics_data_credentials;

        $this->analytics = $this->getAuthorizedAnalyticsObject();
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
        return $this->analytics->management_webproperties->listManagementWebproperties($this->accountId);
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
     * @param $propertyName
     * @return null|Webproperty
     */
    public function insertProperty($propertyName): ?Webproperty
    {
        try {
            $property = new Google_Service_Analytics_Webproperty();
            $property->setName($propertyName);
            $property->setSelfLink($propertyName);
            $property->setWebsiteUrl($propertyName);

            return $this->analytics->management_webproperties->insert($this->accountId, $property);
        } catch (Throwable $e) {
            print 'There was a general API error '
                . $e->getCode() . ':' . $e->getMessage();
        }

        return null;
    }

    /**
     * @param $propertyId
     * @param $viewName
     * @return Profile|null
     */
    public function insertView ($propertyId, $viewName, $ecTracking = true): ?Profile
    {
        $profile = new Google_Service_Analytics_Profile();
        $profile->setName($viewName);
        $profile->setECommerceTracking($ecTracking);

        try {
            return $this->analytics->management_profiles->insert($this->accountId, $propertyId, $profile);
        } catch (Throwable $e) {
            print 'There was a general API error '
                . $e->getCode() . ':' . $e->getMessage();
        }
        return null;
    }

    /**
     * @param Webproperty $property
     * @param array $dimensions
     * @param array $metrics
     * @param string $startDate
     * @param string $endDate
     * @return RunReportResponse
     * @throws ApiException
     * @throws ValidationException
     */
    public function makeGAApiCall (Webproperty $property, array $dimensions, array $metrics, string $startDate, string $endDate = 'today'): RunReportResponse
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
                'property' => 'properties/' . $property->getInternalWebPropertyId(), // @TODO investigate and replace with the right property ID
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
}
