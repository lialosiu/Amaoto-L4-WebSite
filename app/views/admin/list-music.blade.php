@extends('admin.base')

@section('html-head')
<script type="text/html" id="before-upload-file-li">
    <li id="before-upload-file-#ID#" class="list-group-item list-group-item-warning" style="display: none">
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-info" role="progressbar" style="width: 0;"></div>
        </div>
        <div class="row">
            <div class="col-sm-6"><span class="text-danger">#FileName#</span></div>
            <div class="col-sm-4"><span class="text-primary status">#Status#</span></div>
            <div class="col-sm-2"><a class="btn btn-danger btn-xs remove-li" data-before-upload-file-id="#ID#" href="javascript:"><span class="glyphicon glyphicon-remove"></span> 移除</a></div>
        </div>
    </li>
</script>
<script type="text/html" id="amaoto-music-li">
    <li id="amaoto-music-#ID#" class="list-group-item">
        <div class="row">
            <div class="visible-sm visible-md visible-lg">
                <div class="col-sm-1"><span class="text-success">#ID#</span></div>
                <div class="col-sm-1"><span class="text-warning">#Track#</span></div>
                <div class="col-sm-4"><span class="text-danger">#Title#</span></div>
                <div class="col-sm-3"><span class="text-info">#Artist#</span></div>
                <div class="col-sm-1"><span class="text-primary">#PlayTime#</span></div>
                <div class="col-sm-2">
                    <a class="btn btn-xs btn-default" href="<?= URL::to('play-music') ?>/#ID#" target="_blank"><span class="glyphicon glyphicon-play"></span></a>
                    <button class="btn btn-xs btn-default"><span class="glyphicon glyphicon-edit"></span></button>
                </div>
            </div>
            <div class="visible-xs">
                <div class="col-xs-6">
                    <div><span class="text-danger" style="font-size: 1.2em">#Title#</span></div>
                    <div><span class="text-info">#Artist#</span></div>
                </div>
                <div class="col-xs-6">
                    <div class="col-xs-4"><span class="text-success">#Track#</span></div>
                    <div class="col-xs-4"><span class="text-warning">#Year#</span></div>
                    <div class="col-xs-4"><span class="text-primary">#PlayTime#</span></div>
                    <div class="col-xs-12">
                        <a class="btn btn-xs btn-success" href="<?= URL::to('play-music') ?>/#ID#" target="_blank"><span class="glyphicon glyphicon-play"></span> 播放</a>
                        <button class="btn btn-xs btn-default"><span class="glyphicon glyphicon-edit"></span> 编辑</button>
                    </div>
                </div>
            </div>
        </div>
    </li>
</script>
<script type="text/html" id="amaoto-file-li">
    <li id="amaoto-file-#ID#" class="list-group-item">
        <div class="row">
            <div class="col-sm-6"><span class="text-danger">#FileName#</span></div>
            <div class="col-sm-3"><span class="text-success">#Type#</span></div>
            <div class="col-sm-3"><span class="text-info">#Size#</span></div>
        </div>
    </li>
