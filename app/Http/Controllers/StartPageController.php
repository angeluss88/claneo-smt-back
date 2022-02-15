<?php

namespace App\Http\Controllers;

use App\Services\GoogleAnalyticsService;
use Google\Service\Webmasters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Google\Client;
use Illuminate\Http\UploadedFile;

class StartPageController extends Controller
{
    public function index()
    {
        $data = [];

        $data['analytics_creds_exists'] = file_exists(GoogleAnalyticsService::getAnalyticCredsPath());
        $data['gsc_oauth_creds_exists'] = file_exists(GoogleAnalyticsService::getGSCOAuthCredsPath());

        if($data['gsc_oauth_creds_exists']) {
            $client = new Client();
            $client->setAuthConfig(GoogleAnalyticsService::getGSCOAuthCredsPath());
            $client->addScope(Webmasters::WEBMASTERS_READONLY);
            $client->setAccessType('offline');
            $client->setPrompt('consent');
            $data['gsc_auth_url'] = $client->createAuthUrl();
        }

        $data['gsc_auth_code_exists'] = file_exists(GoogleAnalyticsService::getAuthCodePath());
        $data['gsc_refresh_token_exists'] = file_exists(GoogleAnalyticsService::getRefreshTokenPath());
        $data['gsc_access_token_exists'] = file_exists(GoogleAnalyticsService::getAccessTokenPath());

        return view('startPage', $data);
    }

    public function handle(Request $request): RedirectResponse
    {
        $fields = $request->validate([
            'analyticsCreds' => 'file',
            'gscCreds' => 'file',
            'accessToken' => 'file',
            'AuthCode' => 'string|nullable',
            'refreshToken' => 'string|nullable',
        ]);

        if($request->analyticsCreds instanceof UploadedFile ){
            $fileName = GoogleAnalyticsService::getAnalyticCredsPath();
            $dir = explode(DIRECTORY_SEPARATOR, $fileName);
            $fileName = array_pop($dir);
            $dir = implode(DIRECTORY_SEPARATOR, $dir);
            $request->analyticsCreds->move($dir, $fileName);
        }

        if($request->gscCreds instanceof UploadedFile ){
            $fileName = GoogleAnalyticsService::getGSCOAuthCredsPath();
            $dir = explode(DIRECTORY_SEPARATOR, $fileName);
            $fileName = array_pop($dir);
            $dir = implode(DIRECTORY_SEPARATOR, $dir);
            $request->gscCreds->move($dir, $fileName);
        }

        if($request->accessToken instanceof UploadedFile ){
            $fileName = GoogleAnalyticsService::getAccessTokenPath();
            $dir = explode(DIRECTORY_SEPARATOR, $fileName);
            $fileName = array_pop($dir);
            $dir = implode(DIRECTORY_SEPARATOR, $dir);
            $request->accessToken->move($dir, $fileName);
        }

        if($request->AuthCode){
            $authCodePath = GoogleAnalyticsService::getAuthCodePath();
            if (!file_exists(dirname($authCodePath))) {
                mkdir(dirname($authCodePath), 0700, true);
            }
            file_put_contents($authCodePath, $request->AuthCode);
        }

        if($request->refreshToken){
            $refreshTokenPath = GoogleAnalyticsService::getRefreshTokenPath();
            if (!file_exists(dirname($refreshTokenPath))) {
                mkdir(dirname($refreshTokenPath), 0700, true);
            }

            file_put_contents($refreshTokenPath, $request->refreshToken);
        }

        return redirect()->back()->with(['success' => 'Operation Successfully']);
    }
}
