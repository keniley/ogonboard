<?php

namespace App\Interfaces;

use App\Models\WorkDay;
use App\Objects\WorkDayPeriod;

interface TicketServiceInterface
{
    public function calculateEndTime(\DateTime $startDate, int $estimate, bool $checkWorkDays, ?WorkDay $workDay, WorkDayPeriod $workDayPeriod): \DateTime;
}
