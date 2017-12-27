<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 14-Dec-17
 * Time: 13:15
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['longitude', 'latitude'];

    protected $hidden = ['user_id'];

    protected $appends = ['user'];

    public function getUserAttribute()
    {
        return User::find($this->user_id);
    }

    public function requests()
    {
        return $this->hasMany('App/Request');
    }
}