<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Models\User;
use App\Models\profile;
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


        if ($user->role == 'ADMIN') {
            return contract::orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->get();
        }
        $profile = profile::where('id', '=', $user->id)->limit(1)->get()[0];
        return  contract::where('MA_NGUOI_DUNG', '=', $profile['MA_NGUOI_DUNG'])->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->get();
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
        $rules = [
            'NGAY_KY' => 'date',
            'NGAY_HIEU_LUC' => 'date',
            'NGAY_KET_THUC' => 'date|after_or_equal:now',
            'GIAY_CHUNG_NHAN_AN_TOAN' => 'string',
            'GIAY_PHEP_KINH_DOANH' => 'string',
        ];
        switch ($user->role) {
            case 'ADMIN':
                $rules['MA_NGUOI_DUNG'] = 'required|string';
                break;
            case 'BUYER':
                throw new Error('Người dùng không có quyền');
                break;
            default:
                break;
        }
        // Get fields

        $fields = $request->validate($rules);
        $exists = null;
        $contract = null;


        if ($user->role == 'ADMIN') {
            $exists = profile::where('MA_NGUOI_DUNG', '=', $fields['MA_NGUOI_DUNG'])->limit(1)->get();
            if (empty($exists) || $exists == null || count($exists) <= 0)
                throw new Error('Người dùng không tồn tại');
            $exists = $exists[0];
            if (!empty($fields['NGAY_KY']) && $fields['NGAY_KY'] != null)
                $contract['NGAY_KY'] = $fields['NGAY_KY'];
            if (!empty($fields['NGAY_HIEU_LUC']) && $fields['NGAY_HIEU_LUC'] != null)
                $contract['NGAY_HIEU_LUC'] = $fields['NGAY_HIEU_LUC'];
            if (!empty($fields['NGAY_KET_THUC']) && $fields['NGAY_KET_THUC'] != null)
                $contract['NGAY_KET_THUC'] = $fields['NGAY_KET_THUC'];
        } else {
            $exists = profile::where('id', '=', $user->id)->limit(1)->get()[0];
        }

        $contract['LOAI'] = $exists['VAI_TRO'];
        $contract['MA_NGUOI_DUNG'] = $exists['MA_NGUOI_DUNG'];



        switch ($contract['LOAI']) {
            case 'SELLER':
                if (!empty($fields['GIAY_CHUNG_NHAN_AN_TOAN']) && $fields['GIAY_CHUNG_NHAN_AN_TOAN'] != null)
                    $contract['GIAY_CHUNG_NHAN_AN_TOAN'] = $fields['GIAY_CHUNG_NHAN_AN_TOAN'];
                if (!empty($fields['GIAY_PHEP_KINH_DOANH']) && $fields['GIAY_PHEP_KINH_DOANH'] != null)
                    $contract['GIAY_PHEP_KINH_DOANH'] = $fields['GIAY_PHEP_KINH_DOANH'];
                break;
            case 'SHIPPER':
                break;
            default:
                throw new Error('Loại hợp đồng chưa xác nhận');
                break;
        }

        $contract = Contract::create($contract);
        return $contract;
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
        $user = auth()->user();
        if ($user->role == 'ADMIN') {
            return contract::find($id);
        }

        $profile = profile::where('id', '=', $user->id)->limit(1)->get()[0];
        $find = contract::where('MA_NGUOI_DUNG', '=', $profile['MA_NGUOI_DUNG'])->where('MA_HOP_DONG', '=', $id)->limit(1)->get();
        if (count($find) <= 0)
            $find = null;
        else
            $find = $find[0];
        return $find;
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
        // if (!empty($term['TEN'])) {
        //     $builder->where('TEN', 'like', '%' . $term['TEN'] . '%');
        // }
        // if (!empty($term['CMND'])) {
        //     $builder->where('CMND', 'like', '%' . $term['CMND'] . '%');
        // }
        // if (!empty($term['SDT'])) {
        //     $builder->where('SDT', 'like', '%' . $term['SDT'] . '%');
        // }
        // if (!empty($term['NGAY_SINH'])) {
        //     $builder->where('NGAY_SINH', '=', $term['NGAY_SINH']);
        // }
        // if (!empty($term['DIA_CHI'])) {
        //     $builder->where('DIA_CHI', 'like', '%' . $term['DIA_CHI'] . '%');
        // }
        // if (!empty($term['VAI_TRO'])) {
        //     $builder->where('VAI_TRO', 'like', '%' . $term['VAI_TRO'] . '%');
        // }
        return $builder->get();
    }
}
