@extends('base-master')

@section('html-body')
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#admin-page-navbar-collapse-main-top">
                <span class="sr-only">导航菜单</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><?= Config::get('constants.site-name') ?>
                <small> - 管理后台</small>
            </a>
        </div>

        <div class="collapse navbar-collapse" id="admin-page-navbar-collapse-main-top">
            <ul class="nav navbar-nav">
                <li><a href="<?= URL::to('') ?>"><span class="glyphicon glyphicon-home"></span> 主页</a></li>
                <li class="<?= Route::currentRouteAction() == 'AdminController@showDashboard' ? 'active' : '' ?>"><a href="<?= URL::to('admin') ?>"><span class="glyphicon glyphicon-dashboard"></span> 控制台</a></li>
                <li class="<?= Route::currentRouteAction() == 'AdminController@showListAlbumPage' ? 'active' : '' ?>"><a href="<?= URL::to('admin/list-album') ?>"><span class="glyphicon glyphicon-th"></span> 专辑</a></li>
                <li class="<?= Route::currentRouteAction() == 'AdminController@showListMusicPage' ? 'active' : '' ?>"><a href="<?= URL::to('admin/list-music') ?>"><span class="glyphicon glyphicon-music"></span> 音乐</a></li>
                <li class="<?= Route::currentRouteAction() == 'AdminController@showListUserPage' ? 'active' : '' ?>"><a href="<?= URL::to('admin/list-user') ?>"><span class="glyphicon glyphicon-user"></span> 用户</a></li>
                <li class="<?= Route::currentRouteAction() == 'AdminController@showOptionPage' ? 'active' : '' ?>"><a href="<?= URL::to('admin/option') ?>"><span class="glyphicon glyphicon-cog"></span> 站点</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?= isset($CurrentUser) ? $CurrentUser->username : '#Username#' ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li class="disabled"><a href="#">个人信息</a></li>
                        <li class="divider"></li>
                        <li class="disabled"><a href="#">登出</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div id="admin-site-body">
    @yield('site-body')
</div>


<div id="admin-site-footer">
    <span>Copyright &copy; <?= Config::get('constants.copyright-year') ?> <?= Config::get('constants.copyright-name') ?>. All rights reserved. Powered by Lialosiu. Version <?= Config::get('constants.version') ?></span>
</div>
@stop