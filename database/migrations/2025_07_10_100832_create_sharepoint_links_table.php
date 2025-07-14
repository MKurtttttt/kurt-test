<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sharepoint_links', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable();
            $table->string('department')->nullable();
            $table->string('office')->nullable(); 
            $table->string('label');
            $table->string('url');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('sharepoint_department_id')->nullable();
            $table->unsignedBigInteger('sharepoint_office_id')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sharepoint_links');
    }
};
