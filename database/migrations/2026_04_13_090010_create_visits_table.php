<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('visitor_id')
                ->constrained('visitors')
                ->onDelete('cascade');

            $table->foreignId('host_employee_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('registered_by_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Visit Details
            $table->enum('status', ['expected', 'active', 'completed', 'cancelled'])->default('active');
            $table->timestamp('expected_arrival_time')->nullable();
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('expected_departure_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            $table->text('reason_of_visit')->nullable();

            // Bonus Features
            $table->boolean('badge_sent')->default(false);

            $table->timestamps();

            // Indexes voor snelle queries
            $table->index('check_out_time');
            $table->index('check_in_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};