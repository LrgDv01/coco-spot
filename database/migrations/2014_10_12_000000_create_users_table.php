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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('profile_img')->nullable();
            $table->string('user_name');
            $table->string('full_name');
            $table->string('address');
            $table->string('municipal');
            $table->string('contact');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('user_type');
            $table->rememberToken();
            $table->timestamps();
        });

        // Schema::create('technicians_info', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('technician_id')->constrained('users')->onDelete('cascade');
        //     $table->integer('years_in_service')->nullable();
        //     $table->string('services');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // Schema::dropIfExists('technicians');
    }
};
