<?php

namespace App\Services;

use App\Interfaces\TicketServiceInterface;
use App\Interfaces\WorkDayServiceInterface;
use App\Models\WorkDay;
use App\Objects\WorkDayPeriod;

class TicketService implements TicketServiceInterface
{
    public function __construct(protected WorkDayServiceInterface $workDayService)
    {
    }

    public function calculateEndTime(\DateTime $startDate, int $estimate, bool $checkWorkDays, ?WorkDay $workDay, WorkDayPeriod $workDayPeriod): \DateTime
    {
        $workStartTime = $workDayPeriod->getStartDateTime();
        $workEndTime = $workDayPeriod->getEndDateTime();

        // nastavení začátku úkolu na nejbližší pracovní den, pokud začíná mimo pracovní dobu nebo o víkendu
        while (! $this->isWorkDay($startDate, $checkWorkDays, $workDay) || $startDate->format('H:i') < $workStartTime->format('H:i') || $startDate->format('H:i') > $workEndTime->format('H:i')) {
            if ($startDate->format('H:i') >= $workEndTime->format('H:i')) {
                $startDate->modify('+1 day');
            }
            $startDate->setTime((int) $workStartTime->format('H'), (int) $workStartTime->format('i'));
            while (! $this->isWorkDay($startDate, $checkWorkDays, $workDay)) {
                $startDate->modify('+1 day');
            }
        }

        $workStartTime->setDate((int) $startDate->format('Y'), (int) $startDate->format('m'), (int) $startDate->format('d'));
        $workEndTime->setDate((int) $startDate->format('Y'), (int) $startDate->format('m'), (int) $startDate->format('d'));

        // cyklus, který přidává minutu po minutě a kontroluje pracovní dobu
        $current = clone $startDate;
        $minutesAdded = 0;
        $minuteWasAdded = false;

        while ($minutesAdded < $estimate) {
            $current->modify('+1 minute');
            $minutesAdded++;
            $minuteWasAdded = true;
            // pokud je čas mimo pracovní dobu, posune se na začátek dalšího pracovního dne
            if ($current > $workEndTime || ! $this->isWorkDay($current, $checkWorkDays, $workDay)) {
                do {
                    $current->modify('+1 day');
                    if ($minuteWasAdded) {
                        $current->modify('-1 minute');
                        $minutesAdded--;
                        $minuteWasAdded = false;
                    }
                } while (! $this->isWorkDay($current, $checkWorkDays, $workDay)); // cyklus pokračuje, dokud není nalezen pracovní den

                $current->setTime((int) $workStartTime->format('H'), (int) $workStartTime->format('i'));
                $workEndTime->setDate((int) $current->format('Y'), (int) $current->format('m'), (int) $current->format('d'));
                $workEndTime->setTime((int) $workEndTime->format('H'), (int) $workEndTime->format('i'));
            }
        }

        return $current;
    }

    protected function isWorkDay(\DateTime $date, bool $checkWorkDays, ?WorkDay $workDay): bool
    {
        if (! $checkWorkDays) {
            return true;
        }

        return $this->workDayService->isWorkDay($date, $workDay);
    }
}
