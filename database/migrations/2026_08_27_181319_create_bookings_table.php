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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educator_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_profile_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // '1-on-1' or 'group'
            $table->string('status')->default('pending'); // 'pending', 'confirmed', 'cancelled'
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('meeting_link')->nullable();
            $table->integer('max_capacity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
