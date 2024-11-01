<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('host_id')->constrained('users');
            $table->string('title');
            $table->string('host_username');
            $table->integer('host_rank');
            $table->integer('host_mr')->nullable();
            $table->json('host_characters');
            $table->json('requested_characters');
            $table->integer('min_rank')->nullable();
            $table->integer('max_rank')->nullable();
            $table->integer('min_mr')->nullable();
            $table->integer('max_mr')->nullable();
            $table->string('status')->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
