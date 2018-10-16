<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\SendReviewRequest;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use App\Events\OrderReviewed;
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

    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        // 返回订单信息
        return $order;
    }

    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        // 使用 load 方法加载关联数据, 避免 N + 1 性能问题
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(Order $order, SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        // 判断该订单是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }

        $reviews = $request->input('reviews');

        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }

            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }
}