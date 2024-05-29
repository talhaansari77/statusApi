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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('');
            $table->string('description')->nullable();
            $table->longText('imageUrl')->default('');
            $table->longText('gif')->default('');
            $table->bigInteger('views')->default(0);
            $table->bigInteger('likes')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->bigInteger('channelId')->unsigned();
            $table->foreign('channelId')->references('id')->on('status_channels')->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
