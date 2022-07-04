<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\profile;
use Error;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            // Profile
            'TEN' => 'required|string',
            'CMND' => 'required|string|regex:/^[0-9]+$/',
            'SDT' => 'required|string|regex:/^[0-9]+$/',
            'NGAY_SINH' => 'required|date|before_or_equal:now',
            'DIA_CHI' => 'required|string',
        ]);
        $user = null;
        $profile = null;
        try {
            $user = User::create([
                'name' => $fields['name'],
                'email' => $fields['email'],
                'password' => bcrypt($fields['password']),
                'role' => $fields['role']
            ]);
            $profile = profile::create([
                'TEN' => $fields['TEN'],
                'CMND' => $fields['CMND'],
                'SDT' => $fields['SDT'],
                'NGAY_SINH' => date("Y-m-d", strtotime($fields['NGAY_SINH'])),
                'DIA_CHI' => $fields['DIA_CHI'],
                'id' => $user->id,
                'VAI_TRO' => $user->role
            ]);
        } catch (\Exception $er) {
            if ($user != null && !empty($user))
                $user->delete();
            if ($profile != null && !empty($profile))
                $profile->delete();
            throw $er;
        }



        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'profile' => $profile,
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

        $response = [
            'user' => $user,
            'token' => $token
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
        $user['MA_NGUOI_DUNG'] = profile::where('id', '=', $user->id)->get()[0]['MA_NGUOI_DUNG'];
        return $user;
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
        if (!empty($term['VAI_TRO'])) {
            $builder->where('VAI_TRO', 'like', '%' . $term['VAI_TRO'] . '%');
        }
        return $builder->get();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        return profile::where('id', '=', $user->id)->get()[0];
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if ($id == null)
            $id = $user->id;

        $fields = $request->validate([
            'TEN' => 'string',
            'CMND' => 'string|regex:/^[0-9]+$/',
            'SDT' => 'string|regex:/^[0-9]+$/',
            'NGAY_SINH' => 'date|before_or_equal:now',
            'DIA_CHI' => 'string',
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
            $updated['NGAY_SINH'] = $fields['NGAY_SINH'];
        }
        if (!empty($fields['DIA_CHI'])) {
            $updated['DIA_CHI'] = $fields['DIA_CHI'];
        }
        if (count($updated) <= 0)
            throw new Error('Input rỗng');
        profile::where('id', '=', $id)->limit(1)->update($updated);
        return profile::where('id', '=', $id)->get();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
