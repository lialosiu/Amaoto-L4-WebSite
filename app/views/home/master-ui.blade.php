@extends('base-master')

<?php /** @var AmaotoUser $CurrentUser */ ?>

@section('html-head')
<script>
    $(document).ready(function () {
        var $htmlTemplate = $('#html-template');
        var $pageContainer = $('.page-container');
        var $musicPage = $('.music-page');
        var $albumPage = $('.album-page');
        var $searchPage = $('.search-page');
        var $jPlayer = $("#player");
        $jPlayer.jPlayer({
            cssSelectorAncestor: ".jplayer-controller",
            solution: "html, flash",
            swfPath: '<?= URL::to('packages/jQuery.jPlayer.2.6.0/')?>',
            supplied: "m4a, mp3"
        });

        Amaoto.Init({
            $MusicBlockTemplate: $htmlTemplate.find('[data-template-name=music-block]').clone(),
            $AlbumBlockTemplate: $htmlTemplate.find('[data-template-name=album-block]').clone(),
            $AlbumExtInfoMusicRowTemplate: $htmlTemplate.find('[data-template-name=album-ext-info-music-row]').clone(),
            $PlayerPlaylistRowTemplate: $htmlTemplate.find('[data-template-name=player-playlist-row]').clone(),

            $jPlayer: $jPlayer,

            $PlaylistUi: $('.player-playlist'),
            $PlayerScreen: $('.player-screen'),
            $PlayerBufferedBar: $('.buffered-bar'),
            $PlayerSeekBar: $('.player-seek-bar'),
            $MiniPlayerUi: $('.mini-player-ui'),
            $PlayingMusicTitle: $('.player-title'),
            $PlayingMusicArtist: $('.player-artist'),
            $PlayingAlbumTitle: $('.player-album-title'),
            $Playing300Cover: $('.player-cover-300'),
            $PlayingCover: $('.player-cover')
        });


        $(document).on('mouseenter', '.mini-player-ui', function () {
            $(this).addClass('mouseenter');
            Amaoto.Daemon.PlayerUi.showMiniPlayerExtUi();
        });
        $(document).on('mouseleave', '.mini-player-ui', function () {
            $(this).removeClass('mouseenter');
            Amaoto.Daemon.PlayerUi.hideMiniPlayerExtUi();
        });
        $(document).on('click', '.mini-player-ui .cover', function () {
            Amaoto.Daemon.PlayerUi.showPlayerScreen();
        });
        $(document).on('slide', '.jp-volume-slider', function (slideEvt) {
            $("#player").jPlayer('volume', slideEvt.value);
        });
        $(document).on('click', '.unlock-player-screen', function () {
            Amaoto.Daemon.PlayerUi.hidePlayerScreen();
        });
        $(document).on('mousemove', function (e) {
            if (lastMousePageY != e.pageY) {
                lastMousePageY = e.pageY;
                lastMouseMoveTime = new Date().getTime();
            }
        });

        $(document).on('click', '.btn-jump-to-play-music-by-index', function () {
            var $item = $(this).parents('[data-playlist-index]');
            var index = $item.data('playlist-index');
            if (typeof(index) == 'undefined') return false;
            Amaoto.Daemon.Player.playByPlaylistIndex(index);
        });
        $(document).on('click', '.btn-remove-from-playlist-by-index', function () {
            var $item = $(this).parents('[data-playlist-index]');
            var index = $item.data('playlist-index');
            if (typeof(index) == 'undefined') return false;
            $item.slideUp(function () {
                Amaoto.Daemon.Player.removeByPlaylistIndex(index);
            });
        });

        $(document).on('click', '.btn-player-play-prev', function () {
            Amaoto.Daemon.Player.prev();
        });
        $(document).on('click', '.btn-player-play-next', function () {
            Amaoto.Daemon.Player.next();
        });
        $(document).on('click', '.btn-empty-playlist', function () {
            Amaoto.Daemon.Player.stopAndEmptyPlaylist();
        });

        $(document).on('click', '.btn-show-home-page', function () {
            $(this).closest('.site-nav-left').find('li.active').removeClass('active');
            $(this).closest('li').addClass('active');
            $('#page').attr('src', '<?= URL::to('home/home-page') ?>');
        });

        $(document).on('click', '.btn-show-album-page', function () {
            $(this).closest('.site-nav-left').find('li.active').removeClass('active');
            $(this).closest('li').addClass('active');
            $('#page').attr('src', '<?= URL::to('home/album-page') ?>');
        });

        $(document).on('click', '.btn-show-music-page', function () {
            $(this).closest('.site-nav-left').find('li.active').removeClass('active');
            $(this).closest('li').addClass('active');
            $('#page').attr('src', '<?= URL::to('home/music-page') ?>');
        });

        $(document).on('mouseenter', '.btn-show-search-page', function () {
            $(this).find('input.search').stop().clearQueue().show().animate({width: '200px', 'margin-left': '10px'});
        });
        $(document).on('mouseleave', '.btn-show-search-page', function () {
            $(this).find('input.search').stop().clearQueue().animate({width: '0', 'margin-left': '0'}, function () {
                $(this).hide();
            });
        });
        $(document).on('click', '.btn-show-search-page', function () {
            var searchStr = $(this).find('input.search').val();
            if (searchStr == '' || $(this).closest('li').hasClass('active')) return;
            $(this).closest('.site-nav-left').find('li.active').removeClass('active');
            $(this).closest('li').addClass('active');
            $('#page').attr('src', '<?= URL::to('home/search-page') ?>?search-str=' + searchStr);
        });
        $(document).on('change', 'input.search', function () {
            var searchStr = $(this).val();
            if (searchStr == '') return;
            $(this).closest('.site-nav-left').find('li.active').removeClass('active');
            $(this).closest('li').addClass('active');
            $('#page').attr('src', '<?= URL::to('home/search-page') ?>?search-str=' + searchStr);
        });

        // -------------------

        var lastMouseMoveTime;
        var lastMousePageY;
        var showPlayerScreenTime = 30 * 1000;

        setInterval(function () {
            if (lastMouseMoveTime + showPlayerScreenTime < new Date().getTime()) {
                Amaoto.Daemon.PlayerUi.showPlayerScreen();
            }
        }, 3000);

        $('.jp-volume-slider').slider();
        Amaoto.Daemon.Player.loadPlaylistFromCookie();

        setTimeout(function () {
            $('.start-screen').fadeOut('slow');
        }, 2000);


//    Dancer.setOptions({
//        flashJS  : BaseUrl+'/packages/dancer.js-0.4.0/lib/soundmanager2.js',
//        flashSWF : BaseUrl+'/packages/dancer.js-0.4.0/lib/soundmanager2.swf'
//    });
//    var dancer = new Dancer();
//    dancer.load(Amaoto.Daemon.Player.$player.jPlayer().find('audio')[0]);

    });
