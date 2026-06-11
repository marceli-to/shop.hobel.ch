@props([
  'title' => null,
  'description' => null,
  'ogImage' => null,
  'ogType' => 'website',
])

@php
$appName = config('app.name', 'Hobel AG');
$pageTitle = filled($title) ? "{$title} – {$appName}" : $appName;
$metaDescription = filled($description) ? $description : config('shop.meta.description');
$ogImageUrl = filled($ogImage) ? $ogImage : url(config('shop.meta.og_image'));
@endphp

<!doctype html>
<html lang="de" class="h-full bg-white scroll-smooth overflow-y-scroll">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:site_name" content="{{ $appName }}">
@if ($ogImageUrl)
<meta property="og:image" content="{{ $ogImageUrl }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if ($ogImageUrl)
<meta name="twitter:image" content="{{ $ogImageUrl }}">
@endif
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="{{ $appName }}" />
<link rel="manifest" href="/site.webmanifest" />
@vite('resources/css/app.css')
@livewireStyles
</head>