<?php

/**
 * Created by PhpStorm.
 * User: wufly
 * Date: 2020/3/3 下午7:24
 */

use Illuminate\Support\Facades\Config;

function ding()
{

    if (Config::get('dingding.enabled')) {
        $arguments = func_get_args();

        $ding = new Wufly\Dingding\Dingding();

        if (isset($arguments[0]) && $arguments[0]) {
            return $ding->text($arguments[0], $arguments[1] ?? null);
        }
    }
}

function toSampleString($arguments)
{
    if (is_array($arguments)) {
        return json_encode($arguments);
    }
    return $arguments;
}

function dingAt($at = null)
{
    $ding = new Wufly\Dingding\Dingding();
    $ding->setAt($at);
    return $ding;
}
