<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProbationersList extends Controller
{
    public function ProbationerList(Request $request)
    {
        try{
        isset($request->squad_id) ? $squad_Id = remove_specialcharcters($request->squad_id) : $squad_Id = '';
        $data   = DB::table('assign_probationers_to_squad')->where('Squad_Id', $squad_Id)->get();
        $response   = [
            'status'    => "success",
            'data'      => $data,
        ];
    }
    catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        $response   = [
            'code' => "200",
            'status'    => "failed",
            'message' => 'Something went wrong Please try again'
        ];
    }
        return response()->json($response, 200);
    }
}
