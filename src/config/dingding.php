<?php

/**
 * Created by PhpStorm.
 * User: wufly
 * Date: 2020/3/3 下午3:12
 */

return [
    //DingDing access token
    'access_token' => env('DINGDING_TOKEN', ''),
    'secret'       => env('DINGDING_SECRET', ''),
    'prefix'       => env('DINGDING_PREFIX', ''),
    'enabled'       => env('DINGDING_ENABLED', true),
    'default_cache_time'       => env('DINGDING_CACHE_TIME', 60),
];
