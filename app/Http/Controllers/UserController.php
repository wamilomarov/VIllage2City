<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 15-Dec-17
 * Time: 13:13
 */

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $user = new User();
        $user->photo = $user->uploadPhoto($request->file('photo'), 'uploads/photos');
        $user->name = $request->get('name');
        $user->username = $request->get('username');
        $user->password = sha1($request->get('password'));
        $user->phone = $request->get('phone');
        $user->save();

        return response()->json(['status' => 200, 'data' => $user]);
    }

}