<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            // renter id
            $table->unsignedBigInteger('renter_id');
            $table->foreign('renter_id')->references('id')->on('car_renters')->onDelete('cascade');
            // order id
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders_cars')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->enum('rate', ['1', '2', '3', '4', '5']);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
