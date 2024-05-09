<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\UseWorkDaySettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Ticket\CalcEndTimeRequest;
use App\Interfaces\TicketServiceInterface;
use App\Models\User;
use App\Models\WorkDay;
use App\Objects\WorkDayPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    use UseWorkDaySettings;

    // co ocekavam z frontendu
    public const INPUT_STARTDATE_FORMAT = 'Y-m-d H:i';

    public function __construct(protected TicketServiceInterface $ticketService)
    {
        // fake login
        Auth::login(User::find(1));
    }

    public function calcEndTime(CalcEndTimeRequest $calcEndTimeRequest): JsonResponse
    {
        $data = $calcEndTimeRequest->safe(['start', 'estimate', 'workdays', 'settings', 'hoursStart', 'hoursEnd']);
        $data['start'] = \DateTime::createFromFormat(self::INPUT_STARTDATE_FORMAT, $data['start']);

        // prevod na datetime muze selhat, proto jeste jednou osetruji vstup
        if (! $data['start'] instanceof \DateTime) {
            return response()->json(['errors' => [
                'start' => ['Invalid date'],
            ]], 422);
        }

        $workDaySettings = null;

        if ($data['workdays']) {
            $workDaySettings = $this->getWorkDaySettings($data['settings']);
            // osetruji, jestli nastaveni existuji
            if (! $workDaySettings instanceof WorkDay) {
                return response()->json(['errors' => [
                    'settings' => ['Settings not found'],
                ]], 422);
            }
        }

        // prevadim na objekt, jelikoz v realne app bych tohle dal spise do DB v ramci nastaveni daneho klienta
        $workDayPeriod = new WorkDayPeriod(
            startHours: $data['hoursStart']['hours'],
            startMinutes: $data['hoursStart']['minutes'],
            endHours: $data['hoursEnd']['hours'],
            endMinutes: $data['hoursEnd']['minutes']
        );

        // vypocet konce zadaneho casu
        $endTime = $this->ticketService->calculateEndTime($data['start'], $data['estimate'], $data['workdays'], $workDaySettings, $workDayPeriod);

        return response()->json(['endTime' => $endTime->format('d.m.Y H:i')]);
    }
}
