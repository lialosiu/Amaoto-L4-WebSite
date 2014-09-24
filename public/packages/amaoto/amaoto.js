$(document).ready(function () {
    $.ajaxSetup({
        cache: false
    });
});

//$(document).on('submit', 'form', function (e) {
//    return true;
//    var form = this;
//    if ($(form).data('do-not-ajax-submit')) return false;
//    $(form).find('[type=submit]').attr("disabled", true);
//    var action = $(form).attr('action');
//    var method = $(form).attr('method');
//    var successHref = $(form).data('success-href');
//    $.ajax({
//        url: action,
//        type: method,
//        data: $(form).serialize(),
//        success: function (raw) {
//            try {
//                var rsp = (typeof raw == 'object') ? raw : $.parseJSON(raw);
//                noty({type: rsp.type, text: rsp.message, callback: {
//                    afterClose: function () {
//                        switch (rsp.type) {
//                            case 'success':
//                                if (successHref)
//                                    location.href = successHref;
//                                else
//                                    location.reload();
//                                break;
//                            default:
//                                break;
//                        }
//                        $(form).find('[type=submit]').attr("disabled", false);
//                    }
//                }});
//            } catch (ex) {
//                noty({type: 'error', text: '发生内部错误，请联系管理员'});
//                throw(ex);
//            }
//        },
//        error: function (XMLHttpRequest, textStatus, errorThrown) {
//            noty({type: 'error', text: '发生内部错误，请联系管理员'});
//            console.error(XMLHttpRequest);
//        }
//    });
//    e.preventDefault();
//});

function textFormatter(o_text, obj) {
    var text = o_text;
    $.each(obj, function (key, val) {
        var regexp = new RegExp('#' + key + '#', 'g');
        text = text.replace(regexp, val || '');
    });
    return text;
}


Amaoto = {};
Amaoto.Class = {};
Amaoto.Class.Album = function () {
    this.id = undefined;
    this.title = undefined;
    this.artist = undefined;
    this.year = undefined;
    this.genre = undefined;
    this.cover_file_id = undefined;
    this.user_id = undefined;
    this.album_cover_url = undefined;
    this.album_cover_300_url = undefined;
    this.musics = undefined;
};
Amaoto.Class.Music = function () {
    this.id = undefined;
    this.title = undefined;
    this.artist = undefined;
    this.year = undefined;
    this.track = undefined;
    this.genre = undefined;
    this.mime_type = undefined;
    this.playtime_seconds = undefined;
    this.playtime_string = undefined;
    this.bitrate = undefined;
    this.tag_title = undefined;
    this.tag_artist = undefined;
    this.tag_album = undefined;
    this.tag_year = undefined;
    this.tag_track = undefined;
    this.tag_genre = undefined;
    this.tag_comment = undefined;
    this.tag_album_artist = undefined;
    this.tag_composer = undefined;
    this.tag_disc_number = undefined;
    this.tag_json = undefined;
    this.file_id = undefined;
    this.album_id = undefined;
    this.file_url = undefined;
    this.album_title = undefined;
    this.album_cover_url = undefined;
    this.album_cover_300_url = undefined;
};

