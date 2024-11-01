<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_presets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('title');
            $table->json('host_characters');
            $table->integer('host_rank');
            $table->integer('host_mr')->nullable();
            $table->json('requested_characters');
            $table->integer('min_rank')->nullable();
            $table->integer('max_rank')->nullable();
            $table->integer('min_mr')->nullable();
            $table->integer('max_mr')->nullable();
            $table->json('attributes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_presets');
    }
};
