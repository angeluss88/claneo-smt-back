<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Start Your App</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
            .green_colored {
                color: green;
            }
            .red_colored {
                color: red;
            }
            a {
                color: #0969da;
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
    <form name="startAppData" method="POST">
        @csrf
        <div class="credentials_block">
            <p class="creds_block_label">
                <label for="analyticsCreds">
                    Upload GA Service account credentials json-file (
                    <span class="{{ $analytics_creds_exists ? 'green_colored' : 'red_colored' }}">
                        {{ $analytics_creds_exists ? 'Exists' : 'Does not exists' }}
                    </span>
                    )
                </label>
            </p>
            <p class="creds_block_input">
                <input id="analyticsCreds" name="analyticsCreds" type="file" accept="application/json" />
            </p>
        </div>
        <div class="credentials_block">
            <p class="creds_block_label">
                <label for="gscCreds">
                    Upload GCS Oauth account credentials json-file (
                    <span class="{{ $gsc_oauth_creds_exists ? 'green_colored' : 'red_colored' }}">
                        {{ $gsc_oauth_creds_exists ? 'Exists' : 'Does not exists' }}
                    </span>
                    )
                </label>
            </p>
            <p class="creds_block_input">
                <input id="gscCreds" name="gscCreds" type="file" accept="application/json" />
            </p>
        </div>
        @if (isset($gsc_auth_url))
            <div class="credentials_block">
                <p>
                    Click this link and follow instructions to auth your env
                    (
                    <span class="{{ $gsc_oauth_creds_exists ? 'green_colored' : 'red_colored' }}">
                        {{ $gsc_oauth_creds_exists ? 'Already Done' : 'Not done yet' }}
                    </span>
                    )
                    :
                </p>
                <p><a href="{{ $gsc_auth_url }}" target="_blank" >Click Me</a></p>
            </div>
        @endif
        <div class="credentials_block">
            <p class="creds_block_label">
                <label for="authCode">
                    Put Your Google Search Console Authorization Code here (
                    <span class="{{ $analytics_creds_exists ? 'green_colored' : 'red_colored' }}">
                        {{ $gsc_auth_code_exists ? 'Exists' : 'Does not exists' }}
                    </span>
                    )
                </label>
            </p>
            <p class="creds_block_input">
                <input id="authCode" name="AuthCode" type="text" />
            </p>
        </div>
        <HR />
        <p> You also could upload Refresh and Access tokens directly, if there is any issue with getting them programmatically</p>
        <p> Please, be sure you understand what you do!</p>

        <div class="credentials_block">
            <p class="creds_block_label">
                <label for="refreshToken">
                    Put Your Google search console Refresh Token here (
                    <span class="{{ $gsc_refresh_token_exists ? 'green_colored' : 'red_colored' }}">
                        {{ $gsc_refresh_token_exists ? 'Exists' : 'Does not exists' }}
                    </span>
                    )
                </label>
            </p>
            <p class="creds_block_input">
                <input id="refreshToken" name="refreshToken" type="text" />
            </p>
        </div>
        <div class="credentials_block">
            <p class="creds_block_label">
                <label for="accessToken">
                    Upload Google search console Access Token  (
                    <span class="{{ $analytics_creds_exists ? 'green_colored' : 'red_colored' }}">
                        {{ $analytics_creds_exists ? 'Exists' : 'Does not exists' }}
                    </span>
                    )
                </label>
            </p>
            <p class="creds_block_input">
                <input id="accessToken" name="accessToken" type="file" accept="application/json" />
            </p>
        </div>
        <div class="submit_block">
            <button>Submit</button>
        </div>
    </form>
    </body>
</html>
