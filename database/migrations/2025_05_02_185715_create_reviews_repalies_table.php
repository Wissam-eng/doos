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
        Schema::create('reviews_repalies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->foreign('review_id')->references('id')->on('reviews')->onDelete('cascade');

            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');


            $table->unsignedBigInteger('doos_user_id')->nullable();
            $table->foreign('doos_user_id')->references('id')->on('doos_users')->onDelete('cascade');

            $table->text('comment')->nullable();

            $table->enum('love', [ 'active', 'inactive'])->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews_repalies');
    }
};
