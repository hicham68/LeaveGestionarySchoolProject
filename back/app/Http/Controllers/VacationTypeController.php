<?php

namespace App\Http\Controllers;


use App\Models\VacationType;
use Illuminate\Http\Request;



class VacationTypeController extends Controller
{
   
    public function getVacationTypes(): \Illuminate\Http\JsonResponse
    {
      $vacationTypes = VacationType::select(
            'id',
            'label',
        )->get();
      return response()->json(
         $vacationTypes
      );
    }
}
