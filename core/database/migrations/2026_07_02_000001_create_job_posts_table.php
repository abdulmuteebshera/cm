<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_posts')) {
            Schema::create('job_posts', function (Blueprint $table): void {
                $table->id();
                $table->string('title');
                $table->string('department')->nullable();
                $table->string('location')->nullable();
                $table->string('employment_type')->nullable();
                $table->text('summary')->nullable();
                $table->text('description');
                $table->text('requirements')->nullable();
                $table->tinyInteger('status')->default(1)->comment('1: active, 0: inactive');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
