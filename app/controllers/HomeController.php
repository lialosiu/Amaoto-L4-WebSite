<?php

class HomeController extends BaseController
{
    public function showMasterUi()
    {
        return View::make('home/master-ui', array());
    }

    public function showHomePage()
    {
        if (!Config::get('constants.installed')) {
            return Redirect::to('install');
        }

        $needRandomAlbumNum = 9;
        $randomAlbums = array();
        $randomAlbumsOffsetArray = null;

        $albumCount = AmaotoAlbum::count();
        if ($albumCount) {
            $albumOffsetRange = range(0, $albumCount - 1);

            if ($albumCount > $needRandomAlbumNum)
                $randomAlbumsOffsetArray = array_rand($albumOffsetRange, $needRandomAlbumNum);
            else
                $randomAlbumsOffsetArray = array_rand($albumOffsetRange, $albumCount);

            shuffle($randomAlbumsOffsetArray);

            foreach ($randomAlbumsOffsetArray as $offset) {
                $randomAlbums[] = AmaotoAlbum::offset($offset)->first();
            }
        }

        $needRandomMusicNum = 8;
        $randomMusics = array();
        $randomMusicsOffsetArray = null;

        $musicCount = AmaotoMusic::count();
        if ($musicCount) {
            $musicOffsetRange = range(0, $musicCount - 1);

            if ($musicCount > $needRandomMusicNum)
                $randomMusicsOffsetArray = array_rand($musicOffsetRange, $needRandomMusicNum);
            else
                $randomMusicsOffsetArray = array_rand($musicOffsetRange, $musicCount);

            shuffle($randomMusicsOffsetArray);

            foreach ($randomMusicsOffsetArray as $offset) {
                $randomMusics[] = AmaotoMusic::offset($offset)->first();
            }
        }

        return View::make('home/home-page', array(
            'albumCount' => $albumCount,
            'musicCount' => $musicCount,
            'randomAlbums' => $randomAlbums,
            'randomMusics' => $randomMusics,
        ));
    }

    public function showAlbumPage()
    {
        if (!Config::get('constants.installed')) {
            return Redirect::to('install');
        }

        $albums = AmaotoAlbum::orderBy('id', 'desc')->paginate(27);

        return View::make('home/album-page', array('albums' => $albums));
    }

    public function showMusicPage()
    {
        if (!Config::get('constants.installed')) {
            return Redirect::to('install');
        }

        $musics = AmaotoMusic::orderBy('id', 'desc')->paginate(28);

        return View::make('home/music-page', array('musics' => $musics));
    }

    public function showSearchPage()
    {
        if (!Config::get('constants.installed')) {
            return Redirect::to('install');
        }

        $searchStr = Input::get('search-str');
        $albums = AmaotoAlbum::search($searchStr)->limit(100)->get();
        $musics = AmaotoMusic::search($searchStr)->limit(100)->get();

        return View::make('home/search-page', array(
            'searchStr' => $searchStr,
            'albums' => $albums,
            'musics' => $musics,
        ));
    }

}