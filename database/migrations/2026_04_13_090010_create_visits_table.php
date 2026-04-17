<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
        $table->id();

        $table->foreignId('visitor_id')->constrained()->cascadeOnDelete();
        $table->foreignId('host_employee_id')->constrained('employees')->cascadeOnDelete();

        $table->text('reason_of_visit')->nullable();

        $table->timestamp('expected_arrival_time')->nullable();
        $table->timestamp('expected_departure_time')->nullable();

        $table->timestamp('check_in_time')->nullable();
        $table->timestamp('check_out_time')->nullable();

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};