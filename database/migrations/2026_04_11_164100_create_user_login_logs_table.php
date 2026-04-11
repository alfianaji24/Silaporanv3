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
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->index(); // Support IPv6
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // Mobile, Tablet, Desktop
            $table->string('platform')->nullable(); // Windows, Android, iOS, etc.
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->boolean('is_tablet')->default(false);
            $table->boolean('is_desktop')->default(false);
            $table->json('languages')->nullable(); // Browser languages
            $table->timestamp('login_time')->index();
            $table->string('session_id')->nullable()->index();
            $table->boolean('is_successful')->default(true);
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'login_time']);
            $table->index(['ip_address', 'login_time']);
            $table->index(['device_type', 'login_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_logs');
    }
};
