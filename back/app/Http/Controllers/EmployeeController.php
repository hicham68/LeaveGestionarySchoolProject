<?php

namespace App\Http\Controllers;


use App\Models\Employee;
use Illuminate\Http\Request;



class EmployeeController extends Controller
{
    public function getVacationBalance(Request $request): \Illuminate\Http\JsonResponse
    {
        $id = $request->route('id');
        $employee = Employee::select(
            'id',
            'vacation_balance'
        )
            ->where('id', $id)
            ->first();

        if ($employee) {
            return response()->json([
                'employee' => $employee
            ], 200);
        } else {
            return response()->json([
                'message' => "L'employé n'existe pas."
            ], 404);
        }
    }
}
