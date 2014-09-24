@extends('base-master')

@section('html-head')
@stop

@section('html-body')
<div style="width: 600px; margin: 100px auto 0">
    <div class="login-block">
        <form id="login-form" method="post" role="form" data-toggle="validator" action="<?= URL::to('api/login') ?>">
            <h1 class="text-center text-danger"><?= Config::get('constants.site-name') ?>
                <small> - Amaoto System</small>
            </h1>

            <h2 class="text-center"><b>用户登录</b></h2>

            <hr>

            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">用户名</label>
                    <input type="text" class="form-control" name="username" placeholder="请输入用户名" required="required">
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">密码</label>
                    <input type="password" class="form-control" name="password" placeholder="请输入密码" required="required">
                </div>
                <div class="help-block with-errors"></div>
            </div>

            <hr>

            <div class="text-center">
                <button type="submit" class="btn btn-primary" style="padding: 5px 100px">登录</button>
            </div>
        </form>
    </div>
</div>
@stop