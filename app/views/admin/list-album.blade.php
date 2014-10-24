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
            <div class="visible-md visible-lg">
                <div class="col-sm-1"><span class="text-success">#ID#</span></div>
                <div class="col-sm-1"><span class="text-warning">#Track#</span></div>
                <div class="col-sm-4"><span class="text-danger">#Title#</span></div>
                <div class="col-sm-3"><span class="text-info">#Artist#</span></div>
                <div class="col-sm-1"><span class="text-primary">#PlayTime#</span></div>
                <div class="col-sm-2">
                    <a class="btn btn-xs btn-default" href="<?= URL::to('play-music') ?>/#ID#" target="_blank"><span class="glyphicon glyphicon-play"></span></a>
                    <button class="btn btn-xs btn-default"><span class="glyphicon glyphicon-edit"></span></button>
                    <button class="btn btn-xs btn-danger btn-remove-music-at-album" data-music-id="#ID#"><span class="glyphicon glyphicon-remove"></span></button>
                </div>
            </div>
            <div class="visible-xs visible-sm">
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
                        <button class="btn btn-xs btn-danger btn-remove-music-at-album" data-music-id="#ID#"><span class="glyphicon glyphicon-remove"></span> 移除</button>
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

    var $modal = $('#album-modal');
    var $modalTitle = $modal.find('.modal-title');
    var $albumTitle = $modal.find('input[name=title]');
    var $albumArtist = $modal.find('input[name=artist]');
    var $albumYear = $modal.find('input[name=year]');
    var $albumGenre = $modal.find('input[name=genre]');
    var $musicListGroup = $modal.find('.music-list-group');

    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'btn-pick-album-file',
        chunk_size: '2mb',
        max_file_size: '50mb',
        url: '<?= URL::to('api/upload-album') ?>',
        multipart_params: {},
        multi_selection: true,
        unique_names: true,
        flash_swf_url: '<?= URL::to('packages/plupload-2.1.1/Moxie.swf') ?>',
        silverlight_xap_url: '<?= URL::to('packages/plupload-2.1.1/Moxie.xap') ?>',
        filters: {
            mime_types: [
                {title: "所有支持的文件", extensions: "mp3,m4a,jpg,png"},
                {title: "音频文件", extensions: "mp3,m4a"},
                {title: "图像文件", extensions: "jpg,png"}
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
                                            console.error(ex);
                                            noty({type: 'error', text: '发生内部错误，请联系管理员'});
                                        }
                                    });
                                };
                            });
                        }
                    });
                });
            },

            BeforeUpload: function (up, file) {
                up.settings.multipart_params.album_id = $('#album-modal').data('album-id');
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
                            var thisAlbum = rsp.data.album;
                            var thisMusic = rsp.data.music;
                            if (thisAlbum) {
                                $albumTitle.val(thisAlbum.title);
                                $albumArtist.val(thisAlbum.artist);
                                $albumYear.val(thisAlbum.year);
                                $albumGenre.val(thisAlbum.genre);
                                $('#album-modal').data('album-id', thisAlbum.id);
                            }
                            switch (pType) {
                                case 'audio':
                                    if (thisMusic) {
                                        $thisBlock.replaceWith(textFormatter($('#amaoto-music-li').html(), {
                                            'ID': thisMusic.id,
                                            'Title': thisMusic.title,
                                            'Artist': thisMusic.artist,
                                            'Year': thisMusic.year,
                                            'Genre': thisMusic.genre,
                                            'Track': thisMusic.track,
                                            'PlayTime': thisMusic.playtime_string,
                                            'MimeType': thisMusic.mime_type
                                        }));
                                    }
                                    break;
                                case 'image':
                                    if (thisFile) {
                                        $modal.find('.cover-image').attr('src', '' + thisFile.url + '');
                                        $thisBlock.replaceWith(textFormatter($('#amaoto-file-li').html(), {'ID': thisFile.id, 'FileName': thisFile.name, 'Type': thisFile.type, 'Size': thisFile.size}));
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
                } catch (e) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
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

    $(document).on('click', '#btn-start-upload-album', function () {
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

    $(document).on('click', '.btn-remove-music-at-album', function () {
        var musicId = $(this).data('music-id');
        noty({
            text: '<p class="text-danger" style="font-size: 1.3em; margin: 10px 0;">确定要将此音乐从专辑中移除？</p>',
            buttons: [
                {
                    addClass: 'btn btn-primary', text: '确定', onClick: function ($noty) {
                    $noty.close();
                    $.ajax({
                        url: '<?= URL::to('api/remove-music-at-album')?>',
                        type: 'get',
                        data: {id: musicId},
                        dataType: "json",
                        success: function (rsp) {
                            switch (rsp.type) {
                                case 'success':
                                    $('#amaoto-music-' + musicId).slideUp(function () {
                                        $(this).remove();
                                    });
                                    break;
                                default:
                                    noty({type: rsp.type, text: rsp.message});
                                    break;
                            }
                        },
                        error: function () {
                            noty({type: 'error', text: '发生内部错误，请联系管理员'});
                        }
                    });
                }
                },
                {
                    addClass: 'btn btn-default', text: '取消', onClick: function ($noty) {
                    $noty.close();
                }
                }
            ]
        });
    });

    $(document).on('click', '.btn-open-album-uploader', function () {
        $modal.data('album-id', "0");
        $modalTitle.html('上传新专辑');
        $albumTitle.val('');
        $albumArtist.val('');
        $albumYear.val('');
        $albumGenre.val('');
        $musicListGroup.empty();
        $modal.modal();
    });

    $(document).on('click', '.btn-open-album-editor', function () {
        $modal.attr('data-album-id', $(this).data('album-id'));
        $modalTitle.html('修改专辑');
        $albumTitle.val('');
        $albumArtist.val('');
        $albumYear.val('');
        $albumGenre.val('');
        $musicListGroup.empty();
        $.ajax({
            url: '<?= URL::to('api/get-album')?>' + '/' + $(this).data('album-id'),
            async: false,
            dataType: "json",
            success: function (rsp) {
                switch (rsp.type) {
                    case 'success':
                        var albumData = rsp.data;
                        $albumTitle.val(albumData.title);
                        $albumArtist.val(albumData.artist);
                        $albumYear.val(albumData.year);
                        $albumGenre.val(albumData.genre);
                        $modal.find('.cover-image').attr('src', '' + albumData.album_cover_url + '');
                        var musicsData = rsp.data.musics;
                        musicsData.forEach(function (thisMusic) {
                            $musicListGroup.append(textFormatter($('#amaoto-music-li').html(), {
                                'ID': thisMusic.id,
                                'Title': thisMusic.title,
                                'Artist': thisMusic.artist,
                                'Year': thisMusic.year,
                                'Genre': thisMusic.genre,
                                'Track': thisMusic.track,
                                'PlayTime': thisMusic.playtime_string,
                                'MimeType': thisMusic.mime_type
                            }));
                        });
                        break;
                    default:
                        noty({type: rsp.type, text: rsp.message});
                        break;
                }
            },
            error: function () {
                noty({type: 'error', text: '发生内部错误，请联系管理员'});
            }
        });
        $modal.modal();
    });

    $(document).on('click', '#btn-admin-edit-album-form-submit', function () {
        $('form#admin-edit-album-form').submit();
    });

    $(document).on('submit', 'form#admin-edit-album-form', function (e) {
        $(this).find('input[name=album-id]').val($modal.data('album-id'));
        $.ajax({
            url: '<?= URL::to('api/edit-album')?>',
            type: 'post',
            data: $(this).serialize(),
            dataType: "json",
            success: function (rsp) {
                switch (rsp.type) {
                    case 'success':
                        $modal.modal('hide');
                        location.reload();
                        break;
                    default:
                        noty({type: rsp.type, text: rsp.message});
                        break;
                }
            },
            error: function () {
                noty({type: 'error', text: '发生内部错误，请联系管理员'});
            }
        });
        e.preventDefault();
    });


    $(document).on('click', '.btn-delete-album', function () {
        var albumId = $(this).data('album-id');
        noty({
            text: '确定要删除该专辑吗？',
            type: 'warning',
            buttons: [
                {
                    addClass: 'btn btn-danger', text: '删除专辑与歌曲', onClick: function ($noty) {
                    $noty.close();
                    $.ajax({
                        url: '<?= URL::to('api/delete-album-with-music')?>',
                        type: 'post',
                        data: {id: albumId},
                        success: function (raw) {
                            try {
                                var rsp = $.parseJSON(raw);
                                noty({
                                    type: rsp.type, text: rsp.message, callback: {
                                        afterClose: function () {
                                            switch (rsp.type) {
                                                case 'success':
                                                    location.reload();
                                                    break;
                                                default:
                                                    break;
                                            }
                                        }
                                    }
                                });
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
                {
                    addClass: 'btn btn-warning', text: '仅移除专辑', onClick: function ($noty) {
                    $noty.close();
                    $.ajax({
                        url: '<?= URL::to('api/delete-album-without-music')?>',
                        type: 'post',
                        data: {id: albumId},
                        success: function (raw) {
                            try {
                                var rsp = $.parseJSON(raw);
                                noty({
                                    type: rsp.type, text: rsp.message, callback: {
                                        afterClose: function () {
                                            switch (rsp.type) {
                                                case 'success':
                                                    location.reload();
                                                    break;
                                                default:
                                                    break;
                                            }
                                        }
                                    }
                                });
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
                {
                    addClass: 'btn btn-default', text: '取消', onClick: function ($noty) {
                    $noty.close();
                }
                }
            ]
        });
    });

    $modal.on('hide.bs.modal', function (e) {
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
<div class="modal fade" id="album-modal" tabindex="-1" role="dialog" data-album-id="0">
    <div class="modal-dialog modal-fluid">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">修改专辑</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div style="width: 130px;float: left;padding-left: 15px;padding-right: 15px;">
                        <img class="cover-image" src="" style="width: 100px;height: 100px;padding: 3px;border: 1px solid #ccc;">
                    </div>
                    <div style="margin-left: 130px;overflow: hidden;">
                        <form id="admin-edit-album-form" method="post" role="form" data-toggle="validator" action="<?= URL::to('api/edit-album') ?>">
                            <input type="hidden" name="album-id" value="0">

                            <div class="form-group col-sm-6">
                                <div class="input-group">
                                    <label class="input-group-addon">专辑名</label>
                                    <input type="text" class="form-control" name="title" maxlength="30" placeholder="请输入专辑名" required>
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="input-group">
                                    <label class="input-group-addon">艺术家</label>
                                    <input type="text" class="form-control" name="artist" maxlength="30" placeholder="请输入专辑艺术家">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="input-group">
                                    <label class="input-group-addon">年份</label>
                                    <input type="text" class="form-control" name="year" maxlength="30" placeholder="请输入专辑年份">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group col-sm-6">
                                <div class="input-group">
                                    <label class="input-group-addon">流派</label>
                                    <input type="text" class="form-control" name="genre" maxlength="30" placeholder="请输入专辑流派">
                                </div>
                                <div class="help-block with-errors"></div>
                            </div>
                        </form>
                    </div>
                </div>
                <ul class="list-group music-list-group">
                </ul>
            </div>
            <div class="modal-footer">
                <button id="btn-switch-local-md5-check" type="button" class="btn btn-danger"><span class="fa fa-bolt"></span> 本地MD5</button>
                <button id="btn-pick-album-file" type="button" class="btn btn-warning"><span class="glyphicon glyphicon-file"></span> 选择文件</button>
                <button id="btn-start-upload-album" type="button" class="btn btn-primary"><span class="glyphicon glyphicon-cloud-upload"></span> 开始上传</button>
                <button id="btn-admin-edit-album-form-submit" type="button" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> 确定</button>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12"></div>
        <div class="col-md-12">
            <?php if (isset($Albums)): ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">专辑列表</div>
                    <table class="table table-striped table-valign-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>标题</th>
                            <th>艺术家</th>
                            <th>年份</th>
                            <th>流派</th>
                            <th>歌曲数</th>
                            <th>封面</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($Albums as $thisAlbum): ?>
                            <?php /** @var AmaotoAlbum $thisAlbum */ ?>
                            <tr>
                                <td><span class="text-primary"><?= $thisAlbum->id ?></span></td>
                                <td><span class="text-danger"><?= $thisAlbum->title ?></span></td>
                                <td><span class="text-info"><?= $thisAlbum->artist ?></span></td>
                                <td><span class="text-warning"><?= $thisAlbum->year ?></span></td>
                                <td><span class=""><?= $thisAlbum->genre ?></span></td>
                                <td><span class="text-success"><?= $thisAlbum->music()->count() ?></span></td>
                                <td><?= $thisAlbum->cover_ori_file_id ? '<span class="glyphicon glyphicon-ok"></span>' : '' ?></td>
                                <td>
                                    <button class="btn btn-xs btn-default btn-open-album-editor" data-album-id="<?= $thisAlbum->id ?>"><span class="glyphicon glyphicon-edit"></span> 编辑</button>
                                    <button class="btn btn-xs btn-default btn-delete-album" data-album-id="<?= $thisAlbum->id ?>"><span class="glyphicon glyphicon-remove"></span> 删除</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="panel-footer">
                        <?php echo $Albums->links() ?>
                        <div class="pull-right">
                            <button class="btn btn-primary btn-open-album-uploader"><span class="glyphicon glyphicon-cloud-upload"></span> 上传专辑</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
@stop
