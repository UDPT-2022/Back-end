<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\order;
use App\Models\product;
use App\Models\order_detail;
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

            'HO_TEN' => 'required|string',
            'DIA_CHI' => 'required|string',
            'EMAIL' => 'required|string',
            'SDT' => 'required|string|regex:/^[0-9]+$/',
            'GHI_CHU' => 'required|string',
        ];

        if ($CurUser == null) {
            $rules['MA_NGUOI_DUNG'] = 'required|numeric';
        }

        $fields = $request->validate($rules);

        if ($CurUser != null) {
            $fields['MA_NGUOI_DUNG'] = $CurUser['id'];
        }
        $fields['TRANG_THAI'] = 'đặt';

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
        if (empty($order) || $order == null)
            return [];
        $details = order_detail::where('MA_DON_HANG', '=', $id)->get();
        foreach ($details as $index => $val) {
            $product = product::find($val['MA_SP']);
            $details[$index]['TEN_SP'] = $product['TEN_SP'];
            $details[$index]['ANH'] = $product['ANH'];
        }

        $order['CHI_TIET_DON_HANG'] =  $details;
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
        if ($order['TRANG_THAI'] == 'đặt')
            $order->update(['TRANG_THAI' => 'chuẩn bị']);
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
                    $rules['HO_TEN'] = 'string';
                    $rules['DIA_CHI'] = 'string';
                    $rules['EMAIL'] = 'string';
                    $rules['SDT'] = 'string|regex:/^[0-9]+$/';
                    $rules['GHI_CHU'] = 'string';
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

                'HO_TEN' => 'string',
                'DIA_CHI' => 'string',
                'EMAIL' => 'string',
                'SDT' => 'string|regex:/^[0-9]+$/',
                'GHI_CHU' => 'string',

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
            if (!empty($fields['MA_CUA_HANG'])) {
                $store = Http::withHeaders([
                    'accept' => 'application/json',
                ])->get('http://localhost:8002/api/store/' . $fields['MA_CUA_HANG'])->json();
                if (empty($store) || $store == null)
                    throw new Error('store không tồn tại');
            }
            if (!empty($fields['MA_NGUOI_DUNG'])) {
                $buyer = Http::withHeaders([
                    'accept' => 'application/json',
                ])->get('http://localhost:8002/api/existuser/' . $fields['MA_NGUOI_DUNG'])->json();
                if (empty($buyer) || $buyer == null)
                    throw new Error('buyer{MA_NGUOI_DUNG} không tồn tại');
                else {
                    if ($buyer['role'] != 'BUYER')
                        throw new Error('không phải buyer');
                }
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

        $details = order_detail::where('MA_DON_HANG', '=', $id)->get();

        foreach ($details as $key => $detail) {
            $product = product::find($detail['MA_SP']);
            if (!empty($product) && $product != null) {
                $product->update(['SL_CON_LAI' => $product['SL_CON_LAI'] + $detail['SO_LUONG']]);
            }
        }

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

        $orders = $builder->get();

        foreach ($orders as $order) {
            $detail = order_detail::where('MA_DON_HANG', '=', $order['MA_DON_HANG'])->get();
            if (!empty($detail) && $detail != null) {
                $order['CHI_TIET_DON_HANG'] =  $detail;
            } else
                $order['CHI_TIET_DON_HANG'] = [];
        }
        return $orders;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOrderWithProducts(Request $request)
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

            'HO_TEN' => 'required|string',
            'DIA_CHI' => 'required|string',
            'EMAIL' => 'string',
            'SDT' => 'required|string|regex:/^[0-9]+$/',
            'GHI_CHU' => 'string',

            'details' => 'required|array',
            'details.*.MA_SP' => 'required|numeric|distinct',
            'details.*.SO_LUONG' => 'required|numeric',
        ];

        if ($CurUser == null) {
            $rules['MA_NGUOI_DUNG'] = 'required|numeric';
        }

        $fields = $request->validate($rules);

        $products = [];
        $stores = [];

        foreach ($fields['details'] as $key => $val) {
            $product = product::find($val['MA_SP']);
            if (!empty($product) && $product != null) {
                $product['SO_LUONG'] = $val['SO_LUONG'];
                $products[] =  $product;
                if (!in_array($product['MA_CUA_HANG'], $stores))
                    $stores[] = $product['MA_CUA_HANG'];
            }
        }

        if ($CurUser != null) {
            $fields['MA_NGUOI_DUNG'] = $CurUser['id'];
        }

        $fields['TRANG_THAI'] = 'đặt';
        //return ['fields' => $fields, 'products' => $products, 'stores' => $stores];

        if (count($products) <= 0 || count($stores) <= 0)
            throw new Error('details rỗng');

        $size = count($stores);
        $result = [];
        $orders = [];
        //return ['fields' => $fields, 'products' => $products, 'stores' => $stores];
        foreach ($stores as $store) {
            $notEnoughQuanlityProduct = [];
            $orderInfo = $request->validate([
                'HO_TEN' => 'required|string',
                'DIA_CHI' => 'required|string',
                'EMAIL' => 'string|nullable',
                'SDT' => 'required|string|regex:/^[0-9]+$/',
                'GHI_CHU' => 'string|nullable',
            ]);
            $orderInfo['MA_NGUOI_DUNG'] = $fields['MA_NGUOI_DUNG'];
            $orderInfo['MA_CUA_HANG'] = $store;
            $orderInfo['TRANG_THAI'] = $fields['TRANG_THAI'];
            // $orderInfo = [
            //     'MA_NGUOI_DUNG' => $fields['MA_NGUOI_DUNG'],
            //     'MA_CUA_HANG' => $store,
            //     'DIA_CHI' => $fields['DIA_CHI'],
            //     'SDT' => $fields['SDT'],
            //     'TRANG_THAI' => $fields['TRANG_THAI'],
            // ];
            // $storeProduct = array_filter($products, function ($product) use ($store) {
            //         return $product['MA_CUA_HANG'] == $val;
            //  });
            // $storeProduct = []; 
            $order = order::create($orderInfo);
            $total = 0;
            $order_detail = [];
            try {
                foreach ($products as $product) {
                    if ($product['MA_CUA_HANG'] == $store) {
                        // $storeProduct[] = $product;
                        $slcl = $product['SL_CON_LAI'] - $product['SO_LUONG'];
                        if ($slcl < 0) {
                            $notEnoughQuanlityProduct[] = $product;
                        } else {

                            $gia = $product['GIA_SP'] * $product['SO_LUONG'];
                            $detail = [
                                'MA_DON_HANG' => $order['MA_DON_HANG'],
                                'MA_SP' => $product['MA_SP'],
                                'SO_LUONG' => $product['SO_LUONG'],
                                'DON_GIA' => $product['GIA_SP'],
                                'GIA' =>  $gia,
                            ];

                            $total +=  $gia;
                            $updateProduct = product::find($product['MA_SP']);
                            $updateProduct->update([
                                'SL_CON_LAI' => $slcl,
                            ]);
                            $orderDetail = order_detail::create($detail);
                            $order_detail[] = $orderDetail;
                        }
                    }
                }
            } catch (Error) {
                order::destroy($order['MA_DON_HANG']);
            }
            $order->update(['TONG_TIEN' => $total]);
            $result[] = [
                'order' => $order,
                'order_detail' => $order_detail,
            ];
            //$result[]=['orderInfo'=>$orderInfo,'storeProduct'=>$storeProduct];
        }

        return $result;
    }
}
