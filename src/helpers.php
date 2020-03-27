<?php

/**
 * Created by PhpStorm.
 * User: wufly
 * Date: 2020/3/3 下午7:24
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

function ding()
{

    if (Config::get('dingding.enabled')) {
        $arguments = func_get_args();

        $ding = new Wufly\Dingding\Dingding();

        if (isset($arguments[0]) && $arguments[0]) {
            // 一段时间有相同提示就不发请求
            $cacheTime = $arguments[1] ?? Config::get('dingding.default_cache_time');
            if (Cache::has($$arguments[0])) {
                return;
            }
            Cache::put($$arguments[0], 1, $cacheTime);
            return $ding->text($arguments[0]);
        }
    }
}
