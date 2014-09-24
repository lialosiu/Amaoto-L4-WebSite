@extends('admin.base')

@section('site-body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($Users)): ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">用户列表</div>
                    <table class="table table-striped table-valign-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>用户名</th>
                            <th>Email</th>
                            <th>权限</th>
                            <th>注册IP</th>
                            <th>注册时间</th>
                            <th>最后登录IP</th>
                            <th>最后登录时间</th>
                            <th>最后活动IP</th>
                            <th>最后活动时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($Users as $thisUser): ?>
                            <?php /** @var AmaotoUser $thisUser */ ?>
                            <tr>
                                <td><?= $thisUser->id ?></td>
                                <td><?= $thisUser->username ?></td>
                                <td><?= $thisUser->email ?></td>
                                <td><?= $thisUser->power ?></td>
                                <td><?= $thisUser->reg_ip ?></td>
                                <td><?= date('Y-m-d H:i:s', $thisUser->reg_time) ?></td>
                                <td><?= $thisUser->login_ip ?></td>
                                <td><?= date('Y-m-d H:i:s', $thisUser->login_time) ?></td>
                                <td><?= $thisUser->act_ip ?></td>
                                <td><?= date('Y-m-d H:i:s', $thisUser->act_time) ?></td>
                                <td><a class="btn btn-xs btn-default" href="<?= URL::to(sprintf('admin/edit-user/%s', $thisUser->id)) ?>"><span class="glyphicon glyphicon-edit"></span> 编辑</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="panel-footer">
                        <?php echo $Users->links() ?>
                        <div class="pull-right">
                            <div class="btn btn-primary"><span class="glyphicon glyphicon-user"></span> 添加用户</div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
@stop
