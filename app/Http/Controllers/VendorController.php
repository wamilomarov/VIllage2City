<?php
/**
 * Created by PhpStorm.
 * User: wamil
 * Date: 17-Dec-17
 * Time: 20:57
 */

namespace App\Http\Controllers;


use App\StaffMember;
use App\User;
use App\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function create(Request $request)
    {
        $vendor = new Vendor();
        $vendor->photo = $vendor->uploadPhoto($request->file('photo'), 'uploads/photos/');
        $vendor->name = $request->get('name');
        $vendor->latitude = $request->get('latitude');
        $vendor->longitude = $request->get('longitude');
        $vendor->save();

        $vendor = Vendor::find($vendor->id);

        return response()->json(['status' => 200, 'data' => $vendor]);
    }

    public function login(Request $request)
    {
        if ($request->has('facebook_access_token'))
        {
            $user_details = "https://graph.facebook.com/me?fields=id,name,email&access_token=" .$request->get('facebook_access_token');

            $response = file_get_contents($user_details);
            $fb_user = json_decode($response);

            $user = User::where('facebook_id', $fb_user->id)->orWhere('email', $fb_user->email)->first();

            if (!$user)
            {
                $user = new User();
            }
            $user->facebook_id = $fb_user->id;
            $user->name = $fb_user->name;
            $user->email = $fb_user->email;
            $user->save();

        }
        else
        {
            $email = $request->get('email');
            $password = sha1($request->get('password'));

            $user = User::where('email', $email)->where('password', $password)->first();
        }


        if ($user)
        {
            $vendor = StaffMember::where('user_id', $user->id)->first();
            if ($vendor)
            {
                $vendor->api_token = md5(microtime());
                $vendor->save();
                $vendor->makeVisible('api_token');
                return response()->json(['status' => 200, 'data' => $vendor]);
            }
            else
            {
                return response()->json(['status' => 402]);
            }
        }
        else
        {
            return response()->json(['status' => 402]);
        }
    }

    public function logout()
    {
        $staffMember = Auth::user();
        $staffMember->api_token = null;
        $staffMember->save();
        return response()->json(['status' => 200]);
    }

    public function getRequests(Request $request)
    {
        $staffMember = Auth::user();

        $operator = '=';
        $is_completed = 0;
        if ($request->has('type'))
        {
            if ($request->get('type') == 'shipped')
            {
                $operator = '>';
            }
            if ($request->get('type') == 'completed')
            {
                $operator = '>';
                $is_completed = 1;
            }
        }

        $reqs = \App\Request::where('requests.vendor_id', $staffMember->vendor->id)
            ->where(DB::raw('(SELECT COUNT(id) FROM tracking WHERE request_id = requests.id)'), $operator,0)
            ->where('is_completed', $is_completed)
            ->get();

        return response()->json(['status' => 200, 'data' => $reqs]);
    }

    public function respond(Request $request)
    {
        if ($request->has('responses'))
        {
            foreach ($request->get('responses') as $item)
            {
                $req = \App\Request::find($item['request_id']);
                $req->registerResponse($item);
                $req->response_date = DB::raw('NOW()');
                $req->save();
            }
            return response()->json(['status' => 200]);
        }
        else
        {
            return response()->json(['status' => 403]);
        }
    }

    public function send(Request $request)
    {
        $id = $request->get('id');

        $req = \App\Request::find($id);

        $product_count = $req->product->min_quantity * $req->responded_amount;

        DB::table('tracking')->insert(
            [
                'request_id' => $id,
                'latitude' => 45.0001,
                'longitude' => 45.1000,
                'created_at' => DB::raw("NOW()"),
                'updated_at' => DB::raw("NOW()")
            ]
        );

        DB::table('vendors_products')
        ->where('product_id', $req->product->id)
        ->update(
            [
                'amount' => DB::raw("amount - $product_count")
            ]
        );

        return response()->json(['status' => 200]);
    }

    public function addCheckPoint(Request $request)
    {
        $id = $request->get('id');
        $count = DB::table('tracking')
            ->where('request_id', $id)
            ->count();
        if ($count < 5)
        {
            DB::table('tracking')->insert(
                [
                    'request_id' => $id,
                    'latitude' => 45.0001,
                    'longitude' => 45.1000,
                    'created_at' => DB::raw("NOW()"),
                    'updated_at' => DB::raw("NOW()")
                ]
            );
        }
        $resultCount = DB::table('tracking')
            ->where('request_id', $id)
            ->count();
        if ($resultCount == 5)
        {
            DB::table('requests')
                ->where('id', $id)
            ->update(
                [
                    'is_completed' => 1
                ]
            );
        }
        return response()->json(['status' => 200]);

    }

    public function complete(Request $request)
    {
        $id = $request->get('id');
        DB::table('requests')
            ->where('id', $id)
            ->update(
                [
                    'is_completed' => 1
                ]
            );

        $count = DB::table('tracking')
            ->where('request_id', $id)
            ->count();
        if ($count < 5)
        {
            for ($i = 1; $i < (5-$count); $i++)
            {
                DB::table('tracking')->insert(
                    [
                        'request_id' => $id,
                        'latitude' => 45.0001,
                        'longitude' => 45.1000,
                        'created_at' => DB::raw("NOW()"),
                        'updated_at' => DB::raw("NOW()")
                    ]
                );
            }

        }
        return response()->json(['status' => 200]);
    }

    public function getStock()
    {
        $staffMember = Auth::user();
        $vendor = Vendor::find($staffMember->vendor->id);
        return response()->json(['status' => 200., 'data' => ['vendor' => $staffMember->vendor, 'stock' => $vendor->products]]);
    }
}