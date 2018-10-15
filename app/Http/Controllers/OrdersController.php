<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\OrderRequest;

class OrdersController extends Controller
{
    /**
     * 下单, 保存订单
     *
     * 用 Laravel 提供的延迟任务（Delayed Job）功能实现下面的功能
     * 在创建订单的同时我们减去了对应商品 SKU 的库存，恶意用户可以通过下大量的订单又不支付来占用商品库存，
     * 让正常的用户因为库存不足而无法下单。
     * 因此我们需要有一个关闭未支付订单的机制，当创建订单之后一定时间内没有支付，将关闭订单并退回减去的库存。
     *
     * @param OrderRequest $request
     * @return mixed
     */
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user  = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
    }

    public function index(Request $request)
    {
        $orders = Order::query()
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
        // 这里的 load() 方法与上一章节介绍的 with() 预加载方法有些类似，称为 延迟预加载，
        // 不同点在于 load() 是在已经查询出来的模型上调用，
        // 而 with() 则是在 ORM 查询构造器上调用。
    }
}