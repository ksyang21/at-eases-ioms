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
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('group_id');
			$table->foreign('group_id')->on('groups')->references('id')->cascadeOnDelete();
	        $table->unsignedBigInteger('seller_id');
	        $table->foreign('seller_id')->on('users')->references('id')->cascadeOnDelete();
			$table->enum('status', ['active', 'inactive']);
	        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_members');
    }
};
