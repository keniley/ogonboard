<?php

namespace Tests\Feature;

use App\Services\VariableFreeDays\EasterFriday;
use Tests\TestCase;

class EasterFridayTest extends TestCase
{
    public function test_is_free_day_correct_date_2024(): void
    {
        /** @var EasterFriday $service */
        $service = $this->instance(EasterFriday::class, new EasterFriday());
        $easterFriday = $service->isFreeDay(new \DateTime('2024-03-29'));

        $this->assertTrue($easterFriday);
    }

    public function test_is_free_day_incorrect_date_2024(): void
    {
        /** @var EasterFriday $service */
        $service = $this->instance(EasterFriday::class, new EasterFriday());
        $easterFriday = $service->isFreeDay(new \DateTime('2024-03-30'));

        $this->assertFalse($easterFriday);
    }

    public function test_is_free_day_correct_date_2025(): void
    {
        /** @var EasterFriday $service */
        $service = $this->instance(EasterFriday::class, new EasterFriday());
        $easterFriday = $service->isFreeDay(new \DateTime('2025-04-18'));

        $this->assertTrue($easterFriday);
    }

    public function test_is_free_day_incorrect_date_2025(): void
    {
        /** @var EasterFriday $service */
        $service = $this->instance(EasterFriday::class, new EasterFriday());
        $easterFriday = $service->isFreeDay(new \DateTime('2025-03-30'));

        $this->assertFalse($easterFriday);
    }
}
