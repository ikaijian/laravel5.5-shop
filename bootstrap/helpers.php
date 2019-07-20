<?php
/**
 * Created by PhpStorm.
 * User: 陈开坚(jianjian)
 * Date: 2019/7/15
 * Time: 0:23
 */

function route_class()
{
    return str_replace('.', '-',Route::currentRouteName());
}