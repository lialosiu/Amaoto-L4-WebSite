@extends('base-master')

@section('html-head')
@stop

@section('html-body')
<div style="width: 600px; margin: 100px auto 0">
    <div class="install-block">
        <form method="post" role="form" data-toggle="validator" action="<?= URL::to('api/install') ?>" data-success-href="<?= URL::to('/') ?>">
            <h1 class="text-center text-danger">Amaoto System</h1>

            <h2 class="text-center"><b>初始化向导</b></h2>

            <hr>

            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">站点名</label>
                    <input type="text" class="form-control" name="site-name" placeholder="请输入站点名" required="required">
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">用户名</label>
                    <input type="text" class="form-control" name="username" placeholder="请输入初始用户名" required="required">
                </div>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <label class="input-group-addon">初始密码</label>
                    <input type="password" class="form-control" name="password" placeholder="请输入管理员初始密码" required="required">
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