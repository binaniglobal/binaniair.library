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
        Schema::create('manuals_items', function (Blueprint $table) {
            $table->uuid('miid')->primary(); // Set 'miid' as primary key and UUID
            $table->uuid('manual_uid'); // Foreign key should also be a UUID
            $table->string('name')->nullable();
            $table->string('link')->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_size')->nullable();
            $table->timestamps();

            $table->foreign('manual_uid')->references('mid')->on('manuals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuals_items');
    }
};
