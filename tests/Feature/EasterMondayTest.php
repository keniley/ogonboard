<?php

namespace Tests\Feature;

use App\Services\VariableFreeDays\EasterMonday;
use Tests\TestCase;

class EasterMondayTest extends TestCase
{
    public function test_is_free_day_correct_date_2024(): void
    {
        /** @var EasterMonday $service */
        $service = $this->instance(EasterMonday::class, new EasterMonday());
        $easterFriday = $service->isFreeDay(new \DateTime('2024-04-01'));

        $this->assertTrue($easterFriday);
    }

    public function test_is_free_day_incorrect_date_2024(): void
    {
        /** @var EasterMonday $service */
        $service = $this->instance(EasterMonday::class, new EasterMonday());
        $easterFriday = $service->isFreeDay(new \DateTime('2024-03-30'));

        $this->assertFalse($easterFriday);
    }

    public function test_is_free_day_correct_date_2025(): void
    {
        /** @var EasterMonday $service */
        $service = $this->instance(EasterMonday::class, new EasterMonday());
        $easterFriday = $service->isFreeDay(new \DateTime('2025-04-21'));

        $this->assertTrue($easterFriday);
    }

    public function test_is_free_day_incorrect_date_2025(): void
    {
        /** @var EasterMonday $service */
        $service = $this->instance(EasterMonday::class, new EasterMonday());
        $easterFriday = $service->isFreeDay(new \DateTime('2025-03-30'));

        $this->assertFalse($easterFriday);
    }
}
