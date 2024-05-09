<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $work_days
 * @property array|null $free_days_fixed
 * @property array|null $free_days_variable
 */
abstract class WorkDay extends Model
{
    // ocekavana format fixniho data pracovniho klidu v databazi
    public const FREE_DAYS_FIXED_DATE_FORMAT = 'd.m';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d',
            'updated_at' => 'datetime:Y-m-d',
            'free_days_fixed' => 'array',
            'free_days_variable' => 'array',
        ];
    }
}
