<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use Carbon\Carbon;
use App\Jobs\CloseOrder;

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
    public function store(OrderRequest $request)
    {
        $user  = $request->user();
        // 开启一个数据库事务
        $order = \DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            // 更新此地址的最后使用时间
            $address->update(['last_used_at' => Carbon::now()]);
            // 创建一个订单
            $order   = new Order([
                'address'      => [ // 将地址信息放入订单中
                    'address'       => $address->full_address,
                    'zip'           => $address->zip,
                    'contact_name'  => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark'       => $request->input('remark'),
                'total_amount' => 0,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();

            $totalAmount = 0;
            $items       = $request->input('items');
            // 遍历用户提交的 SKU
            foreach ($items as $data) {
                $sku  = ProductSku::find($data['sku_id']);
                // 创建一个 OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($request->input('items'))->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        // 关闭未支付订单
        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));

        return $order;
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