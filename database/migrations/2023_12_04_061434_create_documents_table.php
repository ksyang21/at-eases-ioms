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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->default(NULL)->nullable();
            $table->string('filename');
            $table->unsignedBigInteger('uploaded_by');
            $table->enum('type', ['document', 'image', 'video']);
            $table->enum('status', ['active', 'inactive', 'outdated']);
            $table->foreign('uploaded_by')->references('id')->on('users')->noActionOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
