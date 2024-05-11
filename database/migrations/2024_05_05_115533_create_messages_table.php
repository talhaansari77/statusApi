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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->longText('message')->nullable();
            $table->foreignId('senderId')->constrained('users');
            $table->foreignId('receiverId')->nullable()->constrained('users');
            $table->foreignId('conversationId')->nullable()->constrained('conversations');
            $table->timestamps();
        });

        // Schema::create('conversations', function (Blueprint $table) {
        //     $table->foreignId('lastMessageId')->nullable()->constrained('messages');
        // });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
