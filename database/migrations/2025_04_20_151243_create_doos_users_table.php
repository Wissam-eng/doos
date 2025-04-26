<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doos_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('img')->nullable();
            $table->enum('role', ['owner', 'manager', 'support']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();

            $table->timestamps();
        });


        // تأكد أن جدول users موجود قبل تنفيذ هذا السطر
        DB::table('doos_users')->insert([
            'name' => 'doos admin',
            'email' => 'admin@gmail.com',
            'password' => hash::make('1'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doos_users');
    }
};
