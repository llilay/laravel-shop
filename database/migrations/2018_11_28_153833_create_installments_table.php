<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->unique(); // 分期流水号
            $table->unsignedInteger('user_id'); // 用户 IDs
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('order_id'); // 对应的商品订单 ID
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->decimal('total_amount'); // 总本金
            $table->unsignedInteger('count'); // 还款期数
            $table->float('fee_rate'); // 手续费率
            $table->float('fine_rate'); // 逾期费率
            $table->string('status')->default(\App\Models\Installment::STATUS_PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('installments');
    }
}
