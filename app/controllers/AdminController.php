<?php

class AdminController extends BaseController
{
    public function showDashboard()
    {
        if (!$this->CurrentUser || !$this->CurrentUser->isAdmin()) {
            return Redirect::to('login');
        }
        return View::make('admin/homepage');
    }

    public function showListUserPage()
    {
        if (!$this->CurrentUser || !$this->CurrentUser->isAdmin()) {
            return Redirect::to('login');
        }
        $Users = AmaotoUser::paginate(15);
        return View::make('admin/list-user', array('Users' => $Users));
    }

    public function showEditUserPage($userId)
    {
        if (!$this->CurrentUser || !$this->CurrentUser->isAdmin()) {
            return Redirect::to('login');
        }
        $thatUser = AmaotoUser::whereId($userId)->first();
        return View::make('admin/edit-user', array('thatUser' => $thatUser));
    }

    public function showListMusicPage()
    {
        if (!$this->CurrentUser || !$this->CurrentUser->isAdmin()) {
            return Redirect::to('login');
        }
        $Musics = AmaotoMusic::orderBy('id', 'desc')->paginate(15);
        return View::make('admin/list-music', array('Musics' => $Musics));
    }

    public function showEditMusicPage($musicId)
    {
        if (!$this->CurrentUser || !$this->CurrentUser->isAdmin()) {
            return Redirect::to('login');
        }
        $thatMusic = AmaotoMusic::whereId($musicId)->first();
        return View::make('admin/edit-music', array('thatMusic' => $thatMusic));
    }

    public function showListAlbumPage()
    {
        if (!$this->CurrentUser || !$this->CurrentUser->isAdmin()) {
            return Redirect::to('login');
        }
        $Albums = AmaotoAlbum::orderBy('id', 'desc')->paginate(15);
        return View::make('admin/list-album', array('Albums' => $Albums));
    }

    public function showOptionPage()
    {
        if (!$this->CurrentUser || !$this->CurrentUser->isAdmin()) {
            return Redirect::to('login');
        }
        return View::make('admin/option');
    }
}