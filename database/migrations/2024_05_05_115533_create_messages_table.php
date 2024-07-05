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
            $table->longText('message')->default('');
            $table->longText('gif')->default('');
            $table->foreignId('senderId')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiverId')->nullable()->constrained('users');
            $table->foreignId('conversationId')->nullable()->constrained('conversations')->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
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
