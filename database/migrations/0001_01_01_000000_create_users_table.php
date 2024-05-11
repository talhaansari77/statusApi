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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('email')->unique();
            $table->boolean('isModel')->default(0);
            $table->string('gender')->default('');
            $table->string('birthday')->default('');
            $table->string('occupation')->default('');
            $table->string('bio')->default('');
            $table->string('link')->default('');
            $table->string('otp')->default('');
            $table->string('location')->default('');
            $table->string('lat')->default('');
            $table->string('lng')->default('');
            $table->string('imageUrl')->default('');
            $table->string('wallpaperUrl')->default('');
            $table->string('gif1')->default('');
            $table->string('gif2')->default('');
            $table->boolean('wallComments')->default(1);
            $table->boolean('isActive')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // foreignKey
            // $table->bigInteger('userSettingsId')->unsigned();
            // $table->foreign('userSettingsId')->references('id')->on('user_settings')->onDelete('cascade');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
