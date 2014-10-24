@extends('admin.base')

@section('site-body')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($thatMusic)): ?>
                <?php /** @var AmaotoMusic $thatMusic */ ?>
                <form id="admin-edit-music-form" method="post" role="form" data-toggle="validator" action="<?= URL::to('api/edit-music') ?>">
                    <input type="hidden" name="music-id" value="<?= $thatMusic->id ?>">
                    <?php /** @var AmaotoUser $thatUser */ ?>
                    <div class="panel panel-primary">
                        <div class="panel-heading">编辑音乐信息</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">标题</label>
                                    <input type="text" class="form-control" name="title" maxlength="50" placeholder="请输入音乐标题" value="<?= $thatMusic->title ?>" required>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">艺术家</label>
                                    <input type="text" class="form-control" name="artist" maxlength="50" placeholder="请输入艺术家" value="<?= $thatMusic->artist ?>">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">发行年份</label>
                                    <input type="text" class="form-control" name="year" maxlength="10" placeholder="请输入发行年份" value="<?= $thatMusic->year ?>">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">流派</label>
                                    <input type="text" class="form-control" name="genre" maxlength="20" placeholder="请输入流派" value="<?= $thatMusic->genre ?>">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <label class="input-group-addon">音轨号</label>
                                    <input type="number" class="form-control" name="track" data-minlength="0" maxlength="100" placeholder="请输入音轨号" value="<?= $thatMusic->track ?>">
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
