<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\UseWorkDaySettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Date\CheckRequest;
use App\Interfaces\WorkDayServiceInterface;
use App\Models\User;
use App\Models\WorkDay;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DateController extends Controller
{
    use UseWorkDaySettings;

    // co cekam z frontendu
    public const INPUT_DATE_FORMAT = 'Y-m-d';

    public function __construct(protected WorkDayServiceInterface $workDayService)
    {
        // fake login
        Auth::login(User::find(1));
    }

    public function check(CheckRequest $checkRequest): JsonResponse
    {
        $data = $checkRequest->safe(['date', 'settings']);

        $data['date'] = \DateTime::createFromFormat(self::INPUT_DATE_FORMAT, $data['date']);

        // prevod na datetime muze selhat, proto jeste jednou osetruji vstup
        if (! $data['date'] instanceof \DateTime) {
            return response()->json(['errors' => [
                'date' => ['Invalid date'],
            ]], 422);
        }

        $workDaySettings = $this->getWorkDaySettings($data['settings']);

        // osetruji, jestli nastaveni existuji
        if (! $workDaySettings instanceof WorkDay) {
            return response()->json(['errors' => [
                'settings' => ['Settings not found'],
            ]], 422);
        }

        // zjisteni, zda se jedna o pracovni den
        $isWorkDay = $this->workDayService->isWorkDay($data['date'], $workDaySettings);

        return response()->json(['isWorkDay' => $isWorkDay]);
    }
}
