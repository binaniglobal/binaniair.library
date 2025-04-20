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
        Schema::create('manual_item_contents', function (Blueprint $table) {
            $table->uuid('micd')->primary()->index(); // Set 'micd' as primary key and UUID
            $table->uuid('manual_uid'); // Foreign key
            $table->uuid('manual_items_uid'); // Foreign key
            $table->string('name')->nullable();
            $table->string('link')->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_size')->nullable();
            $table->timestamps();

            $table->foreign('manual_uid')->references('mid')->on('manuals')->onDelete('cascade');
            $table->foreign('manual_items_uid')->references('miid')->on('manuals_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_item_contents');
    }
};
