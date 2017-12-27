<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 14-Dec-17
 * Time: 20:47
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = ['name', 'photo', 'latitude', 'longitude'];


    public function products()
    {
        return $this->belongsToMany('App/Product', 'vendors_products')
            ->withPivot('amount', 'price')
            ->where('amount', '>', 0);
    }





}