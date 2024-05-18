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
        Schema::create('in_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('imageUrl')->default('');
            $table->string('username')->default('');
            $table->string('description')->default('');
            $table->boolean('forComment')->default(false);
            $table->boolean('forFollow')->default(false);

            $table->bigInteger('senderId')->unsigned();
            $table->foreign('senderId')->references('id')->on('users')->onDelete("cascade");
            $table->bigInteger('receiverId')->unsigned();
            $table->foreign('receiverId')->references('id')->on('users')->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_notifications');
    }
};
