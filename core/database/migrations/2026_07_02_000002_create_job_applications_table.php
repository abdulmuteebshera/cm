<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('job_applications')) {
            Schema::create('job_applications', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('job_post_id')->constrained('job_posts')->cascadeOnDelete();
                $table->string('name');
                $table->string('email');
                $table->string('phone', 50)->nullable();
                $table->string('linkedin', 255)->nullable();
                $table->text('message')->nullable();
                $table->string('resume');
                $table->string('resume_original_name')->nullable();
                $table->tinyInteger('application_status')->default(0)->comment('0: pending, 1: reviewed, 2: shortlisted, 3: rejected');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
