<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use App\Models\profile;
use DateTime;
use Error;

class ContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $user = auth()->user();
        if (empty($user) || $user == null || $user['role'] == 'ADMIN') {
            return contract::orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->get();
        } 
        return contract::where('id', '=', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->get();
        
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
        // rules
        $rules = null;
        if (empty($user) || $user == null) {
            $rules = [
                'NGAY_KY' => 'date|after_or_equal:now',
                'id' => 'required|numeric',
                'NGAY_HIEU_LUC' => 'date|after_or_equal:NGAY_KY',
                'NGAY_KET_THUC' => 'date|after:NGAY_HIEU_LUC',
                'GIAY_CHUNG_NHAN_AN_TOAN' => 'string',
                'GIAY_PHEP_KINH_DOANH' => 'string',
                'HOP_DONG_DA_XET_DUYET' => 'boolean',
            ];
        } else {
            $rules = ['NGAY_KY' => 'date|after_or_equal:now'];
            switch ($user['role']) {
                case 'ADMIN':
                    $rules['id'] = 'required|numeric';
                    // $rules['NGAY_KY'] = 'date';
                    $rules['NGAY_HIEU_LUC'] = 'date|after_or_equal:NGAY_KY';
                    $rules['NGAY_KET_THUC'] = 'date|after:NGAY_HIEU_LUC';
                    $rules['GIAY_CHUNG_NHAN_AN_TOAN'] = 'string';
                    $rules['GIAY_PHEP_KINH_DOANH'] = 'string';
                    $rules['HOP_DONG_DA_XET_DUYET'] = 'boolean';
                    break;
                case 'SELLER':
                    $rules['GIAY_CHUNG_NHAN_AN_TOAN'] = 'string';
                    $rules['GIAY_PHEP_KINH_DOANH'] = 'string';
                    break;
                case 'BUYER':
                    throw new Error('Người dùng không có quyền');
                    break;
                default:
                    break;
            }
        }

        // Get fields
        $fields = $request->validate($rules);
        $exists = null;
        $contract = null;

        if (empty($user) || $user == null || $user->role == 'ADMIN') {
            $exists = User::find($fields['id']);
            if (empty($exists) || $exists == null) {
                return new Error('User không tồn tại');
            } else {
                if ($exists->role == 'ADMIN' || $exists->role == 'BUYER') {
                    throw new Error('không thể tạo contract cho user nhập');
                }
            }
        } else {
            $exists = $user;
        }
        $fields['LOAI'] = $exists['role'];
        $fields['id'] = $exists['id'];
        if (!empty($fields['NGAY_KY']) && $fields['NGAY_KY'] != null)
            $fields['NGAY_KY'] =  date("Y-m-d", strtotime($fields['NGAY_KY']));
        if (!empty($fields['NGAY_HIEU_LUC']) && $fields['NGAY_HIEU_LUC'] != null)
            $fields['NGAY_HIEU_LUC'] =  date("Y-m-d", strtotime($fields['NGAY_HIEU_LUC']));
        if (!empty($fields['NGAY_KET_THUC']) && $fields['NGAY_KET_THUC'] != null)
            $fields['NGAY_KET_THUC'] =  date("Y-m-d", strtotime($fields['NGAY_KET_THUC']));
        if (!empty($fields['HOP_DONG_DA_XET_DUYET']) && $fields['HOP_DONG_DA_XET_DUYET'] == true && empty($fields['NGAY_KY']) && empty($fields['NGAY_HIEU_LUC']) && empty($fields['NGAY_KET_THUC'])) {
            //$fields['NGAY_KET_THUC'] =  date("Y-m-d", strtotime($fields['NGAY_KET_THUC']));
            $start = date('Y-m-d');
            $fields['NGAY_KY'] = $start;
            $fields['NGAY_HIEU_LUC'] = date("Y-m-d", strtotime($start . "+ 7 days"));
            $fields['NGAY_KET_THUC'] = date("Y-m-d", strtotime($fields['NGAY_HIEU_LUC'] . "+ 5 years"));
        }

        $contract = Contract::create($fields);
        return  Contract::find($contract['MA_HOP_DONG']);
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
        $contract = contract::find($id);
        $contract['TEN_NGUOI_DUNG'] = user::find($contract['id'])['name'];
        $user = auth()->user();
        if (empty($user) || $user == null || $user->role == 'ADMIN') {
            $response = [
                'contract' => $contract,
                'date' => new DateTime()
            ];

            return response($response, 201);
            return $contract;
        }

        if ($contract['id'] != $user['id'])
            return null;

        return $contract;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        // current user

        $user = auth()->user();

        // rules
        $rules = null;
        if (empty($user) || $user == null) {
            $rules = [
                'NGAY_KY' => 'date|after_or_equal:now',
                // 'id' => 'required|numeric',
                'NGAY_HIEU_LUC' => 'date|after_or_equal:NGAY_KY',
                'NGAY_KET_THUC' => 'date|after:NGAY_HIEU_LUC',
                'GIAY_CHUNG_NHAN_AN_TOAN' => 'string',
                'GIAY_PHEP_KINH_DOANH' => 'string',
                'HOP_DONG_DA_XET_DUYET' => 'boolean',
            ];
        } else {
            $rules = ['NGAY_KY' => 'date|after_or_equal:now'];
            switch ($user->role) {
                case 'ADMIN':
                    //$rules['id'] = 'required|numeric';
                    // $rules['NGAY_KY'] = 'date';
                    $rules['NGAY_HIEU_LUC'] = 'date|after_or_equal:NGAY_KY';
                    $rules['NGAY_KET_THUC'] = 'date|after:NGAY_HIEU_LUC';
                    $rules['GIAY_CHUNG_NHAN_AN_TOAN'] = 'string';
                    $rules['GIAY_PHEP_KINH_DOANH'] = 'string';
                    $rules['HOP_DONG_DA_XET_DUYET'] = 'boolean';
                    break;
                case 'SELLER':
                    $rules['GIAY_CHUNG_NHAN_AN_TOAN'] = 'string';
                    $rules['GIAY_PHEP_KINH_DOANH'] = 'string';
                    break;
                case 'BUYER':
                    throw new Error('Người dùng không có quyền');
                    break;
                default:
                    break;
            }
        }
        $contract = Contract::find($id);
        if (empty($contract) || $contract == null) {
            throw new Error('contract không tồn tại');
        }
        // Get fields
        $fields = $request->validate($rules);
        if (!empty($fields['NGAY_KY']) && $fields['NGAY_KY'] != null)
            $fields['NGAY_KY'] =  date("Y-m-d", strtotime($fields['NGAY_KY']));
        if (!empty($fields['NGAY_HIEU_LUC']) && $fields['NGAY_HIEU_LUC'] != null)
            $fields['NGAY_HIEU_LUC'] =  date("Y-m-d", strtotime($fields['NGAY_HIEU_LUC']));
        if (!empty($fields['NGAY_KET_THUC']) && $fields['NGAY_KET_THUC'] != null)
            $fields['NGAY_KET_THUC'] =  date("Y-m-d", strtotime($fields['NGAY_KET_THUC']));
        if (!empty($fields['HOP_DONG_DA_XET_DUYET']) && $fields['HOP_DONG_DA_XET_DUYET'] == true) {
            //$fields['NGAY_KET_THUC'] =  date("Y-m-d", strtotime($fields['NGAY_KET_THUC']));
            if (empty($fields['NGAY_KY']) && empty($fields['NGAY_HIEU_LUC']) && empty($fields['NGAY_KET_THUC'])) {
                $start = date('Y-m-d');
                $fields['NGAY_KY'] = $start;
                $fields['NGAY_HIEU_LUC'] = date("Y-m-d", strtotime($start . "+ 7 days"));
                $fields['NGAY_KET_THUC'] = date("Y-m-d", strtotime($fields['NGAY_HIEU_LUC'] . "+ 5 years"));
            }
        } else {
            $fields['NGAY_HIEU_LUC'] = null;
            $fields['NGAY_KET_THUC'] = null;
        }



        if (!empty($user) && $user != null && $user['role'] != 'ADMIN' && $user['id'] != $contract['id']) {
            throw new Error('không có quyền chỉnh sửa contract không phải của mình');
        }
        $contract->update($fields);
        return $contract;
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
        $user = auth()->user();

        if (empty($user) || $user == null || $user->role == 'ADMIN') {
            return Contract::destroy($id);;
        } else {
            throw new Error('không có quyền');
        }
        $contract = contract::find($id);
        if ($contract['id'] != $user['id'])
            throw new Error('không có quyền xóa contract không phải của mình');
        return Contract::destroy($id);
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
        $builder = Contract::query();
        $term = $request->all();
        if (!empty($term['LOAI'])) {
            $builder->where('LOAI', 'like', $term['LOAI']);
        }
        if (!empty($term['NGAY_KY'])) {
            $builder->where('NGAY_KY', '=', date("Y-m-d", strtotime($term['NGAY_KY'])));
        }
        if (!empty($term['NGAY_HIEU_LUC'])) {
            $builder->where('NGAY_HIEU_LUC', '=', date("Y-m-d", strtotime($term['NGAY_HIEU_LUC'])));
        }
        if (!empty($term['NGAY_KET_THUC'])) {
            $builder->where('NGAY_KET_THUC', '=', date("Y-m-d", strtotime($term['NGAY_KET_THUC'])));
        }
        if (empty($user) || $user == null || $user->role == 'ADMIN') {
            if (!empty($term['id'])) {
                $builder->where('id', '=', $term['id']);
            }
        } else {
            $builder->where('id', '=', $user['id']);
        }
        if (!empty($term['HOP_DONG_DA_XET_DUYET'])) {
            $builder->where('HOP_DONG_DA_XET_DUYET', '=', $term['HOP_DONG_DA_XET_DUYET']);
        }
        return $builder->get();
    }
}