</script>
<script>
$(document).ready(function () {
    var localMd5CheckFlag = false;

    var $modal = $('#upload-music-modal');
    var $modalTitle = $modal.find('.modal-title');
    var $musicListGroup = $modal.find('.music-list-group');

    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'btn-pick-music-file',
        chunk_size: '2mb',
        max_file_size: '50mb',
        url: '<?= URL::to('api/upload-music') ?>',
        multipart_params: {},
        multi_selection: true,
        unique_names: true,
        flash_swf_url: '<?= URL::to('packages/plupload-2.1.1/Moxie.swf') ?>',
        silverlight_xap_url: '<?= URL::to('packages/plupload-2.1.1/Moxie.xap') ?>',
        filters: {
            mime_types: [
                { title: "所有支持的文件", extensions: "mp3,m4a" },
                { title: "音频文件", extensions: "mp3,m4a" }
            ],
            max_file_size: "50mb",
            prevent_duplicates: false
        },
        init: {
            FilesAdded: function (up, files) {
                files.forEach(function (file) {
                    $musicListGroup.append(textFormatter($('#before-upload-file-li').html(), {'ID': file.id, 'FileName': file.name, 'Status': '等待处理中'}));
                    $('#before-upload-file-' + file.id).slideDown(function () {
                        // 本地计算文件MD5值
                        if (FileReader && localMd5CheckFlag) {
                            files.forEach(function (file) {
                                var $thisBlock = $('#before-upload-file-' + file.id);
                                $thisBlock.find('.status').html('正在计算MD5指纹...');
                                $thisBlock.find('.progress-bar').removeClass().addClass('progress-bar progress-bar-warning');
                                $thisBlock.find('.progress-bar').css('width', '100%');
                                var fileReader = new FileReader();
                                fileReader.readAsArrayBuffer(file.getNative());
                                fileReader.onload = function (e) {
                                    var md5 = SparkMD5.ArrayBuffer.hash(e.target.result);
                                    var $thisStatus = $('#before-upload-file-' + file.id).find('.status');
                                    $thisStatus.html('MD5: ' + md5);
                                    $.getJSON('<?= URL::to('api/check-file-md5-is-exist')?>', {md5: md5}, function (rsp) {
                                        try {
                                            $thisStatus.html(rsp.message);
                                            $thisBlock.find('.progress').removeClass('progress-striped active');
                                            $thisBlock.find('.progress-bar').removeClass().addClass('progress-bar');
                                            $thisBlock.find('.progress-bar').css('width', '0%');
                                        } catch (ex) {
                                            noty({type: 'error', text: '发生内部错误，请联系管理员'});
                                            throw(ex);
                                        }
                                    });
                                };
                            });
                        }
                    });
                });
            },

            BeforeUpload: function (up, file) {
                up.settings.multipart_params.file_ori_name = file.name;
            },

            UploadProgress: function (up, file) {
                var $thisBlock = $('#before-upload-file-' + file.id);
                $thisBlock.find('.status').html('上传中...(' + file.percent + '%)');
                $thisBlock.find('.progress-bar').css('width', file.percent + '%');
            },

            Error: function (up, err) {
                var $thisBlock = $('#before-upload-file-' + file.id);
                $thisBlock.find('.status').html('上传发生错误: ' + err.message);
                $thisBlock.find('.progress').removeClass('progress-striped active');
                $thisBlock.find('.progress-bar').removeClass().addClass('progress-bar progress-danger');
                $thisBlock.find('.progress-bar').css('width', '100%');
            },

            FileUploaded: function (up, file, info) {
                var $thisBlock = $('#before-upload-file-' + file.id);
                try {
                    var rsp = $.parseJSON(info.response);
                    $thisBlock.find('.status').html(rsp.message);
                    $thisBlock.find('.progress').removeClass('progress-striped active');
                    switch (rsp.type) {
                        case 'success':
                            $thisBlock.find('.progress-bar').removeClass().addClass('progress-bar progress-bar-success');
                            var thisFile = rsp.data.file;
                            var pType = '';
                            var sType = '';
                            if (thisFile) {
                                var typeArray = thisFile.type.split("/");
                                if (typeArray.length >= 2) {
                                    pType = typeArray[0];
                                    sType = typeArray[1];
                                }
                            }
                            var thisMusic = rsp.data.music;
                            switch (pType) {
                                case 'audio':
                                    if (thisMusic) {
                                        $thisBlock.replaceWith(textFormatter($('#amaoto-music-li').html(), {'ID': thisMusic.id, 'Title': thisMusic.title, 'Artist': thisMusic.artist, 'Year': thisMusic.year, 'Genre': thisMusic.genre, 'Track': thisMusic.track, 'PlayTime': thisMusic.playtime_string, 'MimeType': thisMusic.mime_type}));
                                    }
                                    break;
                                default :
                                    if (thisFile) {
                                        $thisBlock.replaceWith(textFormatter($('#amaoto-file-li').html(), {'ID': thisFile.id, 'FileName': thisFile.name, 'Type': thisFile.type, 'Size': thisFile.size}));
                                    }
                            }
                            break;
                        default:
                            $thisBlock.find('.progress-bar').removeClass().addClass('progress-bar progress-bar-danger');
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw(ex);
                }
            }
        }
    });

    $(document).on('click', '.remove-li', function () {
        var beforeUploadFileId = $(this).data('before-upload-file-id');
        uploader.removeFile(beforeUploadFileId);
        $('#before-upload-file-' + beforeUploadFileId).slideUp(function () {
            $(this).remove()
        });
    });

    $(document).on('click', '#btn-start-upload-music', function () {
        uploader.start();
    });

    $(document).on('click', '#btn-switch-local-md5-check', function () {
        localMd5CheckFlag = !localMd5CheckFlag;
        if (localMd5CheckFlag) {
            $(this).removeClass('btn-danger').addClass('btn-info active');
        } else {
            $(this).removeClass('btn-info active').addClass('btn-danger');
        }
    });

    $(document).on('click', '.btn-delete-music', function () {
        var musicId = $(this).data('music-id');
        noty({
            text: '确定要删除该歌曲吗？',
            type: 'warning',
            buttons: [
                {addClass: 'btn btn-danger', text: '删除', onClick: function ($noty) {
                    $noty.close();
                    $.ajax({
                        url: '<?= URL::to('api/delete-music')?>',
                        type: 'post',
                        data: {id: musicId},
                        success: function (raw) {
                            try {
                                var rsp = $.parseJSON(raw);
                                noty({type: rsp.type, text: rsp.message, callback: {
                                    afterClose: function () {
                                        switch (rsp.type) {
                                            case 'success':
                                                location.reload();
                                                break;
                                            default:
                                                break;
                                        }
                                    }
                                }});
                            } catch (ex) {
                                noty({type: 'error', text: '发生内部错误，请联系管理员'});
                                throw(ex);
                            }
                        },
                        error: function (event) {
                            console.error(event);
                            noty({type: 'error', text: event.status + ' - ' + event.statusText});
                        }
                    });
                }
                },
                {addClass: 'btn btn-default', text: '取消', onClick: function ($noty) {
                    $noty.close();
                }
                }
            ]
        });
    });

    $modal.on('hide.bs.modal', function () {
        if (uploader.state != plupload.STOPPED) {
            e.preventDefault();
            return;
        }
        uploader.splice();
        location.reload();
    });

    uploader.init();
});
</script>
@stop