Amaoto.Class.AlbumDataManager = function () {
    var _this = this;
    this.albumsCount = undefined;
    this.albums = {};
    this.setAlbum = function (album) {
        this.albums[album.id] = album;
    };
    this.setAlbumArray = function (albums) {
        $.each(albums, function (i, album) {
            _this.albums[album.id] = album;
        });
    };
    this.getAlbumById = function (id, callback) {
        if (this.albums[id]) {
            if (callback)
                callback(this.albums[id]);
            else
                return this.albums[id];
        } else {
            this.downloadAlbumById(id, callback);
        }
    };
    this.downloadAlbumById = function (id, callback) {
        $.ajax({
            url: BaseUrl + '/api/get-album/' + id,
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            var album = Amaoto.Factory.CreateAlbumByJsonObj(rsp.data);
                            _this.setAlbum(album);
                            if (callback)
                                callback(album);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
    this.getAlbumsByOffsetAndLimit = function (offset, limit, callback) {
        this.downloadAlbumsByOffsetAndLimit(offset, limit, callback);
    };
    this.getAlbumsByOffsetRange = function (offsetA, offsetB, callback) {
        var limit = offsetB - offsetA;
        if (limit <= 0) return false;
        this.downloadAlbumsByOffsetAndLimit(offsetA, limit, callback);
    };
    this.downloadAlbumsByOffsetAndLimit = function (offset, limit, callback) {
        $.ajax({
            url: BaseUrl + '/api/get-albums/' + offset + '/' + limit,
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            var array = [];
                            $.each(rsp.data, function (i, obj) {
                                array.push(Amaoto.Factory.CreateAlbumByJsonObj(obj));
                            });
                            _this.setAlbumArray(array);
                            if (callback)
                                callback(array);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
    this.getAlbumsBySearchStr = function (search_str, callback) {
        this.downloadAlbumsBySearchStr(search_str, callback);
    };
    this.downloadAlbumsBySearchStr = function (search_str, callback) {
        $.ajax({
            url: BaseUrl + '/api/get-albums-by-search-str',
            type: 'post',
            data: {'search-str': search_str},
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            var array = [];
                            $.each(rsp.data, function (i, obj) {
                                array.push(Amaoto.Factory.CreateAlbumByJsonObj(obj));
                            });
                            _this.setAlbumArray(array);
                            if (callback)
                                callback(array);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
    this.getAlbumsCount = function (callback) {
        if (_this.albumsCount) return _this.albumsCount;
        _this.downloadAlbumsCount(callback);
    };
    this.downloadAlbumsCount = function (callback) {
        $.ajax({
            url: BaseUrl + '/api/get-albums-count',
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            if (rsp.data && rsp.data.count) {
                                _this.albumsCount = rsp.data.count;
                            }
                            if (callback)
                                callback(rsp.data.count);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
};
Amaoto.Class.MusicDataManager = function () {
    var _this = this;
    this.musicsCount = undefined;
    this.musics = {};
    this.setMusic = function (music) {
        this.musics[music.id] = music;
    };
    this.setMusicArray = function (musics) {
        $.each(musics, function (i, music) {
            _this.musics[music.id] = music;
        });
    };
    this.getMusicsByIdArray = function (array, callback) {
        var needDownload = false;
        var musics = [];
        $.each(array, function (i, id) {
            var music = _this.musics[id];
            if (!music) {
                needDownload = true;
                return false;
            }
            musics.push(music);
        });
        if (needDownload) {
            this.downloadMusicByIdArray(array, callback);
        } else {
            callback(musics);
        }
    };
    this.downloadMusicByIdArray = function (array, callback) {
        $.ajax({
            url: BaseUrl + '/api/get-musics-by-id-json',
            type: 'post',
            data: {'id-json': JSON.stringify(array)},
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            var musics = [];
                            $.each(rsp.data, function (i, obj) {
                                musics.push(Amaoto.Factory.CreateMusicByJsonObj(obj));
                            });
                            _this.setMusicArray(musics);
                            if (callback)
                                _this.getMusicsByIdArray(array, callback);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
    this.getMusicById = function (id, callback) {
        if (this.musics[id]) {
            if (callback)
                callback(this.musics[id]);
            else
                return this.musics[id];
        } else {
            this.downloadMusicById(id, callback);
        }
    };
    this.downloadMusicById = function (id, callback) {
        $.ajax({
            url: BaseUrl + '/api/get-music/' + id,
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            var music = Amaoto.Factory.CreateMusicByJsonObj(rsp.data);
                            _this.setMusic(music);
                            if (callback)
                                callback(music);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
    this.getMusicsByOffsetAndLimit = function (offset, limit, callback) {
        this.downloadMusicsByOffsetAndLimit(offset, limit, callback);
    };
    this.getMusicsByOffsetRange = function (offsetA, offsetB, callback) {
        var limit = offsetB - offsetA;
        if (limit <= 0) return false;
        this.downloadMusicsByOffsetAndLimit(offsetA, limit, callback);
    };
    this.downloadMusicsByOffsetAndLimit = function (offset, limit, callback) {
        $.ajax({
            url: BaseUrl + '/api/get-musics/' + offset + '/' + limit,
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            var array = [];
                            $.each(rsp.data, function (i, obj) {
                                array.push(Amaoto.Factory.CreateMusicByJsonObj(obj));
                            });
                            _this.setMusicArray(array);
                            if (callback)
                                callback(array);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
    this.getMusicsBySearchStr = function (search_str, callback) {
        this.downloadMusicsBySearchStr(search_str, callback);
    };
    this.downloadMusicsBySearchStr = function (search_str, callback) {
        $.ajax({
            url: BaseUrl + '/api/get-musics-by-search-str',
            type: 'post',
            data: {'search-str': search_str},
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            var array = [];
                            $.each(rsp.data, function (i, obj) {
                                array.push(Amaoto.Factory.CreateMusicByJsonObj(obj));
                            });
                            _this.setMusicArray(array);
                            if (callback)
                                callback(array);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
    this.getMusicsCount = function (callback) {
        if (_this.albumsCount) return _this.albumsCount;
        _this.downloadMusicsCount(callback);
    };
    this.downloadMusicsCount = function (callback) {
        $.ajax({
            url: BaseUrl + '/api/get-musics-count',
            dataType: 'json',
            success: function (rsp) {
                try {
                    switch (rsp.type) {
                        case 'success':
                            if (rsp.data && rsp.data.count) {
                                _this.musicsCount = rsp.data.count;
                            }
                            if (callback)
                                callback(rsp.data.count);
                            break;
                        default:
                            noty({type: rsp.type, text: rsp.message});
                            break;
                    }
                } catch (ex) {
                    noty({type: 'error', text: '发生内部错误，请联系管理员'});
                    throw ex;
                }
            }
        });
    };
};
//Amaoto.Class.HomePage = function () {
//};
//Amaoto.Class.AlbumPage = function () {
//    var _this = this;
//    this.$Page = undefined;
//    this.$BlockTemplate = undefined;
//    this.$ExtInfoMusicRowTemplate = undefined;
//    this.$BlockContainer = undefined;
//    this.AlbumBlocksSortByOffset = [];
//    this.NowShowingAlbumBlockLatestOffset = 0;
//    this.appendAlbumBlockByAlbumArray = function (albums) {
//        $.each(albums, function (i, album) {
//            _this.appendAlbumBlockByAlbum(album);
//        });
//    };
//    this.appendAlbumBlockByAlbum = function (album) {
//        var $block = _this.$BlockTemplate.clone();
//        $block.attr('data-album-id', album.id);
//        $block.find('[data-template-fill=album-300-cover]').css({'background-image': 'url(' + album.album_cover_300_url + ')'});
//        $block.find('[data-template-fill=album-cover]').css({'background-image': 'url(' + album.album_cover_url + ')'});
//        $block.find('[data-template-fill=album-title]').html(album.title);
//        $block.find('[data-template-fill=album-artist]').html(album.artist);
//        $block.find('[data-template-fill=album-year]').html(album.year);
//        $block.find('[data-template-fill=album-genre]').html(album.genre);
//        var $musicRowContainer = $block.find('[data-template-fill=music-row]');
//        $.each(album.musics, function (i, music) {
//            var $row = _this.$ExtInfoMusicRowTemplate.clone();
//            $row.attr('data-music-id', music.id);
//            $row.find('[data-template-fill=music-title]').html(music.title);
//            $row.find('[data-template-fill=music-artist]').html(music.artist);
//            $row.find('[data-template-fill=music-track]').html(music.track);
//            $row.find('[data-template-fill=music-year]').html(music.year);
//            $row.find('[data-template-fill=music-genre]').html(music.genre);
//            $row.find('[data-template-fill=music-play-time]').html(music.playtime_string);
//            $row.appendTo($musicRowContainer);
//        });
//        $block.hide();
//        $block.appendTo(_this.$BlockContainer);
//        $block.show('slow');
//    };
//    this.appendAlbumBlockByAlbumId = function (id) {
//        Amaoto.DataManager.AlbumDataManager.getAlbumById(id, _this.appendAlbumBlockByAlbum);
//    };
//    this.appendAlbumBlockByOffsetAndLimit = function (offset, limit) {
//        Amaoto.DataManager.AlbumDataManager.getAlbumsByOffsetAndLimit(offset, limit, _this.appendAlbumBlockByAlbumArray);
//    };
//    this.showAlbumPageByOffsetAndLimit = function (offset, limit) {
//
//        _this.appendAlbumBlockByOffsetAndLimit(offset, limit);
//    };
//    this.appendAlbumBlockByNumber = function (num) {
//        if (_this.NowShowingAlbumBlockLatestOffset >= Amaoto.DataManager.AlbumDataManager.albumsCount) {
//            _this.NoMore = true;
//            return;
//        }
//        Amaoto.DataManager.AlbumDataManager.getAlbumsByOffsetRange(_this.NowShowingAlbumBlockLatestOffset, _this.NowShowingAlbumBlockLatestOffset + num, _this.appendAlbumBlockByAlbumArray);
//        _this.NowShowingAlbumBlockLatestOffset = _this.NowShowingAlbumBlockLatestOffset + num;
//    };
//    this.appendAlbumBlockToFillPage = function () {
//        var $lastBlock = _this.$BlockContainer.find('.album-block').last();
//        var lastBlockBottomPos = $lastBlock.offset() ? $lastBlock.offset().top + $lastBlock.height() : 0;
//        var lastBlockRightPos = $lastBlock.offset() ? $lastBlock.offset().left + $lastBlock.width() : 0;
//        var pageBottomPos = $(window).scrollTop() + $(window).height();
//
//        var colNum = Math.floor(_this.$BlockContainer.width() / _this.$BlockTemplate.width());
//        var canAppendNum = Math.floor((_this.$BlockContainer.width() - lastBlockRightPos) / _this.$BlockTemplate.width());
//
//        if (lastBlockBottomPos < pageBottomPos) {
//            canAppendNum += Math.floor((pageBottomPos - lastBlockBottomPos) / _this.$BlockTemplate.height()) * colNum;
//        }
//        this.appendAlbumBlockByNumber(canAppendNum);
//    };
//};
//Amaoto.Class.MusicPage = function () {
//    var _this = this;
//    this.NoMore = false;
//    this.$Page = undefined;
//    this.$BlockTemplate = undefined;
//    this.$BlockContainer = undefined;
//    this.MusicBlocksSortByOffset = [];
//    this.NowShowingMusicBlockLatestOffset = 0;
//
//    this.appendMusicBlockByMusicArray = function (musics) {
//        $.each(musics, function (i, music) {
//            _this.appendMusicBlockByMusic(music);
//        });
//    };
//
//    this.appendMusicBlockByMusic = function (music) {
//        var $block = _this.$BlockTemplate.clone();
//        $block.attr('data-music-id', music.id);
//        $block.find('[data-template-fill=music-300-cover]').css({'background-image': 'url(' + music.album_cover_300_url + ')'});
//        $block.find('[data-template-fill=music-title]').html(music.title);
//        $block.find('[data-template-fill=music-artist]').html(music.artist);
//        $block.find('[data-template-fill=music-year]').html(music.year);
//        $block.find('[data-template-fill=music-genre]').html(music.genre);
//        $block.hide();
//        $block.appendTo(_this.$BlockContainer);
//        $block.show('slow');
//    };
//
//    this.appendMusicBlockByMusicId = function (id) {
//        Amaoto.DataManager.MusicDataManager.getMusicById(id, _this.appendMusicBlockByMusic);
//    };
//
//    this.appendMusicBlockByNumber = function (num) {
//        if (_this.NowShowingMusicBlockLatestOffset >= Amaoto.DataManager.AlbumDataManager.musicsCount) {
//            _this.NoMore = true;
//            return;
//        }
//        Amaoto.DataManager.MusicDataManager.getMusicsByOffsetRange(_this.NowShowingMusicBlockLatestOffset, _this.NowShowingMusicBlockLatestOffset + num, _this.appendMusicBlockByMusicArray);
//        _this.NowShowingMusicBlockLatestOffset = _this.NowShowingMusicBlockLatestOffset + num;
//        if (_this.NowShowingMusicBlockLatestOffset >= Amaoto.DataManager.MusicDataManager.musicsCount) _this.NoMore = true;
//    };
//
//    this.appendMusicBlockByRowNumber = function (num) {
//        var colNum = Math.floor(_this.$BlockContainer.width() / _this.$BlockTemplate.width());
//        var canAppendNum = colNum * num;
//        this.appendMusicBlockByNumber(canAppendNum);
//    };
//
//    this.appendMusicBlockToFillPage = function () {
//        var $lastBlock = _this.$BlockContainer.find('.music-block').last();
//        var lastBlockBottomPos = $lastBlock.offset() ? $lastBlock.offset().top + $lastBlock.height() : 0;
//        var lastBlockRightPos = $lastBlock.offset() ? $lastBlock.offset().left + $lastBlock.width() : 0;
//        var pageBottomPos = $(window).scrollTop() + $(window).height();
//
//        var colNum = Math.floor(_this.$BlockContainer.width() / _this.$BlockTemplate.width());
//        var canAppendNum = Math.floor((_this.$BlockContainer.width() - lastBlockRightPos) / _this.$BlockTemplate.width());
//
//        if (lastBlockBottomPos < pageBottomPos) {
//            canAppendNum += Math.floor((pageBottomPos - lastBlockBottomPos) / _this.$BlockTemplate.height()) * colNum;
//        }
//        this.appendMusicBlockByNumber(canAppendNum);
//    };
//};

Amaoto.Class.PageManager = function () {
    var _this = this;

    this.buildAlbumBlockByAlbum = function (album) {
        var $block = Amaoto.Daemon.Template.$AlbumBlockTemplate.clone();
        $block.attr('data-album-id', album.id);
        $block.find('[data-template-fill=album-300-cover]').css({'background-image': 'url(' + album.album_cover_300_url + ')'});
        $block.find('[data-template-fill=album-cover]').css({'background-image': 'url(' + album.album_cover_url + ')'});
        $block.find('[data-template-fill=album-title]').html(album.title);
        $block.find('[data-template-fill=album-artist]').html(album.artist);
        $block.find('[data-template-fill=album-year]').html(album.year);
        $block.find('[data-template-fill=album-genre]').html(album.genre);
        var $musicRowContainer = $block.find('[data-template-fill=music-row]');
        $.each(album.musics, function (i, music) {
            var $row = Amaoto.Daemon.Template.$AlbumExtInfoMusicRowTemplate.clone();
            $row.attr('data-music-id', music.id);
            $row.find('[data-template-fill=music-title]').html(music.title);
            $row.find('[data-template-fill=music-artist]').html(music.artist);
            $row.find('[data-template-fill=music-track]').html(music.track);
            $row.find('[data-template-fill=music-year]').html(music.year);
            $row.find('[data-template-fill=music-genre]').html(music.genre);
            $row.find('[data-template-fill=music-play-time]').html(music.playtime_string);
            $row.appendTo($musicRowContainer);
        });
        return $block;
    };

    this.buildAlbumBlockByAlbumArray = function (array) {
        var result = [];
        $.each(array, function (i, album) {
            result.push(_this.buildAlbumBlockByAlbum(album));
        });
        return result;
    };

    this.buildMusicBlockByMusic = function (music) {
        var $block = Amaoto.Daemon.Template.$MusicBlockTemplate.clone();
        $block.attr('data-music-id', music.id);
        $block.find('[data-template-fill=music-300-cover]').css({'background-image': 'url(' + music.album_cover_300_url + ')'});
        $block.find('[data-template-fill=music-title]').html(music.title);
        $block.find('[data-template-fill=music-artist]').html(music.artist);
        $block.find('[data-template-fill=music-year]').html(music.year);
        $block.find('[data-template-fill=music-genre]').html(music.genre);
        return $block;
    };

    this.buildMusicBlockByMusicArray = function (array) {
        var result = [];
        $.each(array, function (i, music) {
            result.push(_this.buildMusicBlockByMusic(music));
        });
        return result;
    };
};

// todo

Amaoto.Class.Player = function () {
    var _this = this;
    var playingMusic = undefined;
    var playingIndex = undefined;
    var playlist = [];

    this.getPlayingMusic = function () {
        return playingMusic;
    };

    this.getPlayingIndex = function () {
        return playingIndex;
    };

    this.getPlaylist = function () {
        return playlist;
    };

    this.addToPlaylistByMusic = function (music) {
        noty({layout: 'bottom', modal: false, type: 'success', text: '歌曲 [' + music.title + '] 已添加到播放列表'});
        playlist.push(music);
        if (typeof(playingIndex) == 'undefined') _this.startPlayByPlaylist();
        Amaoto.Daemon.PlayerUi.refresh();
    };

    this.insertToPlaylistByMusic = function (music) {
        noty({layout: 'bottom', modal: false, type: 'success', text: '插队播放歌曲 [' + music.title + '] '});
        playlist.splice(+playingIndex + 1, 0, music);
        _this.next();
        if (typeof(playingIndex) == 'undefined') _this.startPlayByPlaylist();
        Amaoto.Daemon.PlayerUi.refresh();
    };

    this.addToPlaylistByAlbum = function (album) {
        noty({layout: 'bottom', modal: false, type: 'success', text: '专辑 [' + album.title + '] 已添加到播放列表'});
        $.each(album.musics, function (i, music) {
            playlist.push(music);
        });
        if (typeof(playingIndex) == 'undefined') _this.startPlayByPlaylist();
        Amaoto.Daemon.PlayerUi.refresh();
    };

    this.insertToPlaylistByAlbum = function (album) {
        noty({layout: 'bottom', modal: false, type: 'success', text: '插队播放专辑 [' + album.title + '] '});
        var playlistIndex = (playingIndex ? +playingIndex : 0) + 1;
        $.each(album.musics, function (i, music) {
            playlist.splice(playlistIndex++, 0, music);
        });
        _this.next();
        if (typeof(playingIndex) == 'undefined') _this.startPlayByPlaylist();
        Amaoto.Daemon.PlayerUi.refresh();
    };

    this.setPlaylistByAlbum = function (album) {
        noty({layout: 'bottom', modal: false, type: 'success', text: '播放专辑 [' + album.title + ']'});
        playlist = [];
        $.each(album.musics, function (index, music) {
            playlist.push(music);
        });
        _this.startPlayByPlaylist();
    };

    this.setByAmaotoMusic = function (music) {
        if (!music) return false;
        playingMusic = music;
        Amaoto.Daemon.PlayerUi.refresh();
        switch (music.mime_type) {
            case 'audio/mp4':
                Amaoto.Daemon.Dom.$jPlayer.jPlayer("setMedia", {m4a: music.file_url});
                break;
            case 'audio/mpeg':
                Amaoto.Daemon.Dom.$jPlayer.jPlayer("setMedia", {mp3: music.file_url});
                break;
            default:
                Amaoto.Daemon.Dom.$jPlayer.jPlayer("setMedia", {mp3: music.file_url});
                break;
        }
        return true;
    };

    this.play = function () {
        Amaoto.Daemon.Dom.$jPlayer.jPlayer('play');
    };
    this.pause = function () {
        Amaoto.Daemon.Dom.$jPlayer.jPlayer('pause');
    };
    this.stop = function () {
        Amaoto.Daemon.Dom.$jPlayer.jPlayer('stop');
    };
    this.prev = function () {
        if (!playlist[+playingIndex - 1]) return false;
        playingIndex--;
        if (!_this.setByAmaotoMusic(playlist[+playingIndex])) return false;
        _this.play();
        return true;
    };
    this.next = function () {
        if (!playlist[+playingIndex + 1]) return false;
        playingIndex++;
        if (!_this.setByAmaotoMusic(playlist[+playingIndex])) return false;
        _this.play();
        return true;
    };
    this.startPlayByPlaylist = function () {
        playingIndex = 0;
        if (!_this.setByAmaotoMusic(playlist[+playingIndex])) return false;
        _this.play();
        return true;
    };
    this.playByPlaylistIndex = function (index) {
        if (!playlist[index]) return false;
        playingIndex = index;
        if (!_this.setByAmaotoMusic(playlist[+playingIndex])) return false;
        _this.play();
        return true;
    };
    this.jumpToPlaylistIndex = function (index) {
        if (!playlist[index]) return false;
        playingIndex = index;
        if (!_this.setByAmaotoMusic(playlist[+playingIndex])) return false;
        return true;
    };
    this.removeByPlaylistIndex = function (index) {
        if (!playlist[index]) return false;
        if (playingIndex == index) {
            _this.next();
        }
        playlist.splice(index, 1);
        if (playingIndex > index) {
            playingIndex--;
        }
        return true;
    };
    this.savePlaylistToCookie = function () {
        var idArray = [];
        $.each(playlist, function (i, music) {
            idArray.push(music.id);
        });
        $.cookie('playlist', idArray.toString(), { expires: 7 });
        $.cookie('PlayingIndex', playingIndex, { expires: 7 });
    };

    this.loadPlaylistFromCookie = function () {
        var idStr = $.cookie('playlist');
        var idArray = idStr ? idStr.split(',') : [];
        if (idArray.length != 0)
            playingIndex = $.cookie('PlayingIndex');

        function addToPlaylistByMusics(array) {
            $.each(array, function (i, music) {
                playlist.push(music);
            });
            _this.jumpToPlaylistIndex(playingIndex);
        }

        Amaoto.Daemon.MusicDataManager.getMusicsByIdArray(idArray, addToPlaylistByMusics);
    };

    this.stopAndEmptyPlaylist = function () {
        _this.stop();
        playlist = [];
        playingIndex = undefined;
        playingMusic = undefined;
        Amaoto.Daemon.Dom.$jPlayer.jPlayer("clearMedia");
        Amaoto.Daemon.PlayerUi.refresh();
        _this.savePlaylistToCookie();
    };

    $(document).on($.jPlayer.event.play, Amaoto.Daemon.Dom.$jPlayer, function () {
        Amaoto.Daemon.PlayerUi.activeSeekBar();
    });

    $(document).on($.jPlayer.event.pause, Amaoto.Daemon.Dom.$jPlayer, function () {
        Amaoto.Daemon.PlayerUi.staticSeekBar();
    });

    $(document).on($.jPlayer.event.ended, Amaoto.Daemon.Dom.$jPlayer, function () {
        if (!_this.next()) {
            _this.stop();
            playingMusic = undefined;
        }
        Amaoto.Daemon.PlayerUi.refresh();
    });

    $(document).on($.jPlayer.event.timeupdate, Amaoto.Daemon.Dom.$jPlayer, function () {
        try {
            if (Amaoto.Daemon.Dom.$jPlayer.find('audio').length >= 1) {
                var a = Amaoto.Daemon.Dom.$jPlayer.find('audio')[0];
                if (typeof a.buffered != 'undefined' && typeof a.duration != 'undefined') {
                    var bufferedPercent = Math.round(a.buffered.end(a.buffered.length - 1) / a.duration * 100);
                    Amaoto.Daemon.Dom.$PlayerBufferedBar.width(bufferedPercent + "%");
                }
            }
        } catch ($ex) {

        }
    });

};
Amaoto.Class.PlayerUi = function () {
    var _this = this;

    this.showPlayerScreen = function () {
        Amaoto.Daemon.Dom.$PlayerScreen.stop().clearQueue().fadeIn(function () {
            Amaoto.Daemon.Dom.$MiniPlayerUi.css({'visibility': 'hidden'});
        });
    };
    this.hidePlayerScreen = function () {
        Amaoto.Daemon.Dom.$MiniPlayerUi.removeAttr('style');
        Amaoto.Daemon.Dom.$PlayerScreen.stop().clearQueue().animate({'margin-bottom': $(window).height() + 'px', opacity: 0}, function () {
            $(this).removeAttr('style').hide();
        });
    };
    this.showMiniPlayerExtUi = function () {
        Amaoto.Daemon.Dom.$MiniPlayerUi.find('.cover').stop().clearQueue().animate({width: '200px', height: '200px'});
        Amaoto.Daemon.Dom.$MiniPlayerUi.find('.bar').stop().clearQueue().animate({'margin-left': '200px'});
        Amaoto.Daemon.Dom.$MiniPlayerUi.find('.ext-ui').stop().clearQueue().slideDown();
    };
    this.hideMiniPlayerExtUi = function () {
        if (Amaoto.Daemon.Dom.$MiniPlayerUi.hasClass('mouseenter')) return;
        Amaoto.Daemon.Dom.$MiniPlayerUi.find('.cover').stop().clearQueue().animate({width: '100px', height: '100px'});
        Amaoto.Daemon.Dom.$MiniPlayerUi.find('.bar').stop().clearQueue().animate({'margin-left': '100px'});
        Amaoto.Daemon.Dom.$MiniPlayerUi.find('.ext-ui').stop().clearQueue().slideUp();
    };
    this.activeSeekBar = function () {
        Amaoto.Daemon.Dom.$PlayerSeekBar.addClass('active');
    };
    this.staticSeekBar = function () {
        Amaoto.Daemon.Dom.$PlayerSeekBar.removeClass('active');
    };
    this.showNothingPlaying = function () {
        Amaoto.Daemon.Dom.$PlayingMusicTitle.html('Nothing Playing');
        Amaoto.Daemon.Dom.$PlayingMusicArtist.html('');
        Amaoto.Daemon.Dom.$PlayingAlbumTitle.html('');
        Amaoto.Daemon.Dom.$Playing300Cover.css({'background-image': 'url(' + BaseUrl + '/packages/amaoto/images/no-cover-300.jpg' + ')'});
        Amaoto.Daemon.Dom.$PlayingCover.css({'background-image': 'url(' + BaseUrl + '/packages/amaoto/images/no-cover.jpg' + ')'});
    };
    this.showNotification = function (title, body, icon, tag) {
        if (typeof Notification !== 'undefined') {
            if (Notification.permission !== "granted") {
                Notification.requestPermission(function (status) {
                    if (Notification.permission !== status) {
                        Notification.permission = status;
                    }
                });
            } else if (Notification.permission === "granted") {
                var n = new Notification(title, {body: body, icon: icon, tag: tag});
                setTimeout(function () {
                    n.close()
                }, 3000);
            }
        }
    };
    this.refresh = function () {
        var nowPlayingMusic = Amaoto.Daemon.Player.getPlayingMusic();
        if (nowPlayingMusic) {
            Amaoto.Daemon.Dom.$PlayingMusicTitle.html(nowPlayingMusic.title);
            Amaoto.Daemon.Dom.$PlayingMusicArtist.html(nowPlayingMusic.artist);
            Amaoto.Daemon.Dom.$PlayingAlbumTitle.html(nowPlayingMusic.album_title);
            Amaoto.Daemon.Dom.$Playing300Cover.css({'background-image': 'url(' + nowPlayingMusic.album_cover_300_url + ')'});
            Amaoto.Daemon.Dom.$PlayingCover.css({'background-image': 'url(' + nowPlayingMusic.album_cover_url + ')'});
            _this.showNotification(nowPlayingMusic.title, nowPlayingMusic.artist, nowPlayingMusic.album_cover_300_url, 'NowPlaying');
        } else {
            _this.showNothingPlaying();
        }
        Amaoto.Daemon.Dom.$PlaylistUi.empty();
        $.each(Amaoto.Daemon.Player.getPlaylist(), function (i, music) {
            var $row = Amaoto.Daemon.Template.$PlayerPlaylistRowTemplate.clone();
            if (i == Amaoto.Daemon.Player.getPlayingIndex()) $row.addClass('active');
            $row.attr('data-playlist-index', i);
            $row.attr('data-music-id', music.id);
            $row.find('[data-template-fill=index]').html(i + 1);
            $row.find('[data-template-fill=music-title]').html(music.title);
            $row.find('[data-template-fill=music-artist]').html(music.artist);
            $row.find('[data-template-fill=music-year]').html(music.year);
            $row.find('[data-template-fill=music-genre]').html(music.genre);
            $row.find('[data-template-fill=music-playtime]').html(music.playtime_string);
            $row.hide();
            $row.appendTo(Amaoto.Daemon.Dom.$PlaylistUi);
            $row.show();
        });
        var position = Amaoto.Daemon.Dom.$PlaylistUi.children('.active').position();
        var scrollTopPos = 0;
        if (position)
            scrollTopPos = position.top + Amaoto.Daemon.Dom.$PlaylistUi.scrollTop() - 100;
        Amaoto.Daemon.Dom.$PlaylistUi.scrollTop(scrollTopPos);
        Amaoto.Daemon.Player.savePlaylistToCookie();
    };
};

Amaoto.Factory = {};
Amaoto.Factory.CreateMusicByJsonObj = function (obj) {
    var _this = new Amaoto.Class.Music;
    _this.id = obj.id;
    _this.title = obj.title;
    _this.artist = obj.artist;
    _this.year = obj.year;
    _this.track = obj.track;
    _this.genre = obj.genre;
    _this.mime_type = obj.mime_type;
    _this.playtime_seconds = obj.playtime_seconds;
    _this.playtime_string = obj.playtime_string;
    _this.bitrate = obj.bitrate;
    _this.tag_title = obj.tag_title;
    _this.tag_artist = obj.tag_artist;
    _this.tag_album = obj.tag_album;
    _this.tag_year = obj.tag_year;
    _this.tag_track = obj.tag_track;
    _this.tag_genre = obj.tag_genre;
    _this.tag_comment = obj.tag_comment;
    _this.tag_album_artist = obj.tag_album_artist;
    _this.tag_composer = obj.tag_composer;
    _this.tag_disc_number = obj.tag_disc_number;
    _this.tag_json = obj.tag_json;
    _this.file_id = obj.file_id;
    _this.album_id = obj.album_id;
    _this.file_url = obj.file_url;
    _this.album_title = obj.album_title;
    _this.album_cover_url = obj.album_cover_url;
    _this.album_cover_300_url = obj.album_cover_300_url;
    return _this;
};

Amaoto.Factory.CreateAlbumByJsonObj = function (obj) {
    var _this = new Amaoto.Class.Album;
    _this.id = obj.id;
    _this.title = obj.title;
    _this.artist = obj.artist;
    _this.year = obj.year;
    _this.genre = obj.genre;
    _this.cover_file_id = obj.cover_file_id;
    _this.user_id = obj.user_id;
    _this.album_cover_url = obj.album_cover_url;
    _this.album_cover_300_url = obj.album_cover_300_url;
    if (obj.musics && obj.musics.length) {
        _this.musics = [];
        $.each(obj.musics, function (i, obj) {
            var music = Amaoto.Factory.CreateMusicByJsonObj(obj);
            _this.musics.push(music);
            Amaoto.Daemon.MusicDataManager.setMusic(music);
        });
    }
    return _this;
};

Amaoto.Init = function (parameters) {
    Amaoto.Daemon = {};

    Amaoto.Daemon.Template = {};
    Amaoto.Daemon.Template.$MusicBlockTemplate = parameters.$MusicBlockTemplate;
    Amaoto.Daemon.Template.$AlbumBlockTemplate = parameters.$AlbumBlockTemplate;
    Amaoto.Daemon.Template.$AlbumExtInfoMusicRowTemplate = parameters.$AlbumExtInfoMusicRowTemplate;
    Amaoto.Daemon.Template.$PlayerPlaylistRowTemplate = parameters.$PlayerPlaylistRowTemplate;

    Amaoto.Daemon.Dom = {};
    Amaoto.Daemon.Dom.$jPlayer = parameters.$jPlayer;
    Amaoto.Daemon.Dom.$PlaylistUi = parameters.$PlaylistUi;
    Amaoto.Daemon.Dom.$PlayerScreen = parameters.$PlayerScreen;
    Amaoto.Daemon.Dom.$PlayerBufferedBar = parameters.$PlayerBufferedBar;
    Amaoto.Daemon.Dom.$PlayerSeekBar = parameters.$PlayerSeekBar;
    Amaoto.Daemon.Dom.$MiniPlayerUi = parameters.$MiniPlayerUi;
    Amaoto.Daemon.Dom.$PlayingMusicTitle = parameters.$PlayingMusicTitle;
    Amaoto.Daemon.Dom.$PlayingMusicArtist = parameters.$PlayingMusicArtist;
    Amaoto.Daemon.Dom.$PlayingAlbumTitle = parameters.$PlayingAlbumTitle;
    Amaoto.Daemon.Dom.$Playing300Cover = parameters.$Playing300Cover;
    Amaoto.Daemon.Dom.$PlayingCover = parameters.$PlayingCover;

    Amaoto.Daemon.PageManager = new Amaoto.Class.PageManager();
    Amaoto.Daemon.AlbumDataManager = new Amaoto.Class.AlbumDataManager();
    Amaoto.Daemon.MusicDataManager = new Amaoto.Class.MusicDataManager();

    Amaoto.Daemon.AlbumDataManager.downloadAlbumsCount();
    Amaoto.Daemon.MusicDataManager.downloadMusicsCount();

    Amaoto.Daemon.Player = new Amaoto.Class.Player();

    Amaoto.Daemon.PlayerUi = new Amaoto.Class.PlayerUi();
    Amaoto.Daemon.PlayerUi.showNothingPlaying();
};