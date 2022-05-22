<?php

namespace App\Http\Controllers;

use App\Services\GoogleAnalyticsService;
use Google\Exception;
use Google\Service\Webmasters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Google\Client;
use Illuminate\Http\UploadedFile;

class StartPageController extends Controller
{
    /**
     * @throws Exception
     */
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

    public function welcome(Request $request)
    {
        $request->validate([
            'code' => 'string|nullable',
        ]);
        $data = [];
        if(isset($request->code)) {
//            $authCodePath = GoogleAnalyticsService::getAuthCodePath();
//            if (!file_exists(dirname($authCodePath))) {
//                mkdir(dirname($authCodePath), 0700, true);
//            }
//            file_put_contents($authCodePath, $request->code);
            $data['code'] = $request->code;
        }

        return view('welcome', $data);
    }

    public function handleWelcome (Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'string|nullable',
        ]);

        if(isset($request->code)) {
            $authCodePath = GoogleAnalyticsService::getAuthCodePath();
            if (!file_exists(dirname($authCodePath))) {
                mkdir(dirname($authCodePath), 0700, true);
            }
            file_put_contents($authCodePath, $request->code);
            if(is_file(GoogleAnalyticsService::getRefreshTokenPath())) {
                unlink(GoogleAnalyticsService::getRefreshTokenPath());
            }
            if(is_file(GoogleAnalyticsService::getAccessTokenPath())) {
                unlink(GoogleAnalyticsService::getAccessTokenPath());
            }
        }
        return redirect('/')->with(['success' => 'Operation Successfully. Now You can close this window and try Expand GSC Data']);
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
