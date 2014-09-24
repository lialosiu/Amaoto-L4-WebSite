@extends('admin.base')

@section('site-body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($thatUser)): ?>
                <form id="admin-edit-user-form" method="post" role="form" data-toggle="validator" action="<?= URL::to('api/edit-user') ?>">
                    <input type="hidden" name="id" value="<?= $thatUser->id ?>">
                    <?php /** @var AmaotoUser $thatUser */ ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">编辑用户信息</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">用户名</label>
                                    <input type="text" class="form-control" name="username" data-minlength="3" maxlength="20" placeholder="请输入用户名" value="<?= $thatUser->username ?>" required>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">Email</label>
                                    <input type="email" class="form-control" name="email" maxlength="50" placeholder="请输入Email地址" value="<?= $thatUser->email ?>">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">用户密码</label>
                                    <input type="password" class="form-control" name="password" data-minlength="8" maxlength="50" placeholder="如不修改，请留空">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">权限等级</label>
                                    <input type="number" class="form-control" name="power" min="-99999" max="99999" placeholder="请输入权限等级" value="<?= $thatUser->power ?>">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="pull-right">
                                <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-ok"></span> 提交修改</button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
@stop
