@extends('base-master')

<?php /** @var AmaotoMusic[]|\Illuminate\Pagination\Paginator $musics */ ?>
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
<?php if (isset($musics)): ?>
    <div>
        <?php echo $musics->links() ?>
    </div>
    <div class="music-block-container container-fluid">
        <?php foreach ($musics as $music): ?>
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
    <div>
        <?php echo $musics->links() ?>
    </div>
<?php endif; ?>
@stop