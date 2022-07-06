<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Error;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Store::all();
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
        $user = auth()->user();
        if (!empty($user) && $user != null && $user->role != 'SELLER') {
            throw new Error('Không có quyền');
        }

        $rule = [
            'TEN_CUA_HANG' => 'string|nullable',
            'SDT' => 'string|regex:/^[0-9]+$/|nullable',
            'EMAIL' => 'string|nullable',
            'DIA_CHI' => 'string|nullable',
            'LOGO' => 'string|nullable',
        ];
        $id = null;
        if (empty($user) ||  $user == null) {
            $rule['id'] = 'required|numeric';
        }

        $fields = $request->validate($rule);
        if (empty($user) && $user == null && !empty($fields['id']) && $fields['id'] != null) {
            $id = $fields['id'];
        } else {
            $id = $user->id;
        }
        $new = ['id' => $id];
        foreach ($fields as $field => $value) {
            $new[$field] = $value;
        }

        // $new = [
        //     'id' => $id,
        //     'TEN_CUA_HANG' => $fields['TEN_CUA_HANG'],
        //     'SDT' => $fields['SDT'],
        //     'EMAIL' => $fields['EMAIL'],
        //     'DIA_CHI' => $fields['DIA_CHI'],
        //     'LOGO' => $fields['LOGO'],
        // ];
        $Store = Store::create($new);
        return $Store->find($id);
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
        return Store::find($id);
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
        if (!empty($user) && $user != null && $user->role != 'SELLER' && $user->role != 'ADMIN') {
            throw new Error('Không có quyền');
        }
        if (!empty($user) &&  $user != null) {
            if ($user->role == 'SELLER') {
                $id = $user->id;
            } else if ($user->role == 'ADMIN' && $id = null) {
                throw new Error('id rỗng');
            }
        }
        $fields = $request->validate([
            'TEN_CUA_HANG' => 'string|nullable',
            'SDT' => 'string|regex:/^[0-9]+$/|nullable',
            'EMAIL' => 'string|nullable',
            'DIA_CHI' => 'string|nullable',
            'LOGO' => 'string|nullable',
        ]);
        $updated = [];
        foreach ($fields as $field => $value) {
            $updated[$field] = $value;
        }

        $Store = Store::find($id);
        if (empty($Store) || $Store == null)
            throw new Error('store không tồn tại');

        $Store->update($updated);
        return $Store;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
    /**
     * Search the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $builder = Store::query();
        $term = $request->all();
        if (!empty($term['TEN_CUA_HANG'])) {
            $builder->where('TEN_CUA_HANG', 'like', '%' . $term['TEN_CUA_HANG'] . '%');
        }
        if (!empty($term['SDT'])) {
            $builder->where('SDT', 'like', '%' . $term['SDT'] . '%');
        }
        if (!empty($term['EMAIL'])) {
            $builder->where('EMAIL', 'like', '%' . $term['EMAIL'] . '%');
        }
        if (!empty($term['DIA_CHI'])) {
            $builder->where('DIA_CHI', 'like', '%' . $term['DIA_CHI'] . '%');
        }
        return $builder->get();
    }
}
