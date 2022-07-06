<?php

namespace App\Http\Controllers;

use App\Models\product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Error;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return product::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $CurUser = null;
        if (!empty($request->header()['authorization']) && $request->header()['authorization'] != null) {
            $CurUser = Http::withHeaders([
                'accept' => 'application/json', 
                'Authorization' => $request->header()['authorization']
            ])->get('http://localhost:8002/api/current')->json();
            if (array_key_exists('message', $CurUser))
                if ($CurUser['message'] == 'Unauthenticated.')
                    throw new Error('token không phù hợp hoặc chưa login');
            if ($CurUser['role'] != 'SELLER')
                throw new Error('không có quyền');
        }
        // else 
        //     throw new Error('thiếu token');

        $rules = [
            'TEN_SP' => 'required|string',
            'LOAI_SP' => 'required|string',
            'GIA_SP' => 'required|numeric|gte:0',
            'SL_CON_LAI' => 'required|numeric|gte:0',
            'MO_TA' => 'string|nullable',
            'ANH' => 'string|nullable',
        ];

        if ($CurUser == null) {
            $rules['MA_CUA_HANG'] = 'required|numeric';
        }

        $fields = $request->validate($rules);
        if ($CurUser != null)
            $fields['MA_CUA_HANG'] = $CurUser['id'];

        $store = Http::withHeaders([
            'accept' => 'application/json',
        ])->get('http://localhost:8002/api/store/' . $fields['MA_CUA_HANG'])->json();
        if (empty($store) || $store == null)
            throw new Error('store không tồn tại');

        $product = product::create($fields);

        return product::find($product->MA_SP);
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
        return product::find($id);
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
            if ($CurUser['role'] != 'SELLER')
                throw new Error('không có quyền');
        }
        // else 
        //     throw new Error('thiếu token');

        $product = product::find($id);
        if (!empty($CurUser) && $CurUser != null && $product['MA_CUA_HANG'] != $CurUser['id']) {
            throw new Error('Không có quyền update product của store khác');
        }

        $request->validate([
            'GIA_SP' => 'numeric|gte:0|nullable',
            'SL_CON_LAI' => 'numeric|gte:0|nullable',
            'MO_TA' => 'string|nullable',
            'ANH' => 'string|nullable',
        ]);

        $product = product::find($id);
        $product->update($request->all());
        return $product;
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
            if ($CurUser['role'] != 'SELLER')
                throw new Error('không có quyền');
        }
        // else 
        //     throw new Error('thiếu token');

        $product = product::find($id);
        if (!empty($CurUser) && $CurUser != null && $product['MA_CUA_HANG'] != $CurUser['id']) {
            throw new Error('Không có quyền xóa product của store khác');
        }

        return product::destroy($id);
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
        $builder = product::query();
        $term = $request->all();
        if (!empty($term['TEN_SP'])) {
            $builder->where('TEN_SP', 'like', '%' . $term['TEN_SP'] . '%');
        }
        if (!empty($term['LOAI_SP'])) {
            $builder->where('LOAI_SP', 'like', '%' . $term['LOAI_SP'] . '%');
        }
        if (!empty($term['MA_CUA_HANG'])) {
            $builder->where('MA_CUA_HANG', '=', $term['MA_CUA_HANG']);
        }
        if (!empty($term['GIA_SP'])) {
            $builder->where('GIA_SP', '=', $term['GIA_SP']);
        }
        if (!empty($term['SL_CON_LAI'])) {
            $builder->where('SL_CON_LAI', '=', $term['SL_CON_LAI']);
        }
        return $builder->get();
    }

    /**
     * Get types of products
     *
     * @param  
     * @return \Illuminate\Http\Response
     */
    public function productType()
    {
        return Response(['LOAI_SP' => product::distinct('LOAI_SP')->pluck('LOAI_SP')]);
    }
}
