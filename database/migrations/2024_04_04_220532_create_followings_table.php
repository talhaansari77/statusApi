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
        Schema::create('followings', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('followee')->unsigned();
            $table->foreign('followee')->references('id')->on('users')->onDelete("cascade");
            $table->bigInteger('follower')->unsigned();
            $table->foreign('follower')->references('id')->on('users')->onDelete("cascade");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followings');
    }
};
