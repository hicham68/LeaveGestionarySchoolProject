<?php

namespace App\Http\Controllers;


use App\Models\Reason;
use Illuminate\Http\Request;



class ReasonController extends Controller
{
   
    public function getReasons(): \Illuminate\Http\JsonResponse
    {
      $reasons = Reason::select(
            'id',
            'label',
        )->get();
      return response()->json(
         $reasons
      );
    }
}
