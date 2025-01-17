<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['product_id', 'user_id']);
            
            // Unique constraint yang memungkinkan multiple review dengan order_item berbeda
            $table->unique(['user_id', 'product_id', 'order_item_id']);

            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            $table->foreign('order_item_id')
                  ->references('id')
                  ->on('order_items')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
};