@section('site-body')
<div class="modal fade" id="upload-music-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">上传音乐</h4>
            </div>
            <div class="modal-body">
                <ul class="list-group music-list-group">
                </ul>
            </div>
            <div class="modal-footer">
                <button id="btn-switch-local-md5-check" type="button" class="btn btn-danger"><span class="fa fa-bolt"></span> 本地MD5</button>
                <button id="btn-pick-music-file" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-file"></span> 选择文件</button>
                <button id="btn-start-upload-music" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-upload"></span> 开始上传</button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($Musics)): ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">音乐列表</div>
                    <table class="table table-striped table-valign-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>标题</th>
                            <th>艺术家</th>
                            <th>年份</th>
                            <th>音轨</th>
                            <th>流派</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($Musics as $thisMusic): ?>
                            <?php /** @var AmaotoMusic $thisMusic */ ?>
                            <tr>
                                <td><span class="text-primary"><?= $thisMusic->id ?></span></td>
                                <td><span class="text-danger"><?= $thisMusic->title ?></span></td>
                                <td><span class="text-info"><?= $thisMusic->artist ?></span></td>
                                <td><span class="text-warning"><?= $thisMusic->year ?></span></td>
                                <td><span class="text-success"><?= $thisMusic->track ?></span></td>
                                <td><span class=""><?= $thisMusic->genre ?></span></td>
                                <td>
                                    <a class="btn btn-xs btn-default" href="<?= URL::to(sprintf('play-music/%s', $thisMusic->id)) ?>" target="_blank"><span class="glyphicon glyphicon-play"></span> 播放</a>
                                    <a class="btn btn-xs btn-default" href="<?= URL::to(sprintf('admin/edit-music/%s', $thisMusic->id)) ?>"><span class="glyphicon glyphicon-edit"></span> 编辑</a>
                                    <a class="btn btn-xs btn-default btn-delete-music" data-music-id="<?= $thisMusic->id ?>"><span class="glyphicon glyphicon-remove"></span> 删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="panel-footer">
                        <?php echo $Musics->links() ?>
                        <div class="pull-right">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#upload-music-modal"><span class="glyphicon glyphicon-plus"></span> 添加音乐</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
@stop
