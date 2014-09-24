@extends('errors.base')

@section('site-body')
<div class="text-danger text-center">
    <h1>Error - <?= isset($errorCode) ? $errorCode : 'Unknown' ?></h1>
    <h3>嗯……？</h3>
    <h3>好像出错了_(:3」∠)_</h3>
</div>
@stop