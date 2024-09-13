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
        Schema::create('issuing_books', function (Blueprint $table) {
            $table->id();
            $table->uuid('ibd')->unique()->index();
            $table->string('user_id');
            $table->string('manual_id');
            $table->dateTime('date-time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issuing_books');
    }
};
