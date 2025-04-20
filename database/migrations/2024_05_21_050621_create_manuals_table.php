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
        Schema::create('manuals', function (Blueprint $table) {
            $table->uuid('mid')->primary()->index(); // Set 'mid' as primary key and UUID
            $table->string('name')->unique();
            $table->bigInteger('no_of_items')->default(0);
            $table->tinyInteger('status')->default(0); //0=Active
            $table->tinyInteger('type')->default(0); //0= Soft-copy
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuals');
    }
};
