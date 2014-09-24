<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::any('/', 'HomeController@showMasterUi');
Route::any('home/home-page', 'HomeController@showHomePage');
Route::any('home/album-page', 'HomeController@showAlbumPage');
Route::any('home/music-page', 'HomeController@showMusicPage');
Route::any('home/search-page', 'HomeController@showSearchPage');

Route::any('install', function () {
    if (Config::get('constants.installed')) App::abort(404);
    return View::make('install');
});

Route::any('login', function () {
    return View::make('login');
});

Route::any('register', function () {
    return View::make('register');
});

Route::any('api/install', 'ApiController@doInstall');

Route::any('api/login', 'ApiController@doLogin');
Route::any('api/register', 'ApiController@doSignUp');

Route::any('api/edit-user', 'ApiController@doEditUser');
Route::any('api/edit-album', 'ApiController@doEditAlbum');
Route::any('api/edit-option', 'ApiController@doEditOption');
Route::any('api/upload-music', 'ApiController@doUploadMusic');
Route::any('api/upload-album', 'ApiController@doUploadAlbum');
Route::any('api/remove-music-at-album', 'ApiController@doRemoveMusicAtAlbum');
Route::any('api/delete-music', 'ApiController@doDeleteMusic');
Route::any('api/delete-album-with-music', 'ApiController@doDeleteAlbumWithMusic');
Route::any('api/delete-album-without-music', 'ApiController@doDeleteAlbumWithoutMusic');

Route::any('api/get-album/{id}', 'ApiController@getAlbumById');
Route::any('api/get-albums/{offset}/{limit?}', 'ApiController@getAlbumsByOffsetAndLimit');
Route::any('api/get-albums-by-search-str', 'ApiController@getAlbumsBySearchStr');
Route::any('api/get-albums-count', 'ApiController@getAlbumsCount');
Route::any('api/get-music/{id}', 'ApiController@getMusicById');
Route::any('api/get-musics/{offset}/{limit?}', 'ApiController@getMusicsByOffsetAndLimit');
Route::any('api/get-musics-by-search-str', 'ApiController@getMusicsBySearchStr');
Route::any('api/get-musics-by-id-json', 'ApiController@getMusicsByIdJson');
Route::any('api/get-musics-count', 'ApiController@getMusicsCount');

Route::any('api/download-music/{id}', 'ApiController@downloadMusicById');

Route::any('api/check-file-md5-is-exist', 'ApiController@checkFileMd5IsExist');

Route::any('admin', 'AdminController@showDashboard');
Route::any('admin/list-user', 'AdminController@showListUserPage');
Route::any('admin/edit-user/{id}', 'AdminController@showEditUserPage');
Route::any('admin/list-music', 'AdminController@showListMusicPage');
Route::any('admin/edit-music/{id}', 'AdminController@showEditMusicPage');
Route::any('admin/list-album', 'AdminController@showListAlbumPage');
Route::any('admin/option', 'AdminController@showOptionPage');

Route::any('play-music/{id}', function ($id) {
    $thatMusic = AmaotoMusic::whereId($id)->first();
    return View::make('simple-player', array('thatMusic' => $thatMusic));
});