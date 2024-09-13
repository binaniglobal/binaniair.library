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
            $table->id();
            $table->uuid('mid')->unique()->index();
            $table->string('name')->unique();
            $table->bigInteger('no_of_items')->default(0);
            $table->tinyInteger('status')->default(0); //0=Active
            $table->tinyInteger('type')->default(0); //0= Soft-copy, 1=Hard-copy
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
