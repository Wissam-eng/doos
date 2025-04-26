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
        Schema::create('categories_cars', function (Blueprint $table) {
            $table->id();

            $table->string('make'); // الشركة المصنعة، مثل Toyota، Ford
            $table->string('model'); // اسم الموديل
            $table->year('year'); // سنة الصنع
            $table->string('category')->nullable(); // مثل SUV، Sedan، Hatchback
            $table->unsignedTinyInteger('seats')->nullable(); // عدد المقاعد
            $table->string('transmission')->nullable(); // Automatic / Manual
            $table->string('fuel_type')->nullable(); // Petrol / Diesel / Electric / Hybrid
            $table->float('engine_capacity')->nullable(); // سعة المحرك باللتر
            $table->string('logo')->nullable(); // وصف إضافي
            $table->text('description')->nullable(); // وصف إضافي
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories_cars');
    }
};
