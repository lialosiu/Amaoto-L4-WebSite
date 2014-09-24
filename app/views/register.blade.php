@extends('base-master')

@section('html-head')
@stop

@section('html-body')
<div style="width: 600px; margin: 100px auto 0">
    <div class="register-block">
        <form id="register-form" method="post" role="form" data-toggle="validator" action="<?= URL::to('api/register') ?>">
            <h1 class="text-center text-danger"><?= Config::get('constants.site-name') ?>
                <small> - Amaoto System</small>
            </h1>

            <h2 class="text-center"><b>用户注册</b></h2>

            <hr>

            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">用户名</label>
                    <input type="text" class="form-control" name="username" data-minlength="3" maxlength="20" placeholder="请输入用户名" required>
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">密码</label>
                    <input type="password" class="form-control" name="password" data-minlength="8" maxlength="30" placeholder="请输入密码" required>
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">重复密码</label>
                    <input type="password" class="form-control" data-match="input[name=password]" data-match-error="两次输入的密码不匹配" placeholder="请再次输入密码" required>
                </div>
                <div class="help-block with-errors"></div>
            </div>

            <hr>

            <div class="text-center">
                <button type="submit" class="btn btn-primary" style="padding: 5px 100px">注册</button>
            </div>
        </form>
    </div>
</div>
@stop