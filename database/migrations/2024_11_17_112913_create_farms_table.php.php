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
        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 255);
            $table->decimal('latitude', 10, 7); 
            $table->decimal('longitude', 10, 7);
            $table->string('address')->nullable();
            $table->string('district', 255);
            $table->string('variety', 255)->nullable();
            $table->decimal('hectares', 8, 2);  
            $table->integer('tree_age')->nullable();
            $table->integer('planted_coconut')->nullable();
            $table->string('condition', 255)->nullable();
            $table->timestamps();     
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farms');
    }
};
