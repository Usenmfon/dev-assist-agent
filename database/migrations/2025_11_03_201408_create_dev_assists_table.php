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
        Schema::create('dev_assists', function (Blueprint $table) {
            $table->id();
            $table->string('channel_id');
            $table->string('user_id');
            $table->text('message');
            $table->text('response')->nullable();
            $table->string('intent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dev_assists');
    }
};
