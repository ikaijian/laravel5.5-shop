<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    /** 收货地址列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        return view('user_addresses.index', ['addresses' => $request->user()->addresses]);
    }

    /**
     * 新增地址
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('user_addresses.create_and_edit', ['address' => new UserAddress()]);

    }

    /**
     * 保存
     * $request->user() 获取当前登录用户。
     * user()->addresses() 获取当前用户与地址的关联关系（注意：这里并不是获取当前用户的地址列表）
     * addresses()->create() 在关联关系里创建一个新的记录。
     * $request->only() 通过白名单的方式从用户提交的数据里获取我们所需要的数据。
     * @param UserAddressRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserAddressRequest $request)
    {
        $request->user()->addresses()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone',
        ]));

        return redirect()->route('user_addresses.index');
    }
}
