<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\profile;
use App\Models\Contract;
use DateTime;
use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

class UserController extends Controller
{
    public function register(Request $request)
    {

        $fields = $request->validate([
            // User
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
            'role' => 'required|string|in:ADMIN,SELLER,SHIPPER,BUYER',
            // // Profile
            // 'TEN' => 'string',
            // 'CMND' => 'string|regex:/^[0-9]+$/',
            // 'SDT' => 'string|regex:/^[0-9]+$/',
            // 'NGAY_SINH' => 'date|before_or_equal:now',
            // 'DIA_CHI' => 'string',
        ]);
        // $user = null;
        // $profile = null;
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $fields['role']
        ]);

        // try {
        //     $user = User::create([
        //         'name' => $fields['name'],
        //         'email' => $fields['email'],
        //         'password' => bcrypt($fields['password']),
        //         'role' => $fields['role']
        //     ]);
        //     $profile = profile::create([
        //         'TEN' => $fields['TEN'],
        //         'CMND' => $fields['CMND'],
        //         'SDT' => $fields['SDT'],
        //         'NGAY_SINH' => date("Y-m-d", strtotime($fields['NGAY_SINH'])),
        //         'DIA_CHI' => $fields['DIA_CHI'],
        //         'id' => $user->id,
        //         'VAI_TRO' => $user->role
        //     ]);
        // } catch (\Exception $er) {
        //     if ($user != null && !empty($user))
        //         $user->delete();
        //     if ($profile != null && !empty($profile))
        //         $profile->delete();
        //     throw $er;
        // }



        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            // 'profile' => $profile,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {

        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        // check contract
        $builder = Contract::query();
        $builder->where('id', '=', $user['id']);
        $builder->where('HOP_DONG_DA_XET_DUYET', '=', 1);
        $now = (new DateTime())->format('Y-m-d');
        $builder->where('NGAY_HIEU_LUC', '>=', $now);
        $builder->where('NGAY_KET_THUC', '>=', $now);
        $contract = $builder->first();
        $has_contract = null;
        // if (count($contract) < 0)
        if (empty($contract) || $contract == null)
            $has_contract = false;
        else
            $has_contract = true;
        $response = [
            'user' => $user,
            'token' => $token,
            'has_contract' =>   $has_contract,
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
    public function currentUser(Request $request)
    {
        $user = auth()->user();
        if (empty($user) ||  $user == null)
            return null;
        return $user;
    }
    public function UserExist($id)
    {
        $user = User::find($id);
        if ($user != null) {
            return response(['name' => $user->name, 'role' => $user->role], 201);
        }
        return null;
    }
    public function DropUser($id)
    {
        $user = User::find($id);
        return User::destroy($id);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // current user
        $user = auth()->user();
        $rule = [
            'TEN' => 'string|nullable',
            'CMND' => 'string|regex:/^[0-9]+$/|nullable',
            'SDT' => 'string|regex:/^[0-9]+$/|nullable',
            'NGAY_SINH' => 'date|before_or_equal:now|nullable',
            'DIA_CHI' => 'string|nullable',
        ];

        $id = null;
        if (empty($user) ||  $user == null) {
            $rule['id'] = 'required|numeric';
        }

        $fields = $request->validate($rule);

        if (empty($user) && $user == null && $fields['id'] != null && !empty($fields['id'])) {
            $id = $fields['id'];
        } else {
            $id = $user->id;
        }
        $new = ['id' => $id];
        foreach ($fields as $field => $value) {
            if ($field == 'NGAY_SINH')
                $new[$field] = date("Y-m-d", strtotime($fields[$field]));
            $new[$field] = $value;
        }
        // $new = [
        //     'id' => $id,
        //     'TEN' => $fields['TEN'],
        //     'CMND' => $fields['CMND'],
        //     'SDT' => $fields['SDT'],
        //     'NGAY_SINH' => date("Y-m-d", strtotime($fields['NGAY_SINH'])),
        //     'DIA_CHI' => $fields['DIA_CHI'],
        // ];
        $profile = profile::create($new);
        return $profile->find($id);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = null)
    {
        //
        $user = auth()->user();
        if ((empty($user) || $user == null) && $id == null)
            throw new Error('id trắng');

        if (!empty($user) &&  $user != null) {
            if ($user->role == 'ADMIN' && $id == null) {
                return profile::all();
            }
            if ($user->role != 'ADMIN') {
                $id = $user->id;
            }
        }
        return profile::find($id);
    }
    /**
     * Search the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        //
        $user = auth()->user();
        if (!empty($user) &&  $user != null && $user->role == 'ADMIN') {
            throw new Error('Không có quyền');
        }

        $builder = profile::query();
        $term = $request->all();
        if (!empty($term['TEN'])) {
            $builder->where('TEN', 'like', '%' . $term['TEN'] . '%');
        }
        if (!empty($term['CMND'])) {
            $builder->where('CMND', 'like', '%' . $term['CMND'] . '%');
        }
        if (!empty($term['SDT'])) {
            $builder->where('SDT', 'like', '%' . $term['SDT'] . '%');
        }
        if (!empty($term['NGAY_SINH'])) {
            $builder->where('NGAY_SINH', '=', $term['NGAY_SINH']);
        }
        if (!empty($term['DIA_CHI'])) {
            $builder->where('DIA_CHI', 'like', '%' . $term['DIA_CHI'] . '%');
        }
        return $builder->get();
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null)
    {
        //
        $user = auth()->user();
        if (!empty($user) &&  $user != null) {
            $id = $user->id;
        }
        if ($id == null)
            throw new Error('id trắng');

        $fields = $request->validate([
            'TEN' => 'string|nullable',
            'CMND' => 'string|regex:/^[0-9]+$/|nullable',
            'SDT' => 'string|regex:/^[0-9]+$/|nullable',
            'NGAY_SINH' => 'date|before_or_equal:now|nullable',
            'DIA_CHI' => 'string|nullable',
        ]);
        $updated = [];
        if (!empty($fields['TEN'])) {
            $updated['TEN'] = $fields['TEN'];
        }
        if (!empty($fields['CMND'])) {
            $updated['CMND'] = $fields['CMND'];
        }
        if (!empty($fields['SDT'])) {
            $updated['SDT'] = $fields['SDT'];
        }
        if (!empty($fields['NGAY_SINH'])) {
            $updated['NGAY_SINH'] = date("Y-m-d", strtotime($fields['NGAY_SINH']));
        }
        if (!empty($fields['DIA_CHI'])) {
            $updated['DIA_CHI'] = $fields['DIA_CHI'];
        }

        $profile = profile::find($id);
        if (empty($profile) || $profile == null)
            throw new Error('profile không tồn tại');

        $profile->update($updated);
        return $profile;
    }
}
