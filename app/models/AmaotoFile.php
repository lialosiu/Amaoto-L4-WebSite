<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * AmaotoFile
 *
 * @property integer $id
 * @property string $md5
 * @property string $name
 * @property string $type
 * @property integer $size
 * @property string $url
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereMd5($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereSize($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoFile whereDeletedAt($value)
 */
class AmaotoFile extends Eloquent
{
    /** @var string */
    protected $table = 'files';

    use SoftDeletingTrait;

    public function getAbsoluteUrl()
    {
        if (!$this->url) {
            return '';
        }
        return URL::to($this->url);
    }

    public function updateTypeByGetId3()
    {
        $getID3 = new getID3();

        $info = $getID3->analyze(public_path($this->url));

        $this->type = isset($info['mime_type']) ? $info['mime_type'] : null;

        return $this;
    }
}