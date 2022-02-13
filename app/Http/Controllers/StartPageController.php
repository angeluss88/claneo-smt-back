<?php

namespace App\Http\Controllers;

use App\Services\GoogleAnalyticsService;
use Google\Service\Webmasters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Google\Client;

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
            $client->setPrompt('select_account consent');
            $data['gsc_auth_url'] = $client->createAuthUrl();
        }

        $data['gsc_auth_code_exists'] = file_exists(GoogleAnalyticsService::getAuthCodePath());
        $data['gsc_refresh_token_exists'] = file_exists(GoogleAnalyticsService::getRefreshTokenPath());
        $data['gsc_access_token_exists'] = file_exists(GoogleAnalyticsService::getAccessTokenPath());

        return view('startPage', $data);
    }

    public function handle(Request $request): RedirectResponse
    {
        dd($request->request);

        return redirect()->back();
    }
}
