<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('leaderboards')) {
            Schema::create('leaderboards', function (Blueprint $table): void {
                $table->id();
                $table->unsignedInteger('user_id')->nullable()->comment('linked existing user, null for dummy entries');
                $table->string('name')->comment('display name (masked on the public leaderboard)');
                $table->decimal('amount', 28, 8)->default(0)->comment('invested amount used for ranking');
                $table->tinyInteger('status')->default(1)->comment('1: active, 0: inactive');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('leaderboards');
    }
};
