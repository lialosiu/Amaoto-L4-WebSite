@extends('base-master')

<?php /** @var AmaotoAlbum[]|\Illuminate\Pagination\Paginator $randomAlbums */ ?>
<?php /** @var AmaotoMusic[]|\Illuminate\Pagination\Paginator $randomMusics */ ?>
<?php /** @var AmaotoUser $CurrentUser */ ?>

@section('html-head')
<script>
    var Amaoto = undefined;
    $(document).ready(function () {
        if (self != top) {
            $('body').addClass('in-iframe');
            Amaoto = top.Amaoto;
        }
    });
    $(document).on('click', '.handler-show-album-detail', function () {
        var $thisAlbumBlock = $(this).parents('.album-block');
        var $extInfoBlock = $thisAlbumBlock.find('.ext-info');
        var thisIsActive = $thisAlbumBlock.hasClass('active');

        $('.album-block').removeClass('active');
        $('.ext-info').stop().clearQueue().fadeOut();

        if (thisIsActive) return;

        $thisAlbumBlock.addClass('active');
        $extInfoBlock.stop().clearQueue().fadeIn();
    });
    $(document).on('click', '.handler-hide-album-detail', function (e) {
        $('.album-block').removeClass('active');
        $('.ext-info').stop().clearQueue().fadeOut();
    });
    $(document).on('click', '.handler-add-album-to-playlist', function (e) {
        var $item = $(this).parents('[data-album-id]');
        var id = $item.data('album-id');
        Amaoto.Daemon.AlbumDataManager.getAlbumById(id, Amaoto.Daemon.Player.addToPlaylistByAlbum);
    });
    $(document).on('click', '.handler-insert-album-to-playlist', function (e) {
        var $item = $(this).parents('[data-album-id]');
        var id = $item.data('album-id');
        if (typeof(id) == 'undefined') return false;
        Amaoto.Daemon.AlbumDataManager.getAlbumById(id, Amaoto.Daemon.Player.insertToPlaylistByAlbum);
    });
    $(document).on('click', '.handler-insert-music-to-playlist', function () {
        var $item = $(this).parents('[data-music-id]');
        var id = $item.data('music-id');
        if (typeof(id) == 'undefined') return false;
        Amaoto.Daemon.MusicDataManager.getMusicById(id, Amaoto.Daemon.Player.insertToPlaylistByMusic);
    });
    $(document).on('click', '.handler-add-music-to-playlist', function () {
        var $item = $(this).parents('[data-music-id]');
        var id = $item.data('music-id');
        if (typeof(id) == 'undefined') return false;
        Amaoto.Daemon.MusicDataManager.getMusicById(id, Amaoto.Daemon.Player.addToPlaylistByMusic);
    });
    $(document).on('click', '.handler-download-music', function () {
        var $item = $(this).parents('[data-music-id]');
        var id = $item.data('music-id');
        window.open(BaseUrl + '/api/download-music/' + id);
    });
</script>
@stop

@section('html-body')
<div>
    <?php if (isset($albumCount) && isset($musicCount)): ?>
        <h3 class="text-info"><i class="glyphicon glyphicon-bullhorn"></i> 目前全站共有 <?= $albumCount ?> 张专辑， <?= $musicCount ?> 首歌曲。</h3>
    <?php endif; ?>
</div>
<h1 class="text-danger">随机推荐</h1>
<?php if (isset($randomAlbums)): ?>
<div class="album-block-container container-fluid">
    <?php foreach ($randomAlbums as $album): ?>
        <div class="album-block" data-album-id="<?= $album->id ?>" style="width: 160px;height: 260px;">
            <div class="album-cover handler-show-album-detail" style="background-image: url(<?= $album->getCover300Url() ?>)"></div>
            <div class="album-info">
                <div class="album-title-container">
                    <span class="album-title" title="<?= $album->title ?>"><?= $album->title ?></span>
                </div>
                <div class="album-artist-container">
                    <span class="album-artist" title="<?= $album->artist ?>"><?= $album->artist ?></span>
                </div>
            </div>
            <div class="control-panel">
                <div class="btn pull-left handler-insert-album-to-playlist" title="插队播放"><i class="glyphicon glyphicon-play"></i></div>
                <div class="btn pull-left handler-add-album-to-playlist" title="添加到播放列表"><i class="glyphicon glyphicon-plus"></i></div>
                <div class="btn pull-right handler-show-album-detail" title="专辑详细"><i class="glyphicon glyphicon-list"></i></div>
            </div>
            <div class="ext-info" style="display: none">
                <div class="ext-info-mask handler-hide-album-detail"></div>
                <div class="ext-info-container">
                    <div class="ext-album-cover" style="background-image: url(<?= $album->getCover300Url() ?>)"></div>
                    <div class="ext-album-info">
                        <div class="album-title-container">
                            <span class="album-title" title="<?= $album->title ?>"><?= $album->title ?></span>
                        </div>
                        <div class="album-artist-container">
                            <span class="album-artist" title="<?= $album->artist ?>"><?= $album->artist ?></span>
                        </div>
                        <div class="album-year-container">
                            <span class="album-year" title="<?= $album->year ?>"><?= $album->year ?></span>
                        </div>
                        <div class="album-genre-container">
                            <span class="album-genre" title="<?= $album->genre ?>"><?= $album->genre ?></span>
                        </div>
                    </div>
                    <ul class="list-group music-list-group">
                        <?php $musics = $album->music()->orderBy('track', 'asc')->get(); ?>
                        <?php foreach ($musics as $music): ?>
                            <li data-music-id="<?= $music->id ?>" class="list-group-item">
                                <div class="row">
                                    <div class="col-sm-1"><span class="music-track"><?= $music->track ?></span></div>
                                    <div class="col-sm-5"><span class="music-title"><?= $music->title ?></span></div>
                                    <div class="col-sm-3"><span class="music-artist"><?= $music->artist ?></span></div>
                                    <div class="col-sm-1"><span class="music-playtime"><?= $music->playtime_string ?></span></div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-xs btn-default handler-insert-music-to-playlist" title="插队播放"><i class="glyphicon glyphicon-play"></i></button>
                                        <button class="btn btn-xs btn-default handler-add-music-to-playlist" title="添加到播放列表"><span class="glyphicon glyphicon-plus"></span></button>
                                        <button class="btn btn-xs btn-default handler-download-music" title="下载"><span class="glyphicon glyphicon-save"></span></button>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (isset($randomMusics)): ?>
    <div class="music-block-container container-fluid">
        <?php foreach ($randomMusics as $music): ?>
            <div class="music-block" data-music-id="<?= $music->id ?>" style="width: 300px;height: 100px;">
                <div class="music-cover" style="background-image: url(<?= $music->getCover300Url() ?>)"></div>
                <div class="music-info">
                    <div class="music-title-container"><span class="music-title" title="<?= $music->title ?>"><?= $music->title ?></span></div>
                    <div class="music-artist-container"><span class="music-artist" title="<?= $music->artist ?>"><?= $music->artist ?></span></div>
                </div>
                <div class="control-panel">
                    <div class="btn pull-left handler-insert-music-to-playlist" title="插队播放"><i class="glyphicon glyphicon-play"></i></div>
                    <div class="btn pull-right handler-add-music-to-playlist" title="添加到播放列表"><i class="glyphicon glyphicon-plus"></i></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
@stop