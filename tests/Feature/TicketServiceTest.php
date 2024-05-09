<?php

namespace Tests\Feature;

use App\Interfaces\WorkDayServiceInterface;
use App\Models\GlobalWorkDay;
use App\Objects\WorkDayPeriod;
use App\Services\TicketService;
use App\Services\WorkDayService;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    protected function getTicketService(): TicketService
    {
        $workDayService = $this->instance(WorkDayServiceInterface::class, new WorkDayService());

        return $this->instance(TicketService::class, new TicketService($workDayService));
    }

    protected function getCzWorkDay(): GlobalWorkDay
    {
        $workDay = new GlobalWorkDay();
        $workDay->work_days = '1,2,3,4,5';
        $workDay->free_days_fixed = [
            '01.01', '01.05', '08.05', '05.07', '06.07', '28.09', '28.10', '17.11', '24.12', '25.12', '26.12',
        ];
        $workDay->free_days_variable = [
            \App\Services\VariableFreeDays\EasterFriday::class, \App\Services\VariableFreeDays\EasterMonday::class,
        ];

        return $workDay;
    }

    /**
     *  The work starts on a non-working day, but the day type is not checked
     */
    public function test_calculate_without_workdays_start_sunday(): void
    {
        $service = $this->getTicketService();

        $endTime = $service->calculateEndTime(
            startDate: new \DateTime('2024-05-05 09:00:00'),                                // sunday
            estimate: 60,                                                                           // estimate 1 hour
            checkWorkDays: false,                                                                   // do not check work days
            workDay: null,                                                                          // workday is not checked
            workDayPeriod: new WorkDayPeriod(9, 0, 16, 0)   // working from  9 to 16
        );

        $this->assertSame(
            '2024-05-05 10:00:00',          // sunday at 10
            $endTime->format('Y-m-d H:i:s')
        );
    }

    /**
     *  The work starts on a non-working day and the day type is checked
     */
    public function test_calculate_with_workdays_start_sunday(): void
    {
        $service = $this->getTicketService();

        $endTime = $service->calculateEndTime(
            startDate: new \DateTime('2024-05-05 09:00:00'),                                   // sunday
            estimate: 60,                                                                               // estimate 1 hour
            checkWorkDays: true,                                                                        // check work days
            workDay: $this->getCzWorkDay(),                                                             // CZ workdays, sunday is free day
            workDayPeriod: new WorkDayPeriod(9, 0, 16, 0)       // working from  9 to 16
        );

        $this->assertSame(
            '2024-05-06 10:00:00',          // monday at 10
            $endTime->format('Y-m-d H:i:s')
        );
    }

    /**
     *  Work begins after the start of working hours
     */
    public function test_calculate_start_after_start_day(): void
    {
        $service = $this->getTicketService();

        $endTime = $service->calculateEndTime(
            startDate: new \DateTime('2024-05-06 11:30:00'),                                    // monday 11:30
            estimate: 60,                                                                               // estimate 1 hour
            checkWorkDays: false,                                                                       // do not check work days
            workDay: null,                                                                              // work day is not checked
            workDayPeriod: new WorkDayPeriod(9, 0, 16, 0)       // working from  9 to 16
        );

        $this->assertSame(
            '2024-05-06 12:30:00',              // monday at 12:30
            $endTime->format('Y-m-d H:i:s')
        );
    }

    /**
     * Work begins before the start of working hours
     */
    public function test_calculate_start_before_start_day(): void
    {
        $service = $this->getTicketService();

        $endTime = $service->calculateEndTime(
            startDate: new \DateTime('2024-05-06 07:30:00'),                                    // monday 7:30
            estimate: 60,                                                                               // estimate 1 hour
            checkWorkDays: false,                                                                       // do not check work days
            workDay: null,                                                                              // work day is not checked
            workDayPeriod: new WorkDayPeriod(9, 0, 16, 0)       // working from  9 to 16
        );

        $this->assertSame(
            '2024-05-06 10:00:00',              // monday at 10:00
            $endTime->format('Y-m-d H:i:s')
        );
    }

    /**
     * Duration of work over several days. The day type is not checked
     */
    public function test_calculate_duration_over_several_days_without_workdays(): void
    {
        $service = $this->getTicketService();

        $endTime = $service->calculateEndTime(
            startDate: new \DateTime('2024-05-06 10:00:00'),                                    // monday 10:00
            estimate: 60 * 10,                                                                          // estimate 10 hours
            checkWorkDays: false,                                                                       // do not check work days
            workDay: null,                                                                              // work day is not checked
            workDayPeriod: new WorkDayPeriod(9, 0, 16, 0)       // working from  9 to 16
        );

        $this->assertSame(
            '2024-05-07 13:00:00',              // tuesday at 13:00
            $endTime->format('Y-m-d H:i:s')
        );
    }

    /**
     * Duration of work over several days. The day type is checked
     */
    public function test_calculate_duration_over_several_days_with_workdays(): void
    {
        $service = $this->getTicketService();

        $endTime = $service->calculateEndTime(
            startDate: new \DateTime('2024-05-03 10:00:00'),                                    // friday 10:00
            estimate: 60 * 10,                                                                          // estimate 10 hours
            checkWorkDays: true,                                                                        // check work days
            workDay: $this->getCzWorkDay(),                                                             // CZ work days - SAT & SUN are free days
            workDayPeriod: new WorkDayPeriod(9, 0, 16, 0)       // working from  9 to 16
        );

        $this->assertSame(
            '2024-05-06 13:00:00',              // monday at 13:00
            $endTime->format('Y-m-d H:i:s')
        );
    }
}
