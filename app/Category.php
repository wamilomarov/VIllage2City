<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 14-Dec-17
 * Time: 12:40
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany('App/Product');
    }
}
