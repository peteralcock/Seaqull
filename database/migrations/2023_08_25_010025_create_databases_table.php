<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('databases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->string('host');
            $table->integer('port');
            $table->string('username');
            $table->string('password')->nullable();
            $table->string('name');
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('databases');
    }
};
