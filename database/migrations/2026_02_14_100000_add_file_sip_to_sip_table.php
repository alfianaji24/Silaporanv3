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
        Schema::table('sip', function (Blueprint $table) {
            $table->string('file_sip', 255)->nullable()->after('status_sip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sip', function (Blueprint $table) {
            $table->dropColumn('file_sip');
        });
    }
};
