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
        Schema::create('status_channels', function (Blueprint $table) {
            $table->id();
            $table->string('channelName')->default('');
            //
            $table->bigInteger('lastPostId')->default(0);
            // $table->foreign('lastPostId')->references('id')->on('posts')->onDelete(null);
            $table->bigInteger('userId')->unsigned();
            $table->foreign('userId')->references('id')->on('users')->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_channels');
    }
};
