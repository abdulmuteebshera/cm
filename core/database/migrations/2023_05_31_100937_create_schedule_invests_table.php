<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_invests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('wallet', 40)->nullable();
            $table->decimal('amount', 28, 8)->default(0);
            $table->integer('schedule_times')->default(0);
            $table->integer('rem_schedule_times')->default(0);
            $table->integer('interval_hours')->default(0);
            $table->dateTime('next_invest')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_invests');
    }
};
