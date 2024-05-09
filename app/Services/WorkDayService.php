<?php

namespace App\Services;

use App\Interfaces\VariableFreeDayInterface;
use App\Interfaces\WorkDayServiceInterface;
use App\Models\WorkDay;
use Illuminate\Support\Facades\Log;

class WorkDayService implements WorkDayServiceInterface
{
    public function isWorkDay(\DateTime $date, WorkDay $workDaySettings): bool
    {
        return $this->isWeekday($date, $workDaySettings) && ! $this->isFreeDay($date, $workDaySettings);
    }

    protected function isWeekday(\DateTime $date, WorkDay $workDaySettings): bool
    {
        $day = $date->format('N');

        return str_contains($workDaySettings->work_days, $day);
    }

    protected function isFreeDay(\DateTime $date, WorkDay $workDaySettings): bool
    {
        if ($this->isFixedFreeDay($date, $workDaySettings)) {
            return true;
        }

        return $this->isVariableFreeDay($date, $workDaySettings);
    }

    protected function isFixedFreeDay(\DateTime $date, WorkDay $workDaySettings): bool
    {
        if (! is_array($workDaySettings->free_days_fixed)) {
            return false;
        }

        foreach ($workDaySettings->free_days_fixed as $freeDay) {
            if ($freeDay === $date->format(WorkDay::FREE_DAYS_FIXED_DATE_FORMAT)) {
                return true;
            }
        }

        return false;
    }

    protected function isVariableFreeDay(\DateTime $date, WorkDay $workDaySettings): bool
    {
        if (! is_array($workDaySettings->free_days_variable)) {
            return false;
        }

        foreach ($workDaySettings->free_days_variable as $freeDay) {
            try {
                $service = $this->getFreeDaysVariableInstance($freeDay);
            } catch (\Exception $e) {
                Log::error($e->getMessage());

                continue;
            }

            // skip foreach in first true
            if ($service->isFreeDay($date)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function getFreeDaysVariableInstance(string $freeDay): VariableFreeDayInterface
    {
        $instance = resolve($freeDay);

        if (! $instance instanceof VariableFreeDayInterface) {
            throw new \InvalidArgumentException('Class '.$freeDay.' must implement '.VariableFreeDayInterface::class);
        }

        return $instance;
    }
}
