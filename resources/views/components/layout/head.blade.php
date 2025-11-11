<!doctype html>
<html lang="de" class="h-full bg-white scroll-smooth overflow-y-scroll">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ env('APP_NAME') }}</title>
<meta name="description" content="{{ env('APP_DESCRIPTION') }}">
<meta property="og:title" content="{{ env('APP_NAME') }}">
<meta property="og:description" content="{{ env('APP_DESCRIPTION') }}">
<meta property="og:url" content="{{ url()->current()}} ">
<meta property="og:site_name" content="{{ env('APP_NAME') }}">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#021e24">
@vite('resources/css/app.css')
@livewireStyles
</head>
