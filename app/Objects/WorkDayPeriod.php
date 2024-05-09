<?php

namespace App\Objects;

readonly class WorkDayPeriod
{
    public function __construct(
        private int $startHours,
        private int $startMinutes,
        private int $endHours,
        private int $endMinutes
    ) {
    }

    public function getStartHours(): int
    {
        return $this->startHours;
    }

    public function getStartMinutes(): int
    {
        return $this->startMinutes;
    }

    public function getEndHours(): int
    {
        return $this->endHours;
    }

    public function getEndMinutes(): int
    {
        return $this->endMinutes;
    }

    public function getStartDateTime(): \DateTime
    {
        $date = new \DateTime();
        $date->setTime($this->startHours, $this->startMinutes);

        return $date;
    }

    public function getEndDateTime(): \DateTime
    {
        $date = new \DateTime();
        $date->setTime($this->endHours, $this->endMinutes);

        return $date;
    }
}
