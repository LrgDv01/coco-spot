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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade')->nullable();
            $table->string('farmer_name'); 
            $table->string('contact_no'); 
            $table->string('recipient'); 
            $table->string('farm_name');
            $table->string('farm_location'); 
            $table->string('district');
            $table->decimal('farm_size', 10, 2); 
            $table->bigInteger('coconut_trees'); 
            $table->string('coconut_variety');
            $table->string('soil_type'); 
            $table->json('disease_type'); 
            $table->text('note'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
