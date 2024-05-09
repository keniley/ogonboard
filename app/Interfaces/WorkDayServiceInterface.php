<?php

namespace App\Interfaces;

use App\Models\WorkDay;

interface WorkDayServiceInterface
{
    public function isWorkDay(\DateTime $date, WorkDay $workDaySettings): bool;
}
