<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 15-Dec-17
 * Time: 16:37
 */

namespace App\Http\Controllers;


use App\Customer;
use App\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create(Request $request)
    {
        $customer = new Customer();
        $customer->latitude = $request->get('latitude');
        $customer->longitude = $request->get('longitude');
        $customer->user_id = $request->get('user_id');
        $customer->save();

        $customer = Customer::find($customer->id);

        return response()->json(['status' => 200, 'data' => $customer]);
    }

    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = sha1($request->get('password'));

        $user = User::where('username', $username)->where('password', $password)->first();

        if ($user)
        {

        }
        else
        {
            return response()->json(['status' => 402]);
        }
    }
}