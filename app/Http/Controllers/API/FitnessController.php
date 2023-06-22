<?php

namespace App\Http\Controllers\API;

use App\Models\GeneralAssesment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Exception;

class FitnessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Submit General Assesment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function general_assesment(Request $request)
    {
        $result = [];
        $errors = [];

       // $form_data = $request->all();

       $punctuality = ($request->punctuality);
       $behaviour = ($request->behaviour);
       $teamspirit = ($request->teamspirit);
       $learningefforts = ($request->learningefforts);
       $responsibility = ($request->responsibility);
       $leadership = ($request->leadership);
       $commandcontrol = ($request->commandcontrol);
       $sportsmanship = ($request->sportsmanship);


        // if(!in_array($punctuality, valid_grades())) {
        //     $errors[] = "Invalid Punctuality grade.";
        // }
        // else{
        //     $punctuality = explode("-", $punctuality);
        // }
        // if(!in_array($behaviour, valid_grades())) {
        //     $errors[] = "Invalid Behaviour grade.";
        // }
        // else{
        //     $behaviour = explode("-", $behaviour);
        // }
        // if(!in_array($teamspirit, valid_grades())) {
        //     $errors[] = "Invalid teamspirit grade.";
        // }
        // else{
        //     $teamspirit = explode("-", $teamspirit);
        // }
        // if(!in_array($learningefforts, valid_grades())) {
        //     $errors[] = "Invalid learning efforts grade.";
        // }
        // else{
        //     $learningefforts = explode("-", $learningefforts);
        // }
        // if(!in_array($responsibility, valid_grades())) {
        //     $errors[] = "Invalid responsibility grade.";
        // }
        // else{
        //     $responsibility = explode("-", $responsibility);
        // }
        // if(!in_array($leadership, valid_grades())) {
        //     $errors[] = "Invalid leadership grade.";
        // }
        // else{
        //     $leadership = explode("-", $leadership);
        // }
        // if(!in_array($commandcontrol, valid_grades())) {
        //     $errors[] = "Invalid commandcontrol grade.";
        // }
        // else{
        //     $commandcontrol = explode("-", $commandcontrol);
        // }
        // if(!in_array($sportsmanship, valid_grades())) {
        //     $errors[] = "Invalid sportsmanship grade.";
        // }
        // else{
        //     $sportsmanship = explode("-", $sportsmanship);
        // }

        // if(!empty($errors)) {
        //     return response()->json([
        //         'code'  => 401,
        //         'message' => "Invalid data"
        //     ], 401);
        // }

        $user_role = Auth::user()->role;
        if ($user_role == 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
            isset($request->probationer_id) ? $probationer_id = remove_specialcharcters($request->probationer_id) : $probationer_id = '';
        } else {
            $user_id = Auth::id();
            $probationer_id = probationer_id($user_id);
            if($probationer_id != $request->probationer_id)
            {
                $response   = [
                    'code' => "401",
                    'message'  => "Unauthorized"
                ];
                return response()->json($response, 401);
            }
        }


        $user  = Auth::user();
        $staff_id      = $user->id;
        $staff_role    = $user->role;

        $probationer_id = remove_specialcharcters($request->probationer_id);
        $month           = $request->month;
        $year           = $request->year;

        if(!is_numeric($probationer_id))
        {
            $response   = [
                'code' => "201",
                'status'    => "success",
                'message'  => "Invalid Probationer Id"
            ];
            return response()->json($response, 200);
        }

            GeneralAssesment::updateOrCreate(
                [
                    'probationer_id'    => $probationer_id,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'punctuality' => isset($punctuality) ? $punctuality: "",
                    'behaviour' => isset($behaviour) ? $behaviour: "",
                    'teamspirit' => isset($teamspirit) ? $teamspirit: "",
                    'learningefforts' => isset($learningefforts) ? $learningefforts: "",
                    'responsibility' => isset($responsibility) ? $responsibility: "",
                    'leadership' => isset($leadership) ? $leadership: "",
                    'commandcontrol' => isset($commandcontrol) ? $commandcontrol: "",
                    'sportsmanship' => isset($sportsmanship) ? $sportsmanship: "",
                    'staff_id' => $staff_id,
                    'staff_role' => $staff_role,
                ]
            );

            $result['code']     = 200;
            $result['status']   = 'success';
            $result['message']  = 'Details saved';
        // } catch (Exception $e) {
        //     $result['code']     = 400;
        //     $result['status']   = 'error';
        //     $result['message']  = 'Something went wrong Please try Again';
        // }

        return response()->json($result, 200);
    }

    /**
     * Submit General Assesment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function general_assesment_view(Request $request)
    {
        $errors = [];

        $user_role = Auth::user()->role;

        // if ($user_role !== 'drillinspector' || $user_role !== 'si' || $user_role !== 'adi') {
        //     return response()->json([
        //         'code'      => '401',
        //         'status'    => 'error',
        //         'message'   => 'Unauthorized access.',
        //     ], 200);
        // }

        isset($request->probationer_id) ? $probationer_id = remove_specialcharcters($request->probationer_id) : $probationer_id = '';

        isset($request->month) ? $month = $request->month : $month = '';
        isset($request->year) ? $year = $request->year : $year = '';

        if( empty($probationer_id) ) {
            $errors[]   = "probationer_id required";
        }

        if( !empty($errors) ) {
            return response()->json([
                'code'      => '400',
                'status'    => 'error',
                'message'   => implode(', ', $errors),
            ], 200);
        }

        $GeneralAssesment   = GeneralAssesment::where('probationer_id', $probationer_id)
            ->where('month', $month)->where('year', $year)->first();

        $qData  = [];
        if(!empty($GeneralAssesment)) {
            $qData  = $GeneralAssesment->toArray();
        }

        $columns = [
            'id',
            'probationer_id',
            'punctuality',
            'behaviour',
            'teamspirit',
            'learningefforts',
            'responsibility',
            'leadership',
            'commandcontrol',
            'sportsmanship',
            'month',
            'year'
        ];;

        $data   = [];
        foreach($columns as $column) {
            $val    = isset($qData[$column])? $qData[$column] : '';
            if( empty($val) ) {
                $val    = '';

                if($column === 'id') {
                    $val    = 0;
                }
                if($column === 'probationer_id') {
                    $val    = $probationer_id;
                }
                if($column === 'month') {
                    $val    = $month;
                }

                if($column === 'year') {
                    $val    = $year;
                }

            }

            $data[$column] = $val;
        }
        return response()->json([
            'code'      => '200',
            'status'    => 'success',
            'message'   => '',
            'data'   => $data,
        ], 200);
    }

    public function general_assesment_list_view(Request $request)
    {

        isset($request->probationer_id) ? $probationer_id = remove_specialcharcters($request->probationer_id) : $probationer_id = '';
        isset($request->month) ? $month = $request->month : $month = '';
        isset($request->year) ? $year = $request->year : $year = '';

        if( empty($probationer_id) ) {
            $errors[]   = "probationer_id required";
        }
        if( empty($month)|| empty($year) ) {
            $errors[]   = "month and year required.";
        }

        if( !empty($errors) ) {
            return response()->json([
                'code'      => '400',
                'status'    => 'error',
                'message'   => implode(', ', $errors),
            ], 200);
        }

        $qData   = GeneralAssesment::where('probationer_id', $probationer_id)
        ->where('month', $month)->where('year', $year)->get();

       // $qData['date'] = $qData['month'] . '/' . $qData['year']

        if(count($qData) == '0')
        {
            return response()->json([
                'code'      => '204',
                'status'    => 'success',
                'message'   => 'No data',
            ], 200);
        }
       // $qData  = [];
        $columns = [
            'id',
            'probationer_id',
            'punctuality',
            'behaviour',
            'teamspirit',
            'learningefforts',
            'responsibility',
            'leadership',
            'commandcontrol',
            'sportsmanship',
            'month',
            'year'
        ];;

        $data   = [];
       foreach($qData as $qDatas)
       {
        foreach($columns as $column) {
            $val    = isset($qDatas[$column])? ($qDatas[$column]) : '';
            if( empty($val) ) {
                $val    = '';

                if($column === 'id') {
                    $val    = 0;
                }
                if($column === 'probationer_id') {
                    $val    = $probationer_id;
                }
                if($column === 'month') {
                    $val    = $month . '/' . $year;
                }
                // if($column === 'year') {
                //     $val    = $year;
                // }
                }
                $data[$column] = $val;
            }
            $data['date'] = $data['month'] . '/' . $data['year'];
            $sDt[] = $data;
            unset($data);
        }

        return response()->json([
            'code'      => '200',
            'status'    => 'success',
            'message'   => '',
            'data'   => $sDt,
        ], 200);
    }

}
