<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->date('date');
            $table->string('start_time');
            $table->string('end_time');
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');

            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('doctor_id')->references('id')->on('doctors');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
