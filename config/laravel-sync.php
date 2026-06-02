<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sync Token
    |--------------------------------------------------------------------------
    |
    | A shared secret used to authenticate sync requests. Set this in your
    | .env file. The same token must be configured on both the remote
    | (serving) and local (pulling) environments.
    |
    | This endpoint can stream your entire database — keep the token secret
    | and strong, and consider locking it down with the IP whitelist below.
    |
    */

    'token' => env('LARAVEL_SYNC_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Remote URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the remote environment to pull from. Only needed on the
    | local/pulling side.
    |
    | Example: https://example.com
    |
    */

    'remote' => env('LARAVEL_SYNC_REMOTE', ''),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | The Laravel database connection to dump (remote) and import into (local).
    | Leave null to use the default connection. Only MySQL / MariaDB
    | connections are supported.
    |
    */

    'connection' => env('LARAVEL_SYNC_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | The asset directories to sync, keyed by a short name. Paths are relative
    | to the project root. Add as many entries as you like — e.g. limit the
    | sync to specific sub-folders of storage/app/public.
    |
    */

    'paths' => [
        'assets' => 'storage/app/public',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | The URL prefix for the sync API endpoints on the serving side.
    |
    */

    'route_prefix' => '_sync',

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Optional list of allowed IP addresses. Leave empty to allow any IP
    | (token auth still required).
    |
    */

    'allowed_ips' => [],

];