</script>
@stop

@section('html-body')
<div id="html-template" class="hide">
    <div class="music-block" data-template-name="music-block" style="width: 300px;height: 100px;">
        <div class="music-cover" data-template-fill="music-300-cover"></div>
        <div class="music-info">
            <div class="title" data-template-fill="music-title"></div>
            <div class="artist" data-template-fill="music-artist"></div>
        </div>
        <div class="control-panel">
            <div class="btn btn-insert-to-playlist-by-music-id pull-left" title="插队播放"><i class="glyphicon glyphicon-play"></i></div>
            <div class="btn btn-add-to-playlist-by-music-id pull-right" title="添加到播放列表"><i class="glyphicon glyphicon-plus"></i></div>
        </div>
    </div>

    <div class="album-block" data-template-name="album-block" style="width: 160px;height: 260px;">
        <div class="album-cover" data-template-fill="album-300-cover"></div>
        <div class="album-info">
            <div class="title" data-template-fill="album-title"></div>
            <div class="artist" data-template-fill="album-artist"></div>
        </div>
        <div class="control-panel">
            <div class="btn btn-insert-to-playlist-by-album-id pull-left" title="插队播放"><i class="glyphicon glyphicon-play"></i></div>
            <div class="btn btn-add-to-playlist-by-album-id pull-left" title="添加到播放列表"><i class="glyphicon glyphicon-plus"></i></div>
            <div class="btn btn-show-detail-by-album-id pull-right" title="专辑详细"><i class="glyphicon glyphicon-list"></i></div>
        </div>
        <div class="ext-info" style="display: none">
            <div class="ext-info-mask"></div>
            <div class="ext-info-container">
                <div class="ext-album-cover" data-template-fill="album-300-cover"></div>
                <div class="ext-album-info">
                    <div class="title" data-template-fill="album-title"></div>
                    <div class="artist" data-template-fill="album-artist"></div>
                    <div class="year" data-template-fill="album-year"></div>
                    <div class="genre" data-template-fill="album-genre"></div>
                </div>
                <ul class="list-group music-list-group" data-template-fill="music-row">
                </ul>
            </div>
        </div>
    </div>
    <li class="list-group-item" data-template-name="album-ext-info-music-row">
        <div class="row">
            <div class="visible-sm visible-md visible-lg">
                <div class="col-sm-1"><span class="text-warning" data-template-fill="music-track"></span></div>
                <div class="col-sm-5"><span class="text-danger" data-template-fill="music-title"></span></div>
                <div class="col-sm-3"><span class="text-info" data-template-fill="music-artist"></span></div>
                <div class="col-sm-1"><span class="text-primary" data-template-fill="music-play-time"></span></div>
                <div class="col-sm-2">
                    <button class="btn btn-xs btn-default btn-insert-to-playlist-by-music-id" title="插队播放"><i class="glyphicon glyphicon-play"></i></button>
                    <button class="btn btn-xs btn-default btn-add-to-playlist-by-music-id" title="添加到播放列表"><span class="glyphicon glyphicon-plus"></span></button>
                    <button class="btn btn-xs btn-default btn-download-music-by-id" title="下载"><span class="glyphicon glyphicon-save"></span></button>
                </div>
            </div>
            <div class="visible-xs">
                <div class="col-xs-8">
                    <div><span class="text-danger" style="font-size: 1.2em" data-template-fill="music-title"></span></div>
                    <div><span class="text-info" data-template-fill="music-artist"></span></div>
                </div>
                <div class="col-xs-4">
                    <div class="col-xs-1"><span class="text-success" data-template-fill="music-track"></span></div>
                    <div class="col-xs-5"><span class="text-warning" data-template-fill="music-year"></span></div>
                    <div class="col-xs-6"><span class="text-primary" data-template-fill="music-play-time"></span></div>
                    <div class="col-xs-12">
                        <button class="btn btn-xs btn-default btn-insert-to-playlist-by-music-id" title="插队播放"><i class="glyphicon glyphicon-play"></i></button>
                        <button class="btn btn-xs btn-default btn-add-to-playlist-by-music-id" title="添加到播放列表"><span class="glyphicon glyphicon-plus"></span></button>
                        <button class="btn btn-xs btn-default btn-download-music-by-id" title="下载"><span class="glyphicon glyphicon-save"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </li>

    <li class="list-group-item-player-playlist list-group-item" data-template-name="player-playlist-row">
        <div class="row">
            <div class="col-sm-1"><span class="text-warning" data-template-fill="index"></span></div>
            <div class="col-sm-5"><span class="text-danger" data-template-fill="music-title"></span></div>
            <div class="col-sm-3"><span class="text-info" data-template-fill="music-artist"></span></div>
            <div class="col-sm-3">
                <button class="btn btn-xs btn-default btn-jump-to-play-music-by-index" title="跳转到此位置"><i class="fa fa-rocket"></i></button>
                <button class="btn btn-xs btn-default btn-remove-from-playlist-by-index" title="移除"><i class="glyphicon glyphicon-trash"></i></button>
                <button class="btn btn-xs btn-default btn-download-music-by-id" title="下载"><i class="glyphicon glyphicon-save"></i></button>
            </div>
        </div>
    </li>
