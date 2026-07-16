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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('url')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('Active');
            $table->string('document_code')->nullable();
            $table->integer('revision_count')->default(0)->nullable();
            $table->date('effectivity_date')->nullable();
            $table->string('policy_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
