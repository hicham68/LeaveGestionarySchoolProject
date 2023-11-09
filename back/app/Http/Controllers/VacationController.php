<?php

namespace App\Http\Controllers;

use App\DTO\VacationRequestDTO;
use App\Models\Employee;
use App\Models\VacationRequest;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\VacationType;
use App\Models\Reason;
use App\Services\ValidateVacationService;

class VacationController extends Controller
{
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->validateAndCreateVacationRequest($request);
    }

    public function getAll(): \Illuminate\Http\JsonResponse
    {

        $vacations = VacationRequest::select(
            'id',
            'employee_id',
            'start_date',
            'end_date',
            'reason_id',
            'vacation_type_id'
        )
            ->get();

        return response()->json([
            'vacations' => $vacations
        ], 200);
    }

    public function getVacationRequest(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->route('id');
        $vacation = VacationRequest::select(
            'id',
            'employee_id',
            'start_date',
            'end_date',
            'reason_id',
            'vacation_type_id'
        )
            ->where('id', $id)
            ->first();

        if ($vacation) {
            return response()->json([
                'vacation' => $vacation
            ], 200);
        } else {
            return response()->json([
                'message' => "La demande de congé n'existe pas."
            ], 404);
        }
    }

    private function validateAndCreateVacationRequest(Request $request): \Illuminate\Http\JsonResponse
    {
        // check f date is not invalid
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        if ($startDate->greaterThan($endDate)) {
            return response()->json([
                'message' => "La date de début ne peut pas être supérieure à la date de fin."
            ], 400);
        }

        $id = $request->route('id');
        $vacationRequest = VacationRequest::select('*')->where('id', $id)->first()->getOriginal();

        if (!$vacationRequest) {
            return response()->json([
                'message' => "La demande de congé n'existe pas."
            ], 404);
        }

        $employeeId = $vacationRequest['employee_id'];

        $employee = Employee::select('vacation_balance', 'created_at')->where('id', $employeeId)->first()->getOriginal();
        $validateVacationService = new ValidateVacationService();

        $vacationBalance = $validateVacationService->vacationBalanceCalcul(
            $startDate,
            $endDate,
            $employee['vacation_balance']
        );

        $teamAvailability = $validateVacationService->teamAvailabilityCalcul($startDate, $endDate);
        $seniority = $validateVacationService->seniorityCalcul($employee['created_at']);
        $vacationUnpaidRequestedThisYear = $validateVacationService->getVacationUnpaidThisYear($employeeId);

        $exists = VacationRequest::where('employee_id', $employeeId)
            ->where('id', '!=', $id) // Excluez la VacationRequest que vous modifiez
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => "Vous avez déjà demandé des congés pour cette période. Veuillez choisir une autre période."
            ], 400);
        }

        $vacationTypeLabel = $this->getVacationTypeLabel($vacationRequest['vacation_type_id']);
        $vacationReasonLabel = $this->getVacationReasonLabel($vacationRequest['reason_id']);

        $vacationRequestDto = new VacationRequestDTO(
            $employeeId,
            $startDate,
            $endDate,
            $vacationTypeLabel,
            $vacationReasonLabel
        );

        if ($validateVacationService->validateVacation(
            $vacationRequestDto,
            $vacationBalance,
            $teamAvailability,
            $seniority,
            $vacationUnpaidRequestedThisYear
        )) {
            if ($id) {
                $data = [
                    'employee_id' => $employeeId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'vacation_type_id' => $vacationRequest['vacation_type_id'],
                    'reason_id' => $vacationRequest['reason_id']
                ];
                VacationRequest::where('id', $id)->update($data);
            } else {
                $data = [
                    'employee_id' => $employeeId,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'vacation_type_id' => $request->input('vacation_type_id'),
                    'reason_id' => $request->input('reason_id'),
                ];
                VacationRequest::create($data);
            }

            return response()->json([
                'message' => "Votre demande a été acceptée avec succès. Merci !"
            ], 201);
        } else {
            return response()->json([
                'message' => "Désolé, notre système de priorités n'a pas pu accorder votre demande. Si vous avez des préoccupations ou des questions, n'hésitez pas à en discuter avec votre supérieur."
            ], 400);
        }
    }

    public function getVacationRequestByEmployeeId(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->route('id');
        $vacations = VacationRequest::select(
            'id',
            'employee_id',
            'start_date',
            'end_date',
            'reason_id',
            'vacation_type_id'
        )
            ->where('employee_id', $id)
            ->get();

        if ($vacations) {
            return response()->json([
                'vacations' => $vacations
            ], 200);
        } else {
            return response()->json([
                'message' => "La demande de congé n'existe pas."
            ], 404);
        }
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->validateAndCreateVacationRequest($request);
    }

    public function deleteVacationRequest(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->route('id');
        $vacation = VacationRequest::select(
            'id',
            'employee_id',
            'start_date',
            'end_date',
            'reason_id',
            'vacation_type_id'
        )
            ->where('id', $id)
            ->first();

        if ($vacation) {
            VacationRequest::where('id', $id)->delete();
            return response()->json([
                'message' => "La demande de congé a été supprimée avec succès."
            ], 200);
        } else {
            return response()->json([
                'message' => "La demande de congé n'existe pas."
            ], 404);
        }
    }

    private function getVacationTypeLabel(int $vacationTypeId): string
    {
        return VacationType::find($vacationTypeId)->label;
    }

    private function getVacationReasonLabel(int $vacationReasonId): string
    {
        return Reason::find($vacationReasonId)->label;
    }
}
