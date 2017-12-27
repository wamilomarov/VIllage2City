<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 15-Dec-17
 * Time: 03:59
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Request extends Model
{
    protected $fillable = ['request_date', 'response_date', 'complete_date', 'is_completed', 'is_agreed'];

    protected $hidden = ['customer_id', 'vendor_id'];

    protected $appends = ['customer', 'vendor'];

    public function getCustomerAttribute()
    {
        return Customer::find($this->customer_id);
    }

    public function getVendorAttribute()
    {
        return Vendor::find($this->vendor_id);
    }

    public function products()
    {
        return $this->belongsToMany('App/Product', 'requests_products')
            ->withPivot([
                'requested_amount',
                'responded_amount',
                'requested_price',
                'responded_price',
                'requested_date',
                'responded_date'
            ]);
    }

    public function currentLocation()
    {
        return DB::table('tracking')->where('request_id', $this->id)->last();
    }

    public function locationTracking()
    {
        return DB::table('tracking')->where('request_id', $this->id)->get();
    }


}