<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

/**
 * AmaotoUser
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property integer $power
 * @property string $reg_ip
 * @property integer $reg_time
 * @property string $login_ip
 * @property integer $login_time
 * @property string $act_ip
 * @property integer $act_time
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereRegIp($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereRegTime($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereLoginIp($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereLoginTime($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereActIp($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereActTime($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereUserGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AmaotoUser wherePower($value)
 */
class AmaotoUser extends Eloquent implements UserInterface, RemindableInterface
{

    use UserTrait, RemindableTrait, SoftDeletingTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

    public function isAdmin()
    {
        return $this->power >= 99999 ? true : false;
    }

    public function updateAct()
    {
        $this->act_ip   = Input::getClientIp();
        $this->act_time = time();
        $this->save();
    }

    public function updateLogin()
    {
        $this->login_ip   = Input::getClientIp();
        $this->login_time = time();
        $this->act_ip     = Input::getClientIp();
        $this->act_time   = time();
        $this->save();
    }
}