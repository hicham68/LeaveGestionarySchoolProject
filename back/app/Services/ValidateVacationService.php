<?php

namespace App\Services;

use Illuminate\Support\Carbon as Date;
use App\DTO\VacationRequestDTO;
use Carbon\CarbonPeriod;
use App\Models\VacationRequest;
use App\Models\Employee;

class ValidateVacationService
{
    public function validateVacation(
        VacationRequestDTO $vacationRequest,
        int                $vacationBalance,
        int                $teamAvailability,
        int                $seniority,
        int                $vacationUnpaidRequestedThisYear
    ): bool {
        // Vérification du solde de congés suffisant
        $hasSufficientBalance = $this->hasSufficientBalance($vacationRequest, $vacationBalance);

        // Vérification si c'est un congé non payé
        $isUnpaidLeave = $this->isUnpaidLeave($vacationRequest);

        // Vérification de la disponibilité de l'équipe
        $isTeamAvailable = $this->isTeamAvailable($teamAvailability);

        // Vérification de l'ancienneté
        $isSenior = $this->isSenior($seniority);

        $isTeamPresent = $this->isTeamPresent($teamAvailability);

        // Vérification si le motif est important
        $isImportantReason = $this->isImportantReason($vacationRequest);

        // Vérification si la date de début est trop éloignée dans le futur
        $isTooFarInFuture = $this->isTooFarInFuture($vacationRequest);

        // Vérification du nombre de jours demandés cette année
        $isTooManyDaysRequested = $vacationUnpaidRequestedThisYear >= 5;
        if (!$isUnpaidLeave && $vacationBalance <= 0) {
            return false;
        }
        if ($isTooFarInFuture) {
            return false;
        }
        if ($isImportantReason) {
            return true;
        }
        if ($isTooManyDaysRequested && $isUnpaidLeave) {
            return false;
        }

        if ($hasSufficientBalance && $isTeamPresent && $isSenior) {
            return true;
        }
        if ($isUnpaidLeave && $isTeamAvailable) {
            return true;
        }
        if ($isUnpaidLeave && !$isTeamAvailable) {
            return false;
        }
        if ($hasSufficientBalance && $isTeamAvailable) {
            return true;
        }

        return false;
    }


    private function hasSufficientBalance(VacationRequestDTO $vacationRequest, int $vacationBalance): bool
    {
        return $this->vacationBalanceCalcul(
            $vacationRequest->start_date,
            $vacationRequest->end_date,
            $vacationBalance
        ) >= 0;
    }

    private function isTooFarInFuture(VacationRequestDTO $vacationRequest): bool
    {
        $now = Date::now();
        $diff = $now->diffInDays($vacationRequest->start_date);
        return $diff > 365;
    }

    private function isUnpaidLeave(VacationRequestDTO $vacationRequest): bool
    {
        return $vacationRequest->vacation_type === 'non payé';
    }

    private function isTeamPresent(int $teamAvailability): bool
    {
        return $teamAvailability > 20;
    }

    private function isTeamAvailable(int $teamAvailability): bool
    {
        return $teamAvailability >= 50;
    }

    private function isSenior(int $seniority): bool
    {
        return $seniority >= 1;
    }

    private function isImportantReason(VacationRequestDTO $vacationRequest): bool
    {
        $importantReasons = ['Maladie grave', 'Décès dans la famille'];
        return in_array($vacationRequest->reason, $importantReasons);
    }

    public function seniorityCalcul(Date $dateOfHire): int
    {
        $now = Date::now();
        $seniority = $now->diffInYears($dateOfHire);
        return $seniority;
    }

    public function vacationBalanceCalcul(
        Date $startDate,
        Date $endDate,
        int  $vacationBalance
    ): int {
        // Liste des jours fériés à personnaliser
        $holidays = [
            '2023-01-01', // Jour de l'An
            '2023-05-01', // Fête du Travail
            '2023-07-14', // Fête Nationale
            '2023-12-25', // Noël
            // Ajoutez d'autres jours fériés au besoin
        ];

        // Créez une période de dates entre la date de début et la date de fin
        $period = CarbonPeriod::create($startDate, $endDate);

        // compteur pour le nombre de jours de congé
        $vacationDays = 0;


        foreach ($period as $day) {
            // Vérification si le jour est un week-end
            if ($day->isWeekend()) {
                continue; // Ignorez les week-ends
            }

            // formatez le jour pour correspondre au format de la liste des jours fériés
            $formattedDay = $day->format('Y-m-d');

            // Vérification si le jour est un jour férié
            if (in_array($formattedDay, $holidays)) {
                continue; // Ignore les jours fériés français
            }

            // Si ce n'est ni un week-end ni un jour férié, ajoutez-le aux jours de congé
            $vacationDays++;
        }

        // Calculez le nouveau solde de congé après déduction des jours de congé

        $newVacationBalance = $vacationBalance - $vacationDays;

        return $newVacationBalance;
    }

    public function teamAvailabilityCalcul(Date $startDate, Date $endDate): int
    {
        // Récupérez toutes les demandes de congé qui chevauchent la période spécifiée
        $vacationRequests = VacationRequest::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<', $startDate)
                        ->where('end_date', '>', $endDate);
                });
        })->get();

        // Comptez le nombre d'employés absents pendant cette période
        $absentEmployees = $vacationRequests->pluck('employee_id')->unique()->count();

        // Le nombre total d'employés dans l'équipe (à ajuster en fonction de votre modèle de données)
        $totalEmployees = Employee::count();

        // Calculez la disponibilité en pourcentage
        return (($totalEmployees - $absentEmployees) / $totalEmployees) * 100;
    }


    public function getVacationUnpaidThisYear(int $employee_id): int
    {
        // return le nombre de VacationRequest non payé type id 2 pour l'employee_id en paramètre qui ont été créées cette année

        return VacationRequest::where('employee_id', $employee_id)
            ->where('vacation_type_id', 2)
            ->whereYear('created_at', Date::now()->year)
            ->count();
    }
}
