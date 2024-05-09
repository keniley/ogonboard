<?php

namespace App\Http\Controllers\Api\Traits;

use App\Models\GlobalWorkDay;
use App\Models\WorkDay;
use Illuminate\Support\Facades\Auth;

trait UseWorkDaySettings
{
    protected function getWorkDaySettings(int $settingsId): ?WorkDay
    {
        if ($settingsId === 0) {
            return Auth::user()->workDay;
        }

        return GlobalWorkDay::find($settingsId);
    }
}
