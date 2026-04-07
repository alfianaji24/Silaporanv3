<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->integer('attempts')->default(0)->after('status');
            $table->timestamp('last_attempt_at')->nullable()->after('attempts');
            $table->boolean('permanent_failed')->default(false)->after('last_attempt_at');
        });

        DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('pending','success','failed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip rollback to avoid data truncation issues
        // Manually remove columns if needed
    }
};
