<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_strategy_reports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedSmallInteger('year');
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->unique(['plan_id', 'year']);
            $table->index(['plan_id', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_strategy_reports');
    }
};
