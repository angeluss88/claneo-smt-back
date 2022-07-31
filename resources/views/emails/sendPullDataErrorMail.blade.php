<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ __('SEO Management Tool: Confirm your email address') }}</title>
    </head>
    <body>
        <p style="text-align: center">
            <img width="300" src="{{ $message->embed(public_path() . '/Claneo_logo_mails.png') }}" alt />
        </p>
        <h2 style="text-align: center; background-color: #98b2d3; padding-top: 20px;">
            {{ __('Data cannot be pulled for') . $details['type'] }}
        </h2>
        <p style="font-weight: bold">
            <span> {{ __('Project(domain)') }}: &nbsp; </span>
            <span> {{ $details['project'] }}</span>
        </p>
        <p style="font-weight: bold">
            <span> {{ __('Date: ') }}: &nbsp; </span>
            <span> {{ $details['date'] }} </span>
        </p>
        <p style="font-weight: bold">
            <span> {{ __('Error message: ') }}: &nbsp; </span>
            <span> {{ $details['error'] }} </span>
        </p>
    </body>
</html>
