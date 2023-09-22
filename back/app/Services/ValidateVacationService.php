<?php

namespace App\Services;

use Illuminate\Support\Facades\Date;
use App\DTO\VacationRequestDTO;

class ValidateVacationService
{
    public function validateVacation(
        VacationRequestDTO  $vacationRequest,
        int    $vacationBalance,
        int    $teamAvailability,
        int    $seniority,
        string $vacationType,
        string $reason
    ): bool {
        return false
    }

    public function seniorityCalcul(Date $dateOfHire): int
    {
        return 0;
    }

    public function vacationBalanceCalcul(
        Date   $startDate,
        Date   $endDate,
        string $vacationType,
        int    $vacationBalance
    ): int {
        return 0;
    }

    public function teamAvailabilityCalcul(Date $startDate, Date $endDate): int
    {
        return 0;
    }
}