</div>
<div id="home-site-body">
    <div id="player"></div>
    <div class="player-screen jplayer-controller">
        <div class="cover player-cover" style="background-image: url(<?= URL::to('packages/amaoto/images/no-cover.jpg') ?>)">
            <div class="cover-mask"></div>
        </div>
        <div class="music-info">
            <div class="icon"><i class="glyphicon glyphicon-headphones"></i></div>
            <div class="title player-title"></div>
            <div class="artist player-artist"></div>
            <div class="album-title player-album-title"></div>
        </div>
        <div class="control-panel">
            <span class="btn btn-player-play-prev"><i class="glyphicon glyphicon-step-backward"></i></span>
            <span class="btn jp-play"><i class="glyphicon glyphicon-play"></i></span>
            <span class="btn jp-pause"><i class="glyphicon glyphicon-pause"></i></span>
            <span class="btn jp-stop"><i class="glyphicon glyphicon-stop"></i></span>
            <span class="btn btn-player-play-next"><i class="glyphicon glyphicon-step-forward"></i></span>
        </div>
        <div class="unlock-player-screen">
            <i class="fa fa-angle-double-up"></i>
        </div>
        <div class="seek-bar player-seek-bar jp-seek-bar progress progress-striped active">
            <div class="jp-play-bar progress-bar progress-bar-danger"></div>
        </div>
    </div>
    <div class="mini-player-ui jplayer-controller">
        <div class="cover player-cover-300" style="width: 100px; height: 100px; background-image: url(<?= URL::to('packages/amaoto/images/no-cover.jpg') ?>)"></div>
        <div class="bar">
            <div class="info-bar">
                <span class="title player-title"></span>
                <span class="artist player-artist"></span>
            </div>
            <div class="control-bar">
                <span class="btn btn-player-play-prev"><i class="glyphicon glyphicon-step-backward"></i></span>
                <span class="btn btn-player-play-next"><i class="glyphicon glyphicon-step-forward"></i></span>
                <span class="btn jp-play"><i class="glyphicon glyphicon-play"></i></span>
                <span class="btn jp-pause"><i class="glyphicon glyphicon-pause"></i></span>
                <span class="btn jp-stop"><i class="glyphicon glyphicon-stop"></i></span>
            </div>
            <div class="seek-bar player-seek-bar jp-seek-bar progress progress-striped active">
                <div class="buffered-bar progress-bar progress-bar-success"></div>
                <div class="jp-play-bar progress-bar progress-bar-info"></div>
            </div>
            <div class="jp-no-solution"><b>#无法播放#</b> <span>加载播放器失败，请确认你的浏览器支持 HTML5 或 AdobeFlash</span></div>
        </div>
        <div class="ext-ui">
            <ul class="list-group player-playlist"></ul>
            <div class="pull-left">
                <div class="btn btn-primary btn-sm jp-repeat"><i class="glyphicon glyphicon-sort-by-order"></i> 顺序播放</div>
                <div class="btn btn-primary btn-sm jp-repeat-off"><i class="glyphicon glyphicon-repeat"></i> 洗脑循环</div>
                <div class="btn btn-primary btn-sm btn-empty-playlist"><i class="glyphicon glyphicon-trash"></i> 清空播放列表</div>
            </div>
            <div class="pull-right">
                <span class="btn btn-primary btn-xs jp-mute"><i class="glyphicon glyphicon-volume-up"></i></span>
                <span class="btn btn-primary btn-xs jp-unmute"><i class="glyphicon glyphicon-volume-off"></i></span>
                <input class="jp-volume-slider" data-slider-min="0" data-slider-max="1" data-slider-step="0.01" data-slider-tooltip="hide" data-slider-value="0.8">
            </div>
        </div>
    </div>
    <div class="start-screen" style="background-image: url(<?= URL::to('packages/amaoto/images/start-screen.jpg') ?>); text-shadow: 1px 1px 10px #000;">
        <div style="position:absolute;top:50%;left:50%;height:300px;margin-top: -150px;width:600px;margin-left:-300px;color: #fff;">
            <div style="font-size: 60px"><?= Config::get('constants.site-name') ?><span style="font-size: 20px"> - Ver <?= Config::get('constants.version') ?></span></div>
        </div>
        <div style="text-align: right;position:absolute;bottom:50px;right:50px;color: #fff;">
            <div style="font-size: 20px">Copyright &copy; <?= Config::get('constants.copyright-year') ?> <?= Config::get('constants.copyright-name') ?>. All rights reserved.</div>
            <div style="font-size: 15px">Powered by Lialosiu.</div>
        </div>
    </div>
    <div class="site-nav-left">
        <ul class="list-unstyled">
            <li class="active">
                <div class="btn-show-home-page"><i class="glyphicon glyphicon-home"></i> 首页</div>
            </li>
            <li>
                <div class="btn-show-album-page"><i class="glyphicon glyphicon-picture"></i> 专辑</div>
            </li>
            <li>
                <div class="btn-show-music-page"><i class="glyphicon glyphicon-music"></i> 歌曲</div>
            </li>
            <li>
                <div class="btn-show-search-page"><i class="glyphicon glyphicon-search"></i> 搜索
                    <input class="search" type="text">
                </div>
            </li>
            <?php /*
            <li>
                <div class="btn-show-music-page"><i class="glyphicon glyphicon-user"></i> 用户</div>
            </li> */
            ?>
            <?php if (isset($CurrentUser) && $CurrentUser->isAdmin()): ?>
                <li>
                    <a class="btn-link-to-admin-page" href="<?= URL::to('admin') ?>" target="_blank"><i class="fa fa-tachometer"></i> 管理</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <iframe id="page" src="<?= URL::to('home/home-page') ?>"></iframe>
</div>

<div id="site-footer">
    <span>Copyright &copy; <?= Config::get('constants.copyright-year') ?> <?= Config::get('constants.copyright-name') ?>. All rights reserved. Powered by Lialosiu. Version <?= Config::get('constants.version') ?>.</span>
</div>
@stop