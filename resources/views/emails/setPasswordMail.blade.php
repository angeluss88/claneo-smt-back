<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>{{ __('SEO Management Tool: Confirm your email address') }}</title>
</head>
    <body>
        <p style="text-align: center">
            <img width="300" src="{{ $message->embed(public_path() . '/Claneo_logo.png') }}" alt />
        </p>
        <h2 style="text-align: center; background-color: #98b2d3; padding-top: 20px;">
            {{ __('Welcome to SEO Management Tool') }} 😉
        </h2>
        <p style="text-align: center">{{ __('Hello, ') . $details['firstname'] }} 😉</p>
        <p style="text-align: center">
            {{ __('Account has been created for you. Please find your credentials below') }}:
        </p>
        <p style="text-align: center; font-weight: bold">{{ 'Email: ' . ' ' . $details['email'] }}</p>
        <p style="text-align: center; font-weight: bold">{{ __('Password: ') . ' ' . $details['password'] }}</p>
        <p style="text-align: center">{{ __('Please confirm your email address to activate your account.') }}</p>
        <p style="text-align: center">⬇</p>
        <p style="text-align: center"><a href="{{ $details['url'] }}">{{ __('Confirm email ') }}</a></p>
        <p style="text-align: center">{{ __('Claneo GmbH | Revaler Str. 30 | 10245 Berlin') }}</p>
    </body>
</html>
