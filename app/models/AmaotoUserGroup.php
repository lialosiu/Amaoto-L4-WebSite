<?php

/**
 * AmaotoUserGroup
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $power
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\AmaotoUser[] $user
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUserGroup whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUserGroup whereName($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUserGroup whereDescription($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUserGroup wherePower($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUserGroup whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUserGroup whereUpdatedAt($value) 
 */
class AmaotoUserGroup extends Eloquent
{
    /** @var string */
    protected $table = 'user_groups';

    public function user()
    {
        return $this->hasMany('AmaotoUser', 'user_group_id');
    }
}