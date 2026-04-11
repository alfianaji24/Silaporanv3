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
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique(); // Laravel session ID
            $table->string('ip_address', 45)->index(); // Support IPv6
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // Mobile, Tablet, Desktop
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity')->useCurrent()->index();
            $table->timestamp('login_time')->useCurrent();
            $table->timestamp('logout_time')->nullable();
            $table->boolean('is_forced_logout')->default(false);
            $table->text('forced_logout_reason')->nullable();
            $table->foreignId('forced_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['session_id', 'is_active']);
            $table->index(['last_activity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
