<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add SKU to products table if not exists
        if (!Schema::hasColumn('products', 'sku')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('sku')->nullable()->after('brand_id');
            });
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable()->unique();
            $table->unsignedInteger('payment_method')->nullable();
            $table->unsignedInteger('status')->default(8); // STATUS_UNREAD = 8
            $table->unsignedInteger('cancel_status')->nullable();
            $table->text('admin_note')->nullable();
            $table->text('status_note')->nullable();
            $table->boolean('taxes_included')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->boolean('buyer_accepts_marketing')->default(false);
            $table->text('discount_codes')->nullable();

            $table->bigInteger('total_price')->default(0);
            $table->bigInteger('shipping_cost')->default(0);
            $table->bigInteger('subtotal_price')->default(0);
            $table->bigInteger('total_discounts')->default(0);
            $table->bigInteger('deposit_fee')->default(0);
            $table->bigInteger('total_line_items_price')->default(0);
            $table->decimal('total_weight', 8, 2)->default(0.00);

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('note')->nullable();

            $table->date('start_rental_date')->nullable();
            $table->date('end_rental_date')->nullable();
            $table->integer('rental_date')->nullable();
            $table->string('rental')->nullable();

            $table->string('currency')->default('VND');
            $table->string('financial_status')->default('PENDING');
            $table->string('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('checkout_token')->nullable();
            $table->string('reference')->nullable();
            $table->string('device_id')->nullable();
            $table->string('browser_ip')->nullable();
            $table->text('note_attributes')->nullable();
            $table->text('payment_gateway_names')->nullable();

            $table->string('processing_method')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('shipping_lines')->nullable();
            $table->text('tax_lines')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('ward')->nullable();

            $table->unsignedBigInteger('creator_id')->nullable();
            $table->unsignedBigInteger('editor_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->bigInteger('price')->default(0);
            $table->bigInteger('sale_price')->default(0);
            $table->boolean('is_sale')->default(false);
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_lines');
        Schema::dropIfExists('orders');

        if (Schema::hasColumn('products', 'sku')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('sku');
            });
        }
    }
};
