<?php

namespace App\Services\VariableFreeDays;

use App\Interfaces\VariableFreeDayInterface;
use App\Services\VariableFreeDays\Traits\EasterTrait;

class EasterMonday implements VariableFreeDayInterface
{
    use EasterTrait;

    public function isFreeDay(\DateTime $dateTime): bool
    {
        $easterSunday = $this->getEasterSunday((int) $dateTime->format('Y'));
        $easterMonday = clone $easterSunday;
        $easterMonday->modify('+1 day');

        return $dateTime->format('Y-m-d') === $easterMonday->format('Y-m-d');
    }
}
