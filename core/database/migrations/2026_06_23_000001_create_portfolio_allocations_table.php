<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('portfolio_allocations')) {
            Schema::create('portfolio_allocations', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->decimal('percentage', 8, 2)->default(0);
                $table->string('color', 20)->default('#1989BE');
                $table->string('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->tinyInteger('status')->default(1)->comment('1: active, 0: inactive');
                $table->timestamps();
            });

            $now      = now();
            $defaults = [
                ['name' => 'Commodities', 'percentage' => 33, 'color' => '#1989BE', 'description' => 'Gold, oil and metals exposure', 'sort_order' => 1],
                ['name' => 'Forex',       'percentage' => 20, 'color' => '#14709a', 'description' => 'Major and minor currency pairs', 'sort_order' => 2],
                ['name' => 'Indices',     'percentage' => 18, 'color' => '#47a8d4', 'description' => 'Global equity index positions', 'sort_order' => 3],
                ['name' => 'Futures',     'percentage' => 15, 'color' => '#7fc4e8', 'description' => 'Diversified futures contracts', 'sort_order' => 4],
                ['name' => 'Crypto',      'percentage' => 14, 'color' => '#b3dff5', 'description' => 'Select digital asset allocation', 'sort_order' => 5],
            ];

            foreach ($defaults as $row) {
                $row['status']     = 1;
                $row['created_at'] = $now;
                $row['updated_at'] = $now;
                \Illuminate\Support\Facades\DB::table('portfolio_allocations')->insert($row);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_allocations');
    }
};
