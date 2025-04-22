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
        Schema::create('technician_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->string('farmer_name'); 
            $table->string('contact_no'); 
            $table->string('recipient'); 
            $table->string('farm_location'); 
            $table->string('district');
            $table->decimal('farm_size', 10, 2); 
            $table->bigInteger('coconut_trees'); 
            $table->string('coconut_variety');
            $table->string('diagnosis_disease'); 
            // $table->text('treatment_recommendation'); 
            $table->string('image_proof'); 
            $table->string('status'); 
            $table->integer('no_of_infected')->unsigned()->nullable(false); 
            $table->json('coordinates'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_reports');
    }
};
