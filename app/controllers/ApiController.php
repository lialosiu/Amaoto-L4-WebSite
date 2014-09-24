<?php

/**
 * Class ApiController
 */
class ApiController extends BaseController
{

    public function doInstall()
    {
        try {
            if (AmaotoOption::getValueByKey('installed')) App::abort(404);
            $siteName = Input::get('site-name');
            $username = Input::get('username');
            $email = Input::get('email');
            $password = Input::get('password');

            $validator = Validator::make(array(
                '站点名' => $siteName,
                '用户名' => $username,
                'Email' => $email,
                '密码' => $password,
            ), array(
                '站点名' => 'required|alpha_dash|min:3|max:20',
                '用户名' => 'required|alpha_dash|min:3|max:20|unique:users,username',
                'Email' => 'email|max:50|unique:users,email',
                '密码' => 'required|min:8|max:30',
            ));

            if ($validator->fails()) {
                throw new InvalidArgumentException($validator->messages()->first());
            }

            $InstalledOption = AmaotoOption::getOptionByKey('installed');
            $InstalledOption->value = 1;
            $InstalledOption->save();

            $siteNameOption = AmaotoOption::getOptionByKey('site-name');
            $siteNameOption->value = $siteName;
            $siteNameOption->save();

            $thatUser = new AmaotoUser;
            $thatUser->username = $username;
            $thatUser->email = $email;
            $thatUser->password = Hash::make($password);
            $thatUser->reg_time = time();
            $thatUser->reg_ip = Input::getClientIp();
            $thatUser->save();

            return Response::json(array(
                'type' => 'success',
                'message' => '初始化成功',
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doSignUp()
    {
        try {
            $username = Input::get('username');
            $email = Input::get('email');
            $password = Input::get('password');

            $validator = Validator::make(array(
                '用户名' => $username,
                'Email' => $email,
                '密码' => $password,
            ), array(
                '用户名' => 'required|alpha_dash|min:3|max:20|unique:users,username',
                'Email' => 'email|max:50|unique:users,email',
                '密码' => 'required|min:8|max:30',
            ));

            if ($validator->fails()) {
                throw new InvalidArgumentException($validator->messages()->first());
            }

            $thatUser = new AmaotoUser;
            $thatUser->username = $username;
            $thatUser->email = $email;
            $thatUser->password = Hash::make($password);
            $thatUser->power = 1;
            $thatUser->reg_time = time();
            $thatUser->reg_ip = Input::getClientIp();
            $thatUser->save();

            return Response::json(array(
                'type' => 'success',
                'message' => '注册成功',
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doLogin()
    {
        try {
            $username = Input::get('username');
            $password = Input::get('password');

            $validator = Validator::make(array(
                '用户名' => $username,
                '密码' => $password,
            ), array(
                '用户名' => 'required|alpha_dash|min:3',
                '密码' => 'required',
            ));

            if ($validator->fails()) {
                throw new InvalidArgumentException($validator->messages()->first());
            }

            if (!Auth::attempt(array('username' => $username, 'password' => $password))) {
                throw new NotMatchException('用户名或密码不正确');
            }

            $this->CurrentUser = Auth::user();
            $this->CurrentUser->updateLogin();

            return Response::json(array(
                'type' => 'success',
                'message' => '登录成功',
                'data' => array('isAdmin' => $this->CurrentUser->isAdmin()),
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (NotMatchException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }


    public function doEditUser()
    {
        try {
            // 检查必需参数
            if (!Input::has('id')) {
                throw new InvalidArgumentException('缺少参数');
            }

            // 检查id格式
            $id = Input::get('id');
            $validator = Validator::make(array('ID' => $id,), array('ID' => 'required|integer|exists:users,id',));
            if ($validator->fails()) {
                throw new InvalidArgumentException($validator->messages()->first());
            }

            // 获取用户
            $thatUser = AmaotoUser::whereId($id)->first();

            // 判断是否成功获取
            if (!$thatUser) {
                throw new NotExistException('该用户不存在');
            }

            // 检查权限
            if ($this->CurrentUser->id !== $thatUser->id && !$this->CurrentUser->isAdmin()) {
                throw new PermissionDeniedException('无权编辑该用户');
            }

            // username
            if (Input::has('username')) {
                $username = Input::get('username');
                $validator = Validator::make(array('用户名' => $username), array('用户名' => 'required|alpha_dash'));
                if ($validator->fails()) {
                    throw new InvalidArgumentException($validator->messages()->first());
                }
                $thatUser->username = $username;
            }

            // email
            if (Input::has('email')) {
                $email = Input::get('email');
                $validator = Validator::make(array('Email' => $email), array('Email' => 'email'));
                if ($validator->fails()) {
                    throw new InvalidArgumentException($validator->messages()->first());
                }
                $thatUser->email = $email;
            }

            // password
            if (Input::has('password')) {
                $password = Input::get('password');
                $validator = Validator::make(array('密码' => $password), array('密码' => 'min:8'));
                if ($validator->fails()) {
                    throw new InvalidArgumentException($validator->messages()->first());
                }
                if (!strlen($password) == 0) {
                    $thatUser->password = Hash::make($password);
                }
            }

            // password
            if (Input::has('power') && $this->CurrentUser->isAdmin()) {
                $power = Input::get('power');
                $validator = Validator::make(array('权限' => $power), array('权限' => 'required|integer|min:0|max:99999'));
                if ($validator->fails()) {
                    throw new InvalidArgumentException($validator->messages()->first());
                }
                $thatUser->power = $power;
            }

            $thatUser->save();

            return Response::json(array(
                'type' => 'success',
                'message' => '修改成功',
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doUploadMusic()
    {
        try {
            if (!Input::hasFile('file'))
                throw new InvalidArgumentException('缺少参数');

            if (!$this->CurrentUser || !$this->CurrentUser->isAdmin())
                throw new PermissionDeniedException('无权上传音乐');

            $theFile = Input::file('file');

            $fileUniName = strtolower(Input::get('name'));
            $fileName = strtolower(Input::get('file_ori_name'));
            $fileUrl = $theFile->getRealPath();
            $chunk = Input::get('chunk');
            $chunks = Input::get('chunks');

            $file = AmaotoUploader::uploadFile($this->CurrentUser->id, $fileUniName, $fileName, $fileUrl, $chunk, $chunks, 'upload');

            $typeArray = explode('/', $file->type);
            $pType = '';
            $sType = '';
            if (is_array($typeArray)) {
                if (isset($typeArray[0]))
                    $pType = $typeArray[0];
                if (isset($typeArray[1]))
                    $sType = $typeArray[1];
            }

            switch ($pType) {
                case 'audio':
                    $music = new AmaotoMusic;
                    $music->file_id = $file->id;
                    $music->album_id = 0;
                    $music->updateTagsByGetId3();
                    $music->save();

                    return Response::json(array(
                        'type' => 'success',
                        'message' => '上传成功',
                        'data' => array('file' => $file->toArray(), 'music' => $music->toArray(),),
                    ));
                default:
                    throw new NotSupportedException('不支持该类型文件');
            }
        } catch (NeedMoreDataException $e) {
            return Response::json(array(
                'type' => 'information',
                'message' => $e->getMessage(),
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doUploadAlbum()
    {
        try {
            if (!Input::hasFile('file'))
                throw new InvalidArgumentException('缺少参数');

            if (!$this->CurrentUser || !$this->CurrentUser->isAdmin())
                throw new PermissionDeniedException('无权上传专辑');

            $theFile = Input::file('file');

            $fileUniName = strtolower(Input::get('name'));
            $fileName = strtolower(Input::get('file_ori_name'));
            $fileUrl = $theFile->getRealPath();
            $chunk = Input::get('chunk');
            $chunks = Input::get('chunks');

            $file = AmaotoUploader::uploadFile($this->CurrentUser->id, $fileUniName, $fileName, $fileUrl, $chunk, $chunks, 'upload');

            // 文件上传完成后--->

            // 判断专辑是否已存在
            $albumId = Input::get('album_id');
            if (is_numeric($albumId) && $albumId > 0) {
                $album = AmaotoAlbum::whereId($albumId)->first();
            } else {
                $album = new AmaotoAlbum;
            }
            if (!$album->title) {
                $album->title = '#未命名专辑#';
            }
            if (!$album->user_id) {
                $album->user_id = $this->CurrentUser->id;
            }
            $album->save();

            $typeArray = explode('/', $file->type);
            $pType = '';
            $sType = '';
            if (is_array($typeArray)) {
                if (isset($typeArray[0]))
                    $pType = $typeArray[0];
                if (isset($typeArray[1]))
                    $sType = $typeArray[1];
            }

            switch ($pType) {
                case 'audio':
                    $music = new AmaotoMusic;
                    $music->file_id = $file->id;
                    $music->updateTagsByGetId3();
                    if (!$album->title || $album->title == '#未命名专辑#') {
                        $album->title = $music->tag_album;
                    }
                    if (!$album->artist) {
                        $album->artist = $music->tag_album_artist ? $music->tag_album_artist : $music->tag_artist;
                    }
                    if (!$album->year) {
                        $album->year = $music->tag_year;
                    }
                    if (!$album->genre) {
                        $album->genre = $music->tag_genre;
                    }
                    $album->save();

                    $music->album_id = $album->id;
                    $music->save();

                    return Response::json(array(
                        'type' => 'success',
                        'message' => '上传成功',
                        'data' => array('file' => $file->toArray(), 'music' => $music->toArray(), 'album' => $album->toArray(),),
                    ));
                case 'image':
                    $album->cover_ori_file_id = $file->id;

                    // 生成300px缩图
                    $width = 300;
                    $height = 300;
                    list($width_orig, $height_orig) = getimagesize(public_path($file->url));
                    $ratio_orig = $width_orig / $height_orig;
                    if ($width / $height > $ratio_orig) {
                        $width = $height * $ratio_orig;
                    } else {
                        $height = $width / $ratio_orig;
                    }
                    $image_p = imagecreatetruecolor($width, $height);
                    $image = imagecreatefromjpeg(public_path($file->url));
                    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
                    $img300TmpUrl = public_path($file->url) . '.300.tmp';
                    imagejpeg($image_p, $img300TmpUrl);
                    imagedestroy($image_p);
                    $img300file = AmaotoUploader::uploadFile($this->CurrentUser->id, $fileUniName, $fileName, $img300TmpUrl, false, false, 'upload');
                    $album->cover_300_file_id = $img300file->id;
                    $album->save();
                    unlink($img300TmpUrl);

                    return Response::json(array(
                        'type' => 'success',
                        'message' => '上传成功',
                        'data' => array('file' => $file->toArray(), 'album' => $album->toArray(),),
                    ));
                default:
                    throw new NotSupportedException('不支持该类型文件');
            }
        } catch (NeedMoreDataException $e) {
            return Response::json(array(
                'type' => 'information',
                'message' => $e->getMessage(),
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doEditAlbum()
    {
        try {
            // 检查必需参数
            if (!Input::has('album-id')) {
                throw new InvalidArgumentException('缺少参数');
            }

            // 检查id格式
            $id = Input::get('album-id');
            $validator = Validator::make(array('ID' => $id,), array('ID' => 'required|integer|exists:albums,id',));
            if ($validator->fails()) {
                throw new InvalidArgumentException($validator->messages()->first());
            }

            // 检查权限
            if (!$this->CurrentUser || !$this->CurrentUser->isAdmin())
                throw new PermissionDeniedException('无权编辑该用户');

            //获取专辑
            $album = AmaotoAlbum::whereId($id)->first();
            if (!$album) {
                throw new NotExistException('该专辑不存在');
            }

            // 设置专辑名
            if (Input::has('title')) {
                $title = Input::get('title');
                $validator = Validator::make(array('专辑名' => $title), array('专辑名' => 'required'));
                if ($validator->fails()) {
                    throw new InvalidArgumentException($validator->messages()->first());
                }
                $album->title = $title;
            }

            // 设置艺术家
            if (Input::has('artist')) {
                $artist = Input::get('artist');
                $album->artist = $artist;
            }

            // 设置年份
            if (Input::has('year')) {
                $year = Input::get('year');
                $album->year = $year;
            }

            // 设置流派
            if (Input::has('genre')) {
                $genre = Input::get('genre');
                $album->genre = $genre;
            }

            $album->save();

            return Response::json(array(
                'type' => 'success',
                'message' => '修改成功',
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doRemoveMusicAtAlbum()
    {
        try {
            // 检查必需参数
            if (!Input::has('id')) {
                throw new InvalidArgumentException('缺少参数');
            }

            // 检查权限
            if (!$this->CurrentUser || !$this->CurrentUser->isAdmin())
                throw new PermissionDeniedException('无权从该专辑中移除此音乐');

            $music = AmaotoMusic::whereId(Input::get('id'))->first();

            if (!$music)
                throw new NotExistException('此音乐不存在');

            $music->album_id = 0;
            $music->save();

            return Response::json(array(
                'type' => 'success',
                'message' => '成功移除',
                'data' => $music,
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doDeleteMusic()
    {
        try {
            // 检查必需参数
            if (!Input::has('id')) {
                throw new InvalidArgumentException('缺少参数');
            }

            // 检查权限
            if (!$this->CurrentUser || !$this->CurrentUser->isAdmin())
                throw new PermissionDeniedException('无权删除此音乐');

            $music = AmaotoMusic::whereId(Input::get('id'))->first();

            if (!$music)
                throw new NotExistException('此音乐不存在');

            $result = $music->delete();

            if (!$result)
                throw new Exception('删除失败');

            return Response::json(array(
                'type' => 'success',
                'message' => '成功删除',
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doDeleteAlbumWithMusic()
    {
        try {
            // 检查必需参数
            if (!Input::has('id')) {
                throw new InvalidArgumentException('缺少参数');
            }

            // 检查权限
            if (!$this->CurrentUser || !$this->CurrentUser->isAdmin())
                throw new PermissionDeniedException('无权删除此专辑');

            $album = AmaotoAlbum::whereId(Input::get('id'))->first();

            if (!$album)
                throw new NotExistException('此专辑不存在');

            foreach($album->music as $music){
                if(!$music->delete())
                    throw new Exception('删除失败');
            }

            $result = $album->delete();

            if (!$result)
                throw new Exception('删除失败');

            return Response::json(array(
                'type' => 'success',
                'message' => '成功删除',
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doDeleteAlbumWithoutMusic()
    {
        try {
            // 检查必需参数
            if (!Input::has('id')) {
                throw new InvalidArgumentException('缺少参数');
            }

            // 检查权限
            if (!$this->CurrentUser || !$this->CurrentUser->isAdmin())
                throw new PermissionDeniedException('无权删除此专辑');

            $album = AmaotoAlbum::whereId(Input::get('id'))->first();

            if (!$album)
                throw new NotExistException('此专辑不存在');

            foreach($album->music as $music){
                $music->album_id = 0;
                $music->save();
            }

            $result = $album->delete();

            if (!$result)
                throw new Exception('删除失败');

            return Response::json(array(
                'type' => 'success',
                'message' => '成功删除',
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function doEditOption()
    {
        try {
            // 检查权限
            if (!$this->CurrentUser->isAdmin()) {
                throw new PermissionDeniedException('无权修改');
            }

            if (Input::has('site-name')) {
                $val = Input::get('site-name');
                $validator = Validator::make(array('站点名' => $val,), array('站点名' => 'required|alpha_dash|min:3|max:20',));
                if ($validator->fails())
                    throw new InvalidArgumentException($validator->messages()->first());
                $option = AmaotoOption::getOptionByKey('site-name');
                $option->value = $val;
                $option->save();
            }

            if (Input::has('copyright-name')) {
                $val = Input::get('copyright-name');
                $validator = Validator::make(array('版权名' => $val,), array('版权名' => 'required|alpha_dash|min:3|max:20',));
                if ($validator->fails())
                    throw new InvalidArgumentException($validator->messages()->first());
                $option = AmaotoOption::getOptionByKey('copyright-name');
                $option->value = $val;
                $option->save();
            }

            if (Input::has('copyright-first-year')) {
                $val = Input::get('copyright-first-year');
                $validator = Validator::make(array('建站年份' => $val,), array('建站年份' => 'required|integer|min:1000|max:9999',));
                if ($validator->fails())
                    throw new InvalidArgumentException($validator->messages()->first());
                $option = AmaotoOption::getOptionByKey('copyright-first-year');
                $option->value = $val;
                $option->save();
            }

            return Response::json(array(
                'type' => 'success',
                'message' => '修改成功',
            ));
        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getAlbumById($id)
    {
        try {
            $album = AmaotoAlbum::whereId($id)->first();
            if (!$album)
                throw new NotExistException('此专辑不存在');

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $album->toDataArray(),
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getAlbumsByOffsetAndLimit($offset = 0, $limit = 10)
    {
        try {
            if (!is_numeric($offset)) $offset = 0;
            if (!is_numeric($limit)) $limit = 10;

            $albums = AmaotoAlbum::orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

            $array = array();
            foreach ($albums as $album) {
                /** @var AmaotoAlbum $album */
                $array[] = $album->toDataArray();
            }

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $array,
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getAlbumByPage()
    {
        try {
            $albums = AmaotoAlbum::orderBy('id', 'desc')->paginate(10);

            $array = array();
            foreach ($albums as $album) {
                /** @var AmaotoAlbum $album */
                $array[] = $album->toDataArray();
            }

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $array,
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getAlbumsBySearchStr()
    {
        try {
            if (!Input::has('search-str'))
                App::abort(404);

            $searchStr = Input::get('search-str');

            $albums = AmaotoAlbum::search($searchStr)->limit(100)->get();

            $array = array();
            foreach ($albums as $album) {
                /** @var AmaotoAlbum $album */
                $array[] = $album->toDataArray();
            }

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $array,
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getAlbumsCount()
    {
        try {
            $albumsCount = AmaotoAlbum::count();

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => array('count' => $albumsCount,),
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getMusicsBySearchStr()
    {
        try {
            if (!Input::has('search-str'))
                App::abort(404);

            $searchStr = Input::get('search-str');

            $musics = AmaotoMusic::search($searchStr)->limit(100)->get();

            $array = array();
            foreach ($musics as $music) {
                /** @var AmaotoMusic $music */
                $array[] = $music->toDataArray();
            }

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $array,
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getMusicsByOffsetAndLimit($offset = 0, $limit = 10)
    {
        try {
            if (!is_numeric($offset)) $offset = 0;
            if (!is_numeric($limit)) $limit = 10;

            $musics = AmaotoMusic::orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

            $array = array();
            foreach ($musics as $music) {
                /** @var AmaotoMusic $music */
                $array[] = $music->toDataArray();
            }

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $array,
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getMusicsByIdJson()
    {
        try {
            if (!Input::has('id-json'))
                App::abort(404);

            $musicIdArray = json_decode(Input::get('id-json'));

            $musics = AmaotoMusic::findMany($musicIdArray);

            $array = array();
            foreach ($musics as $music) {
                /** @var AmaotoMusic $music */
                $array[] = $music->toDataArray();
            }

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $array,
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getMusicsCount()
    {
        try {
            $musicsCount = AmaotoMusic::count();

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => array('count' => $musicsCount,),
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function checkFileMd5IsExist()
    {
        try {
            if (!Input::has('md5'))
                throw new InvalidArgumentException('缺少参数');

            if (AmaotoFile::whereMd5(Input::get('md5'))->count()) {
                return Response::json(array(
                    'type' => 'information',
                    'message' => '文件已存在',
                ));
            } else {
                return Response::json(array(
                    'type' => 'information',
                    'message' => '文件不存在',
                ));
            }

        } catch (InvalidArgumentException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function getMusicById($id)
    {
        try {
            $music = AmaotoMusic::whereId($id)->first();
            if (!$music)
                throw new NotExistException('此音乐不存在');

            return Response::json(array(
                'type' => 'success',
                'message' => '获取成功',
                'data' => $music->toDataArray(),
            ));
        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

    public function downloadMusicById($id)
    {
        try {
            $music = AmaotoMusic::whereId($id)->first();
            if (!$music)
                throw new NotExistException('此音乐不存在');

            return Response::download(public_path($music->file->url), $music->title, array('Content-Type' => $music->file->type));

        } catch (NotExistException $e) {
            return Response::json(array(
                'type' => 'warning',
                'message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            return Response::json(array(
                'type' => 'error',
                'message' => $e->getMessage(),
            ));
        }
    }

}