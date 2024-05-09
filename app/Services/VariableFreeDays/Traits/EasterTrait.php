<?php

namespace App\Services\VariableFreeDays\Traits;

trait EasterTrait
{
    protected function getEasterSunday(int $year): \DateTime
    {
        $a = $year % 19;
        $b = $year % 4;
        $c = $year % 7;
        $d = (19 * $a + 24) % 30;
        $e = (2 * $b + 4 * $c + 6 * $d + 5) % 7;
        $day = 22 + $d + $e;
        $month = 3;
        if ($day > 31) {
            $month = 4;
            $day -= 31;
        }

        return new \DateTime("$year-$month-$day");
    }
}
