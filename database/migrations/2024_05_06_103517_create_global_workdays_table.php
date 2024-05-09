<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const FREE_DAYS_FIXED_CZ = [
        '01.01', '01.05', '08.05', '05.07', '06.07', '28.09', '28.10', '17.11', '24.12', '25.12', '26.12',
    ];

    const FREE_DAYS_FIXED_SK = [
        '01.01', '06.01', '01.05', '08.05', '05.07', '29.08', '01.09', '15.09', '01.11', '17.01', '24.12', '25.12', '26.12',
    ];

    const FREE_DAYS_VARIABLE_CZ = [\App\Services\VariableFreeDays\EasterFriday::class, \App\Services\VariableFreeDays\EasterMonday::class];

    const FREE_DAYS_VARIABLE_SK = [\App\Services\VariableFreeDays\EasterFriday::class, \App\Services\VariableFreeDays\EasterMonday::class];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_workdays', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('country_code', 2)->nullable();
            $table->string('work_days', 7);
            $table->json('free_days_fixed')->nullable();
            $table->json('free_days_variable')->nullable();
        });

        // insert default data
        DB::table('global_workdays')->insert([
            ['country_code' => 'CZ', 'work_days' => '12345', 'free_days_fixed' => json_encode(self::FREE_DAYS_FIXED_CZ), 'free_days_variable' => json_encode(self::FREE_DAYS_VARIABLE_CZ)],
            ['country_code' => 'SK', 'work_days' => '12345', 'free_days_fixed' => json_encode(self::FREE_DAYS_FIXED_SK), 'free_days_variable' => json_encode(self::FREE_DAYS_VARIABLE_SK)],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_workdays');
    }
};
