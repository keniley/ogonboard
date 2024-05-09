<?php

namespace Tests\Feature;

use App\Interfaces\WorkDayServiceInterface;
use App\Models\GlobalWorkDay;
use App\Services\VariableFreeDays\EasterFriday;
use App\Services\WorkDayService;
use Tests\TestCase;

class WorkDayServiceTest extends TestCase
{
    public function test_is_work_day_weekday_settings_1(): void
    {
        $workDay = new GlobalWorkDay();
        $workDay->work_days = '1,2,3,4,5';

        $service = $this->instance(WorkDayServiceInterface::class, new WorkDayService());

        // monday
        $result = $service->isWorkDay(new \DateTime('2024-05-06'), $workDay);

        $this->assertTrue($result);
    }

    public function test_is_work_day_weekday_settings_2(): void
    {
        $workDay = new GlobalWorkDay();
        $workDay->work_days = '1,2,3,4,5';

        $service = $this->instance(WorkDayServiceInterface::class, new WorkDayService());

        // sunday
        $result = $service->isWorkDay(new \DateTime('2024-05-11'), $workDay);

        $this->assertFalse($result);
    }

    public function test_is_work_day_weekday_settings_3(): void
    {
        $workDay = new GlobalWorkDay();
        $workDay->work_days = '';

        $service = $this->instance(WorkDayServiceInterface::class, new WorkDayService());

        // monday
        $result = $service->isWorkDay(new \DateTime('2024-05-06'), $workDay);

        $this->assertFalse($result);
    }

    public function test_is_work_day_if_is_weekday_but_in_fixed(): void
    {
        $workDay = new GlobalWorkDay();
        $workDay->work_days = '1,2,3,4,5';
        $workDay->free_days_fixed = [
            '01.05',
        ];

        $service = $this->instance(WorkDayServiceInterface::class, new WorkDayService());

        // wednesday but is in fixed
        $result = $service->isWorkDay(new \DateTime('2024-05-01'), $workDay);

        $this->assertFalse($result);
    }

    public function test_is_work_day_if_is_weekday_but_in_variable(): void
    {
        $workDay = new GlobalWorkDay();
        $workDay->work_days = '1,2,3,4,5';
        $workDay->free_days_variable = [
            EasterFriday::class,
        ];

        $service = $this->instance(WorkDayServiceInterface::class, new WorkDayService());

        // friday but is in variable
        $result = $service->isWorkDay(new \DateTime('2024-03-29'), $workDay);

        $this->assertFalse($result);
    }
}
