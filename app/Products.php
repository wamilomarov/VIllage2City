<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 14-Dec-17
 * Time: 17:57
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Products extends Model
{
    protected $fillable = ['name', 'description', 'min_quantity', 'price'];

    protected $hidden = ['category_id', 'unit_id', 'vendor_id'];

    protected $appends = ['category', 'unit', 'rating'];

    public function getCategoryAttribute()
    {
        return Category::find($this->category_id);
    }

    public function getUnitAttribute()
    {
        return DB::table('units')->where('id', $this->unit_id)->first();
    }

    public function getRatingAttribute()
    {
        return DB::table('products_ratings')->where('product_id', $this->id)->get()->avg('rating');
    }

    public function vendor()
    {
        return $this->belongsToMany('App/Vendor', 'vendors_products');
    }

}