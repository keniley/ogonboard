<?php

namespace App\Services\VariableFreeDays;

use App\Interfaces\VariableFreeDayInterface;
use App\Services\VariableFreeDays\Traits\EasterTrait;

class EasterFriday implements VariableFreeDayInterface
{
    use EasterTrait;

    public function isFreeDay(\DateTime $dateTime): bool
    {
        $easterSunday = $this->getEasterSunday((int) $dateTime->format('Y'));
        $easterFriday = clone $easterSunday;
        $easterFriday->modify('-2 days');

        return $easterFriday->format('Y-m-d') === $dateTime->format('Y-m-d');
    }
}
