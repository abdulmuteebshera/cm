<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('support_tickets', function (Blueprint $table): void {
            $table->string('company_name', 255)->nullable()->after('email');
            $table->string('phone', 50)->nullable()->after('company_name');
            $table->string('capital_range', 80)->nullable()->after('subject');
        });
    }

    public function down(): void
    {
        Schema::table('support_tickets', function (Blueprint $table): void {
            $table->dropColumn(['company_name', 'phone', 'capital_range']);
        });
    }
};
