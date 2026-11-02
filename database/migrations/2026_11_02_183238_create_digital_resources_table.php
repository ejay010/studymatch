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
        Schema::create('digital_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educator_profile_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('subject');
            $table->unsignedTinyInteger('grade_level')->nullable();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedInteger('file_size')->default(0); // bytes
            $table->string('file_type', 20)->default('pdf');
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_resources');
    }
};
