<?php

namespace App\Interfaces;

interface VariableFreeDayInterface
{
    public function isFreeDay(\DateTime $dateTime): bool;
}
