<?php

App::error(function (Symfony\Component\HttpKernel\Exception\HttpException $exception, $code) {
    if (Config::get('app.debug') == false || 1) {
        switch ($code) {
            case 404:
                return Response::view('errors.404', array('errorCode' => $code), 404);

            case 500:
                return Response::view('errors.500', array('errorCode' => $code), 500);

            default:
                return Response::view('errors.default', array('errorCode' => $code), $code);
        }
    }
    return false;
});

App::error(function (BrowserNotSupportedException $exception) {
    return Response::view('errors.browser-not-supported');
});

App::error(function (AppNeedInstallException $exception) {
    return Redirect::to('install');
});