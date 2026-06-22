<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->string('type')->default('welcome')->comment('welcome, investment');
                $table->unsignedInteger('plan_id')->nullable()->comment('strategy/plan for investment certificates');
                $table->string('strategy_name')->nullable();
                $table->string('certificate_number')->unique();
                $table->string('uid', 40)->unique()->comment('public shareable token');
                $table->timestamp('issued_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'type']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
