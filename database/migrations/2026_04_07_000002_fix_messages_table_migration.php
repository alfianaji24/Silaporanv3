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
        // Check if columns already exist
        $columns = Schema::getColumnListing('messages');
        
        if (!in_array('attempts', $columns)) {
            Schema::table('messages', function (Blueprint $table) {
                $table->integer('attempts')->default(0)->after('status');
            });
        }
        
        if (!in_array('last_attempt_at', $columns)) {
            Schema::table('messages', function (Blueprint $table) {
                $table->timestamp('last_attempt_at')->nullable()->after('attempts');
            });
        }
        
        if (!in_array('permanent_failed', $columns)) {
            Schema::table('messages', function (Blueprint $table) {
                $table->boolean('permanent_failed')->default(false)->after('last_attempt_at');
            });
        }

        // Update status enum to include 'pending' if not already done
        try {
            DB::statement("ALTER TABLE messages MODIFY COLUMN status ENUM('pending','success','failed') NOT NULL DEFAULT 'pending'");
        } catch (\Exception $e) {
            // Status enum might already be correct, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't rollback to avoid data loss issues
        // These columns can be dropped manually if needed
    }
};
