<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order;
use Illuminate\Support\Facades\Http;
use Error;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }
        // else 
        //     throw new Error('thiếu token');

        $rules = [
            'MA_CUA_HANG' => 'required|numeric',
            'DIA_CHI' => 'required|string',
            'SDT' => 'required|string|regex:/^[0-9]+$/'
        ];

        if ($CurUser == null) {
            $rules['MA_NGUOI_DUNG'] = 'required|numeric';
        }

        $fields = $request->validate($rules);

        if ($CurUser != null) {
            $fields['MA_NGUOI_DUNG'] = $CurUser['id'];
        }
        $fields['TRANG_THAI'] = 'chuẩn bị';

        $store = Http::withHeaders([
            'accept' => 'application/json',
        ])->get('http://localhost:8002/api/store/' . $fields['MA_CUA_HANG'])->json();
        if (empty($store) || $store == null)
            throw new Error('store không tồn tại');

        $order = order::create($fields);

        return order::find($order->MA_DON_HANG);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }
        // else 
        //     throw new Error('thiếu token');

        $order = order::find($id);
        if ($CurUser != null) {
            if ($order['MA_NGUOI_DUNG'] != $CurUser['id'] && $order['MA_CUA_HANG'] != $CurUser['id'])
                throw new Error('không có quyền');
        }

        return $order;
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
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }
        // else 
        //     throw new Error('thiếu token');

        $order = order::find($id);
        if ($CurUser != null) {
            if ($CurUser['id'] != $order['MA_NGUOI_DUNG'] && $CurUser['id'] != $order['MA_CUA_HANG']) {
                if ($CurUser['role'] != 'ADMIN' && $CurUser['role'] != 'SHIPPER')
                    throw new Error('không có quyền');
            }
        }
        $rules = null;
        if ($CurUser != null) {
            $rules = [];
            switch ($CurUser['role']) {
                case 'BUYER':
                    $rules['DIA_CHI'] = 'string';
                    $rules['SDT'] = 'string|regex:/^[0-9]+$/';
                    break;
                case 'SELLER':
                    $rules['TRANG_THAI'] = 'string|in:chuẩn bị,chờ giao hàng';
                    break;
                case 'SHIPPER':
                    $rules['SHIPPER_ACTION'] = 'string|in:nhận,hủy';
                    $rules['TRANG_THAI'] = 'string|in:đã giao';
                    break;
                case 'ADMIN':
                    $rules['MA_SHIPPER'] = 'numeric|nullable';
                    $rules['TRANG_THAI'] = 'string|in:chuẩn bị,chờ giao hàng,giao hàng,hủy';
                    break;
                default:
                    break;
            }
        } else {
            $rules = [
                'MA_NGUOI_DUNG' => 'numeric',
                'MA_CUA_HANG' => 'numeric',
                /////////////////////////////////
                'MA_SHIPPER' => 'numeric|nullable',
                'DIA_CHI' => 'string',
                'SDT' => 'string|regex:/^[0-9]+$/',
                'TRANG_THAI' => 'string|in:chuẩn bị,chờ giao hàng,giao hàng,đã giao,hủy',
            ];
        }

        $fields = $request->validate($rules);
        if (array_key_exists('MA_SHIPPER', $fields)) {
            if ($fields['MA_SHIPPER'] != null) {
                $shipper = Http::withHeaders([
                    'accept' => 'application/json',
                ])->get('http://localhost:8002/api/existuser/' . $fields['MA_SHIPPER'])->json();
                if (empty($shipper) || $shipper == null)
                    throw new Error('shippier không tồn tại');
                else {
                    if ($shipper['role'] != 'SHIPPER')
                        throw new Error('không phải shipper');
                }
            }
        }
        if ($CurUser == null) {
            $store = Http::withHeaders([
                'accept' => 'application/json',
            ])->get('http://localhost:8002/api/store/' . $fields['MA_CUA_HANG'])->json();
            if (empty($store) || $store == null)
                throw new Error('store không tồn tại');

            $buyer = Http::withHeaders([
                'accept' => 'application/json',
            ])->get('http://localhost:8002/api/existuser/' . $fields['MA_NGUOI_DUNG'])->json();
            if (empty($buyer) || $buyer == null)
                throw new Error('buyer{MA_NGUOI_DUNG} không tồn tại');
            else {
                if ($buyer['role'] != 'BUYER')
                    throw new Error('không phải buyer');
            }
        } else {
            if ($CurUser['role'] == 'SHIPPER' && array_key_exists('SHIPPER_ACTION', $fields)) {
                if ($fields['SHIPPER_ACTION'] == 'nhận') {
                    if ($order['TRANG_THAI'] != 'chờ giao hàng')
                        throw new Error('order đã có người giao hoặc đang chuẩn bị');
                    $fields['MA_SHIPPER'] = $CurUser['id'];
                    $fields['TRANG_THAI'] = 'giao hàng';
                } else if ($fields['SHIPPER_ACTION'] == 'hủy' && $order['TRANG_THAI'] == 'giao hàng') {
                    $fields['MA_SHIPPER'] = null;
                    $fields['TRANG_THAI'] = 'chờ giao hàng';
                }
            }
            if ($CurUser['role'] == 'SELLER' && $order['TRANG_THAI'] != 'chuẩn bị' && $order['TRANG_THAI'] != 'chờ giao hàng') {
                throw new Error('order đang giao hoặc đã giao hoặc hủy');
            }
        }
        if (array_key_exists('TRANG_THAI', $fields)) {
            if ($order['TRANG_THAI'] == 'đã giao' || $order['TRANG_THAI'] == 'hủy') {
                throw new Error('order đã giao hoặc hủy');
            }
            if (array_key_exists('MA_SHIPPER', $fields)) {
                if ($fields['MA_SHIPPER'] != null && $order['TRANG_THAI'] != 'chờ giao hàng') {
                    throw new Error('order đã có người giao hoặc đang chuẩn bị');
                } else {
                    if (!array_key_exists('SHIPPER_ACTION', $fields))
                        $fields['TRANG_THAI'] = 'giao hàng';
                }
            }
        }
        $order->update($fields);
        return $order;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }
        // else 
        //     throw new Error('thiếu token');

        if ($CurUser != null && $CurUser['role'] != 'ADMIN')
            throw new Error('không có quyền');

        return order::destroy($id);
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
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json',
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
        }
        // else 
        //     throw new Error('thiếu token');

        $builder = order::query();
        $term = $request->all();


        if ($CurUser != null && $CurUser['role'] == 'BUYER')
            $builder->where('MA_NGUOI_DUNG', '=', $CurUser['id']);
        else {
            if (!empty($term['MA_NGUOI_DUNG']))
                $builder->where('MA_NGUOI_DUNG', '=', $term['MA_NGUOI_DUNG']);
        }

        if ($CurUser != null && $CurUser['role'] == 'SELLER')
            $builder->where('MA_CUA_HANG', '=', $CurUser['id']);
        else {
            if (!empty($term['MA_CUA_HANG']))
                $builder->where('MA_CUA_HANG', '=', $term['MA_CUA_HANG']);
        }

        if ($CurUser != null && $CurUser['role'] == 'SELLER')
            $builder->where('MA_SHIPPER', '=', $CurUser['id']);
        else {
            if (!empty($term['MA_SHIPPER']))
                $builder->where('MA_SHIPPER', '=', $term['MA_SHIPPER']);
        }

        if (!empty($term['DIA_CHI'])) {
            $builder->where('DIA_CHI', 'like', '%' . $term['DIA_CHI'] . '%');
        }
        if (!empty($term['SDT'])) {
            $builder->where('SDT', 'like', '%' . $term['SDT'] . '%');
        }
        if (!empty($term['TONG_TIEN'])) {
            $builder->where('TONG_TIEN', '=', $term['TONG_TIEN']);
        }
        if (!empty($term['TRANG_THAI'])) {
            $builder->where('TRANG_THAI', '=', $term['TRANG_THAI']);
        }
        return $builder->get();
    }
}
