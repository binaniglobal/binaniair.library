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
            $table->uuid('issue_uid')->primary()->index();
            $table->string('user_uid');
            $table->string('manual_uid');
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
