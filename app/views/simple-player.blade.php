@extends('base-master')

@section('html-head')
@stop

@section('html-body')
<div class="container">
    <?php if (isset($thatMusic)): ?>
        <?php /** @var AmaotoMusic $thatMusic */ ?>
        <div style="margin: 100px 20px">
            <h2 class="text-danger"><b><?= $thatMusic->title ?></b></h2>
            <h4 class="text-infp"><?= $thatMusic->artist ?></h4>
            <hr>
            <audio src="<?= $thatMusic->file->url ?>" style="width: 500px" controls autoplay>浏览器不支持</audio>
        </div>
    <?php endif; ?>
</div>
@stop