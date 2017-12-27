<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 14-Dec-17
 * Time: 20:52
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    protected $hidden = ['user_id', 'vendor_id'];

    protected $appends = ['user', 'vendor'];

    public function getUserAttribute()
    {
        return User::find($this->user_id);
    }

    public function getVendorAtribute()
    {
        return Vendor::find($this->vendor_id);
    }


}