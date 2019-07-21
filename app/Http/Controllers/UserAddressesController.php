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
    public function index(Request $request,UserAddress $address)
    {

//       $count=$address->where('user_id',auth()->user()->id)->count();
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
     * 保存地址
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

    /**
     * 修改页面页面渲染
     * @param UserAddress $user_address
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(UserAddress $user_address)
    {
        //策略类校验权限
        $this->authorize('own',$user_address);
//        注意：控制器的参数名 $user_address 必须和路由中的 {user_address} 一致才可以
        return view('user_addresses.create_and_edit',['address'=>$user_address]);
    }

    /**
     * 保存修改数据
     * @param UserAddress $user_address
     * @param UserAddressRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserAddress $user_address,UserAddressRequest $request)
    {

        //策略类校验权限
        $this->authorize('own',$user_address);

        $user_address->update($request->only([
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

    /**
     * 删除
     * @param UserAddress $user_address
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(UserAddress $user_address)
    {
        //策略类校验权限
        $this->authorize('own',$user_address);
        $user_address->delete();
//        return redirect()->route('user_addresses.index');
        return ['data'=>['message'=>"删除成功",'code'=>200]];
    }
}
