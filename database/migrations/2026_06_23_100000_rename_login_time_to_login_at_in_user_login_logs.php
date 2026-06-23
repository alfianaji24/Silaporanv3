<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename login_time -> login_at to match source project's UserloginlogController.
     */
    public function up(): void
    {
        // 1. Drop the foreign key first (it depends on user_id index)
        DB::statement('ALTER TABLE user_login_logs DROP FOREIGN KEY user_login_logs_user_id_foreign');

        // 2. Now drop composite indexes that contain login_time
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_user_id_login_time_index');
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_ip_address_login_time_index');
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_device_type_login_time_index');
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_login_time_index');

        // 3. Rename the column
        Schema::table('user_login_logs', function (Blueprint $table) {
            $table->renameColumn('login_time', 'login_at');
        });

        // 4. Recreate foreign key + indexes with new column name
        Schema::table('user_login_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('login_at');
            $table->index(['user_id', 'login_at']);
            $table->index(['ip_address', 'login_at']);
            $table->index(['device_type', 'login_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE user_login_logs DROP FOREIGN KEY user_login_logs_user_id_foreign');
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_user_id_login_at_index');
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_ip_address_login_at_index');
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_device_type_login_at_index');
        DB::statement('ALTER TABLE user_login_logs DROP INDEX user_login_logs_login_at_index');

        Schema::table('user_login_logs', function (Blueprint $table) {
            $table->renameColumn('login_at', 'login_time');
        });

        Schema::table('user_login_logs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('login_time');
            $table->index(['user_id', 'login_time']);
            $table->index(['ip_address', 'login_time']);
            $table->index(['device_type', 'login_time']);
        });
    }
};
