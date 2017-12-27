<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'facebook_id'
    ];

    public function uploadPhoto(UploadedFile $file, $folder)
    {
        $name = time() . '-' . $file->getFilename();
        $extension = $file->getClientOriginalExtension();
        $name = $name . '.' . $extension;
        $file->move($folder, $name);
        return $name;
    }

    public function addPermissions($permissions = [])
    {
        $array = [];
        foreach ($permissions as $permission)
        {
            $array[] = [
                'user_id' => $this->id,
                'role_id' => DB::table('roles')->where('key', $permission)->first()->id
            ];
        }
        DB::table('user_roles')->insert($array);
    }

    public function removePermission($permissions = [])
    {

        foreach ($permissions as $permission)
        {
            $array = [
                ['user_id', '=', $this->id],
                ['role_id', 'LIKE', DB::table('roles')->where('key', $permission)->first()->id]
            ];
            DB::table('user_roles')->where($array)->delete();
        }

    }

    public function hasPermission($permissions = [])
    {
        $array = [];
        foreach ($permissions as $permission)
        {
            $array[] = [
                'user_id' => $this->id,
                'role_id' => DB::table('roles')->where('key', $permission)->first()->id
            ];
        }

        return DB::table('user_roles')->where($array)->count() == 0 ? false : true;
    }

}
