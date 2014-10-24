<?php

/**
 * AmaotoOption
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\AmaotoOption whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoOption whereKey($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoOption whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoOption whereUpdatedAt($value)
 */
class AmaotoOption extends Eloquent
{

    /** @var string */
    protected $table = 'options';

    /**
     * @param $key
     * @param string $value
     * @return mixed|string
     */
    public static function getValueByKey($key, $value = '')
    {
        try {
            $option = self::whereKey($key)->first();
        } catch (\Illuminate\Database\QueryException $ex) {
            return NULL;
        }
        if (!$option) {
            return $value;
        }
        return $option->value;
    }

    /**
     * @param $key
     * @param string $value
     * @return AmaotoOption|\Illuminate\Database\Eloquent\Model|mixed|null|static
     */
    public static function getOptionByKey($key, $value = '')
    {
        try {
            $option = self::whereKey($key)->first();
        } catch (\Illuminate\Database\QueryException $ex) {
            return NULL;
        }
        if (!$option) {
            $option = new AmaotoOption();
            $option->key = $key;
            $option->value = $value;
        }
        return $option;
    }

}