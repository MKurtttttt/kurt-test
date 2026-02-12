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
        Schema::table('iso_master_documents', function (Blueprint $table) {
            //Change Timestamp columns to datetime to prevent auto-update and for long-term support
            DB::statement('ALTER TABLE iso_master_documents MODIFY registered_at DATETIME NULL');
            DB::statement('ALTER TABLE iso_master_documents MODIFY superseded_at DATETIME NULL');
            DB::statement('ALTER TABLE iso_master_documents MODIFY deleted_at DATETIME NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iso_master_documents', function (Blueprint $table) {
            //Revert back to timestamp
            DB::statement(query: 'ALTER TABLE iso_master_documents MODIFY registered_at TIMESTAMP NULL');
            DB::statement(query: 'ALTER TABLE iso_master_documents MODIFY superseded_at TIMESTAMP NULL');
            DB::statement(query: 'ALTER TABLE iso_master_documents MODIFY deleted_at TIMESTAMP NULL');
        });
    }
};
