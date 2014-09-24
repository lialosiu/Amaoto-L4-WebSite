<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * AmaotoMusic
 *
 * @property integer $id
 * @property string $title
 * @property string $artist
 * @property string $year
 * @property integer $track
 * @property string $genre
 * @property string $mime_type
 * @property float $playtime_seconds
 * @property string $playtime_string
 * @property float $bitrate
 * @property string $tag_title
 * @property string $tag_artist
 * @property string $tag_album
 * @property string $tag_year
 * @property string $tag_track
 * @property string $tag_genre
 * @property string $tag_comment
 * @property string $tag_album_artist
 * @property string $tag_composer
 * @property string $tag_disc_number
 * @property string $tag_json
 * @property integer $file_id
 * @property integer $album_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \AmaotoFile $file
 * @property-read \AmaotoAlbum $album
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTitle($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereArtist($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereYear($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTrack($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereGenre($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereMimeType($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic wherePlaytimeSeconds($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic wherePlaytimeString($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereBitrate($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagTitle($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagArtist($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagAlbum($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagYear($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagTrack($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagGenre($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagComment($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagAlbumArtist($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagComposer($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagDiscNumber($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereTagJson($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereFileId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereAlbumId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereUpdatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoMusic whereDeletedAt($value) 
 * @method static \AmaotoMusic search($str) 
 */
class AmaotoMusic extends Eloquent
{
    /** @var string */
    protected $table = 'musics';

    use SoftDeletingTrait;

    public function file()
    {
        return $this->belongsTo('AmaotoFile', 'file_id');
    }

    public function album()
    {
        return $this->belongsTo('AmaotoAlbum', 'album_id');
    }

    public function getFileUrl()
    {
        if (!$this->file) {
            return '';
        }
        return $this->file->getAbsoluteUrl();
    }

    public function getCoverUrl()
    {
        if (!$this->album) {
            return URL::to('packages/amaoto/images/no-cover.jpg');
        }
        return $this->album->getCoverUrl();
    }

    public function getCover300Url()
    {
        if (!$this->album) {
            return URL::to('packages/amaoto/images/no-cover-300.jpg');
        }
        return $this->album->getCover300Url();
    }

    public function getAlbumTitle()
    {
        if (!$this->album) {
            return '';
        }
        return $this->album->title;
    }

    public function scopeSearch(Illuminate\Database\Eloquent\Builder $query, $str)
    {
        return $query->where('title', 'like', '%' . $str . '%')->orWhere('artist', 'like', '%' . $str . '%');
    }

    public function updateTagsByGetId3()
    {
        $getID3 = new getID3();
        $getID3->encoding_id3v1 = "GBK";
        $info = $getID3->analyze(public_path($this->file->url));

        if (isset($info['tags'])) {
            $tagTypes = ['quicktime', 'ape', 'id3v2', 'id3v1'];
            foreach ($tagTypes as $thisTagType) {
                if (isset($info['tags'][$thisTagType])) {
                    $tag = $info['tags'][$thisTagType];
                    $this->tag_title = implode(';', isset($tag['title']) ? $tag['title'] : array());
                    $this->tag_artist = implode(';', isset($tag['artist']) ? $tag['artist'] : array());
                    $this->tag_album = implode(';', isset($tag['album']) ? $tag['album'] : array());
                    $this->tag_year = implode(';', isset($tag['creation_date']) ? $tag['creation_date'] : (isset($tag['year']) ? $tag['year'] : array()));
                    $this->tag_track = implode(';', isset($tag['track_number']) ? $tag['track_number'] : (isset($tag['track']) ? $tag['track'] : array()));
                    $this->tag_genre = implode(';', isset($tag['genre']) ? $tag['genre'] : array());
                    $this->tag_comment = implode(';', isset($tag['comment']) ? $tag['comment'] : array());
                    $this->tag_album_artist = implode(';', isset($tag['album_artist']) ? $tag['album_artist'] : array());
                    $this->tag_composer = implode(';', isset($tag['composer']) ? $tag['composer'] : array());
                    $this->tag_disc_number = implode(';', isset($tag['disc_number']) ? $tag['disc_number'] : array());
                    try {
                        $this->tag_json = json_encode($tag);
                    } catch (ErrorException $ex) {
                        $this->tag_json = '';
                    }
                    $this->title = $this->tag_title ? $this->tag_title : $this->file->name;
                    $this->artist = $this->tag_artist;
                    $this->year = $this->tag_year;
                    $tmp = explode('/', $this->tag_track);
                    $this->track = (is_array($tmp) && sizeof($tmp) > 0) ? $tmp[0] : $this->tag_track;
                    $this->genre = $this->tag_genre;
                    break;
                }
            }
        } else {
            $this->title = $this->file->name;
            $this->artist = '';
            $this->year = '';
            $this->track = 0;
            $this->genre = '';
        }

        $this->playtime_seconds = isset($info['playtime_seconds']) ? $info['playtime_seconds'] : '';
        $this->playtime_string = isset($info['playtime_string']) ? $info['playtime_string'] : '';
        $this->bitrate = isset($info['bitrate']) ? $info['bitrate'] : 0;
        $this->mime_type = isset($info['mime_type']) ? $info['mime_type'] : '';

        return $this;
    }

    public function toDataArray()
    {
        $array = $this->toArray();
        $array['file_url'] = $this->getFileUrl();
        $array['album_cover_url'] = $this->getCoverUrl();
        $array['album_cover_300_url'] = $this->getCover300Url();
        $array['album_title'] = $this->getAlbumTitle();
        return $array;
    }
}