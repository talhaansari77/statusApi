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
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            // $table->string('privacyLvl')->default('everyone');
            // $table->array('blockedAccounts')->nullable();
            $table->boolean('pushNotification')->default(0);
            $table->string('instagramLink')->default('');
            $table->string('youtubeLink')->default('');
            $table->bigInteger('userId')->unsigned()->default(0);
            // $table->foreign('userId')->references('id')->on('users')->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
