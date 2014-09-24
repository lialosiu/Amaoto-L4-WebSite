@extends('admin.base')

@section('html-head')
@stop

@section('site-body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <form id="admin-edit-option-form" method="post" role="form" data-toggle="validator" action="<?= URL::to('api/edit-option') ?>">
                <div class="panel panel-primary">
                    <div class="panel-heading">站点设置</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="input-group">
                                <label class="input-group-addon">站点名</label>
                                <input type="text" class="form-control" name="site-name" maxlength="20" placeholder="请输入站点名" value="<?= Config::get('constants.site-name') ?>" required>
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label class="input-group-addon">版权名</label>
                                <input type="text" class="form-control" name="copyright-name" placeholder="请输入版权名" value="<?= Config::get('constants.copyright-name') ?>">
                            </div>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <label class="input-group-addon">版权起始年份</label>
                                <input type="text" class="form-control" name="copyright-first-year" pattern="^[0-9]{4}$" data-error="请输入4位数的有效年份。" placeholder="请输入版权起始年份" value="<?= Config::get('constants.copyright-first-year') ?>">
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
        </div>
    </div>
</div>
@stop
