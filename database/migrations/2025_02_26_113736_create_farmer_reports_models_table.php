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
        Schema::create('farmer_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->onDelete('cascade')->nullable();
            // $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade')->nullable();
            $table->string('farmer_name'); 
            $table->string('contact_no'); 
            $table->string('farm_location'); 
            $table->decimal('farm_size', 10, 2); 
            $table->bigInteger('coconut_trees'); 
            $table->string('coconut_variety');
            $table->json('symptoms'); 
            $table->string('disease'); 
            $table->string('image_path')->nullable(); 
            $table->string('status')->default('unread');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmer_reports');
    }
};
