<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ __('SEO Management Tool: Forgot password?') }}</title>
</head>
    <body>
        <p style="text-align: center">
            <img width="300" src="{{ $message->embed(public_path() . '/Claneo_logo_mails.png') }}" alt />
        </p>
        <h2 style="text-align: center; background-color: #98b2d3; padding-top: 20px;">
            {{ __('Forgot password') }}?
        </h2>
        <p style="text-align: center">{{ __('Hello, ') . $details['firstname'] }} 😉</p>
        <p style="text-align: center">
            {{ __('We received a request to change the password for the following account') }}:
        </p>
        <p style="text-align: center; font-weight: bold">{{ $details['email'] }}</p>
        <p style="text-align: center">{{ __('Please click below to reset your password') }}.</p>
        <p style="text-align: center">⬇</p>
        <p style="text-align: center"><a href="{{ $details['url'] }}">{{ __('Reset password ') }}</a></p>
        <p style="text-align: center">{{ __('NOTE: the link is valid until') }}&nbsp; {{ $details['valid_until'] }}</p>
        <p style="text-align: center">{{ __('Claneo GmbH | Revaler Str. 30 | 10245 Berlin') }}</p>
    </body>
</html>
