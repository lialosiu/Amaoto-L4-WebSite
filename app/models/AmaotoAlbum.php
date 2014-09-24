<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * AmaotoAlbum
 *
 * @property integer $id
 * @property string $title
 * @property string $artist
 * @property string $year
 * @property string $genre
 * @property integer $cover_300_file_id
 * @property integer $cover_ori_file_id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\AmaotoMusic[] $music
 * @property-read \AmaotoFile $cover
 * @property-read \AmaotoFile $cover300
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereTitle($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereArtist($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereYear($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereGenre($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereCover300FileId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereCoverOriFileId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereUserId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereUpdatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoAlbum whereDeletedAt($value) 
 * @method static \AmaotoAlbum search($str) 
 */
class AmaotoAlbum extends Eloquent
{
    /** @var string */
    protected $table = 'albums';

    use SoftDeletingTrait;

    public function music()
    {
        return $this->hasMany('AmaotoMusic', 'album_id');
    }

    public function cover()
    {
        return $this->belongsTo('AmaotoFile', 'cover_ori_file_id');
    }

    public function cover300()
    {
        return $this->belongsTo('AmaotoFile', 'cover_300_file_id');
    }

    public function getCoverUrl()
    {
        if (!$this->cover || !$this->cover->getAbsoluteUrl())
            return URL::to('packages/amaoto/images/no-cover.jpg');
        return $this->cover->getAbsoluteUrl();
    }

    public function getCover300Url()
    {
        if (!$this->cover300 || !$this->cover300->getAbsoluteUrl())
            return URL::to('packages/amaoto/images/no-cover-300.jpg');
        return $this->cover300->getAbsoluteUrl();
    }

    public function scopeSearch(Illuminate\Database\Eloquent\Builder $query, $str)
    {
        return $query->where('title', 'like', '%' . $str . '%')->orWhere('artist', 'like', '%' . $str . '%');
    }

    public function toDataArray()
    {
        $array = $this->toArray();

        $array['album_cover_url']     = $this->getCoverUrl();
        $array['album_cover_300_url'] = $this->getCover300Url();

        $musicArray = array();
        foreach ($this->music as $music) {
            $musicArray[] = $music->toDataArray();
        }
        $array['musics'] = $musicArray;

        return $array;
    }
}