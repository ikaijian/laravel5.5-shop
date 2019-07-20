<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    //页面展示
    public function root()
    {
        return view('pages.root');
    }

    //用来重定向用户未登录访问系统页面
    public function emailVerifyNotice(Request $request)
    {
        return view('pages.email_verify_notice');

    }

}
