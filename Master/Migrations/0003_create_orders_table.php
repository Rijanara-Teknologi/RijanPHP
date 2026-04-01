<?php

use Teguh02\Rijanphp\Core\Database\Migration\Migration;
use Teguh02\Rijanphp\Core\Database\Schema\Blueprint;
use Teguh02\Rijanphp\Core\Database\Schema\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
