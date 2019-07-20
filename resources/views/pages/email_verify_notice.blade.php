@extends('layouts.app')
@section('title', '未登录提示')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">错误提示</div>
        <div class="panel-body text-center">
            <h1>请先验证邮箱</h1>
            <a class="btn btn-primary" href="{{ route('root') }}">返回首页</a>
        </div>
    </div>
@endsection

