<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const FREE_DAYS_FIXED = [
        '01.01', '01.05', '08.05', '05.07', '06.07', '28.09', '28.10', '17.11', '24.12', '25.12', '26.12',
    ];

    const FREE_DAYS_VARIABLE = [\App\Services\VariableFreeDays\EasterFriday::class, \App\Services\VariableFreeDays\EasterMonday::class];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_workdays', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained();
            $table->string('work_days', 7);
            $table->json('free_days_fixed')->nullable();
            $table->json('free_days_variable')->nullable();
        });

        DB::table('user_workdays')->insert([
            ['user_id' => 1, 'work_days' => '12345', 'free_days_fixed' => json_encode(self::FREE_DAYS_FIXED), 'free_days_variable' => json_encode(self::FREE_DAYS_VARIABLE)],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_workdays');
    }
};
