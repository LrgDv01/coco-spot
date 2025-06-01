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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade')->nullable();
            $table->foreignId('farmer_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->foreignId('technician_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->string('farmer_name');
            $table->integer('request_id');
            $table->string('status');
            $table->string('remarks');
            $table->string('visit_date')->default('Unscheduled')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
