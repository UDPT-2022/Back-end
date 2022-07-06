<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Review;
use App\Models\product;
use Error;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
            'MA_SP' => 'required|numeric',
            'DANH_GIA' => 'required|string',
        ];

        if ($CurUser == null) {
            $rules['MA_NGUOI_DUNG'] = 'required|numeric';
        }
        $fields = $request->validate($rules);
        if ($CurUser != null)
            $fields['MA_NGUOI_DUNG'] = $CurUser['id'];
        else {
            $user = Http::withHeaders([
                'accept' => 'application/json',
            ])->get('http://localhost:8002/api/existuser/' . $fields['MA_NGUOI_DUNG'])->json();
            if (empty($user) || $user == null)
                throw new Error('user không tồn tại');
        }
        $product = product::find($fields['MA_SP']);
        if ($product['MA_CUA_HANG'] == $fields['MA_NGUOI_DUNG'])
            throw new Error('Không cho phép tự đánh giá ');
        $Review = Review::create($fields);
        return Review::find($Review->MA_REVIEW);
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
        return Review::find($id);
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

        $rules = [
            'DANH_GIA' => 'required|string',
        ];

        $review = Review::find($id);
        if (empty($review) || $review == null)
            throw new Error('reivew không tồn tại');
        else if ($CurUser != null && $CurUser['id'] != $review['MA_NGUOI_DUNG'])
            throw new Error('Không có quyền');

        $fields = $request->validate($rules);
        $review->update($fields);
        return $review;
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

        $review = Review::find($id);
        if (empty($review) || $review == null)
            throw new Error('reivew không tồn tại');
        else if ($CurUser != null && $CurUser['id'] != $review['MA_NGUOI_DUNG'])
            throw new Error('Không có quyền');
        return Review::destroy($id);
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
        $builder = Review::query();
        $term = $request->all();
        if (!empty($term['MA_SP'])) {
            $builder->where('MA_SP', '=', $term['MA_SP']);
        }
        if (!empty($term['MA_NGUOI_DUNG'])) {
            $builder->where('MA_NGUOI_DUNG', '=', $term['MA_NGUOI_DUNG']);
        }
        return $builder->get();
    }
}
