<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\probationer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Probationers extends Controller
{
    /*
        probationer Basic details
    */
    public function ProbationerDetails(Request $request)
    {
        try {

            $user_role = Auth::user()->role;

            if ($user_role == 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request->probationer_id) ? $probationer_Id = remove_specialcharcters($request->probationer_id) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
               // print_r($request->probationer_id);exit;
                if($probationer_Id != $request->probationer_id)
                {
                    $response   = [
                        'code' => "401",
                        'message'  => "Unauthorized"
                    ];
                    return response()->json($response, 401);
                }
            }

            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'editable' => '',
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 201);
            }

            $data  = DB::table('probationers')->where('probationers.id', $probationer_Id)->leftjoin('batches', 'batches.id', '=', 'probationers.batch_id')->first();

            if($data->Religion != '' || $data->Religion != null)
            {
                $editable_flag = 'No';
            }
            else
            {
                $editable_flag = 'Yes';
            }
            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    if (is_null($value)) {
                        $data->$key = "";
                    }
                }
            }
            if (!empty($data)) {
                $response   = [
                    'code'  => '200',
                    'status'    => "success",
                    'editable' => 'Yes',
                    'data'      => $data,
                ];
            } else {
                $response   = [
                    'code'  => '204',
                    'status'    => "failed",
                    'editable' => '',
                    'data'      => "Data is Empty",
                ];
            }
            return response()->json($response, 200);
        } catch (PDOException $e) {
            $response   = [
                'code' => "201",
                'status'    => "failed",
                'editable' => '',
                'message'  => "Something went wrong Please try again"
            ];
            return response()->json($response, 200);
        }
    }

    /*
        Inserting the probationer data from probationer app
    */

    public function ProbationerData(Request $request)
    {
        try {
            $timestamp  = date('Y-m-d H:i:s');
            $request = (json_decode($request->getContent(), true));

            $user_role = Auth::user()->role;
            if ($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            } else {
                $user_id = Auth::id();
                $probationer_Id = probationer_id($user_id);
                if($probationer_Id != $request['probationer_id'])
                    {
                        $response   = [
                            'code' => "401",
                            'message'  => "Unauthorized"
                        ];
                        return response()->json($response, 401);
                    }
            }

            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 201);
            }

            $data   = DB::table('probationers')->where('id', $probationer_Id)->update([
                'Cadre' => remove_specialcharcters($request['cadre']),
                'Category' => remove_specialcharcters($request['category']),
                'Religion' => remove_specialcharcters($request['religion']),
                'MartialStatus' => remove_specialcharcters($request['martial_status']),
                'MotherName' => remove_specialcharcters($request['mother_name']),
                'Moccupation' => remove_specialcharcters($request['mother_occupation']),
                'FatherName' => remove_specialcharcters($request['father_name']),
                'Foccupation' => remove_specialcharcters($request['father_occupation']),
                'Stateofdomicile' => remove_specialcharcters($request['state_domicile']),
                'HomeAddress' => remove_specialcharcters($request['address']),
                'Hometown' => remove_specialcharcters($request['hometown']),
                'District' => remove_specialcharcters($request['district']),
                'State' => remove_specialcharcters($request['state']),
                'Pincode' => remove_specialcharcters($request['pincode']),
                'phoneNumberStd' => remove_specialcharcters($request['phonewithstd']),
                'OtherState' => remove_specialcharcters($request['which_state_in_india']),
                'EmergencyName' => remove_specialcharcters($request['ename']),
                'EmergencyAddress' => remove_specialcharcters($request['eaddress']),
                'EmergencyPhone' => $request['ephonewithstd'],
                'EmergencyEmailId' => $request['eemailid'],
                'updated_at' => $timestamp
            ]);
            $response   = [
                'code'  => 200,
                'status'   => "success",
                'message'   => "Profile Updated Successfully",
            ];
        } catch (PDOException $e) {
            $response   = [
                'code' => 201,
                'status'    => "failed",
                'message'  => "Something went wrong Please try again"
            ];
        }
        return response()->json($response, 200);
    }

    public function image(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
            //isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            $user_role = Auth::user()->role;
        if($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi')
        {
            isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
        }
        else
        {
            $user_id = Auth::id();
            $probationer_Id = probationer_id($user_id);
			 if($probationer_Id != $request['probationer_id'])
                {
                    $response   = [
                        'code' => "401",
                        'message'  => "Unauthorised"
                    ];
                    return response()->json($response, 401);
                }
        }
            isset($request['image']) ? $image = remove_specialcharcters($request['image']) : $image = '';
            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 201);
            }
            if ($image == '') {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Something went wrong Please try again"
                ];
                return response()->json($response, 201);
            }

           //  $image = str_replace('data:image/png;base64,', '', $image);
          //  $image = str_replace(' ', '+', $image);
            $fileName = $probationer_Id . ".png";

            // $fileName1 = url('/storage').'/'.$probationer_Id .".png";

            Storage::disk('local')->put($fileName, base64_decode($image));
            $data   = DB::table('probationers')->where('id', $probationer_Id)->update([
                'profile_url' => $request['image']
            ]);
            $response   = [
                'code' => "200",
                'status'    => "success",
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }
    public function getimage(Request $request)
    {
        $user       = Auth::user();
        $user_id    = $user->id;

        try {
            $request = (json_decode($request->getContent(), true));

            if ($user_role == 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
                isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
            } else {
                $probationer_Id = probationer_id($user_id);
                if($probationer_Id != $request['probationer_id'])
                    {
                        $response   = [
                            'code' => "401",
                            'message'  => "Unauthorized"
                        ];
                        return response()->json($response, 401);
                    }
            }

            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            $data   = DB::table('probationers')->where('probationers.id', $probationer_Id)->join('batches', 'batches.id', '=', 'probationers.batch_id')->select('BatchName', 'RollNumber', 'Dob', 'Name', 'MobileNumber', 'profile_url', 'Cadre')->first();
            if (!empty($data)) {
                $basic_details = array(
                    'batchName' => $data->BatchName,
                    'rollNo' => $data->RollNumber,
                    'dob' => isset($data->Dob) ? date('d-m-Y', strtotime($data->Dob)) : '',
                    'name' => $data->Name,
                    'mobileNumber' => isset($data->MobileNumber) ? $data->MobileNumber : '',
                   // 'profile_url' => isset($data->profile_url) ? url('/storage') . '/' . $data->profile_url : url('/images/photo_upload.png'),
                    'profile_url' => isset($data->profile_url) ? $data->profile_url : '',
                    'cadre' => isset($data->Cadre) ? $data->Cadre : '',
                );
                $response   = [
                    'code' => "200",
                    'status'    => "success",
                    'data'      => $basic_details,

                ];
            } else {
                $response   = [
                    'code' => "200",
                    'status'    => "success",
                    'message' => 'No records Exits'

                ];
            }
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }

    public function probationercadre(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
           // isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
           $user_role = Auth::user()->role;
        if($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi')
        {
            isset($request['probationer_id']) ? $probationer_Id = remove_specialcharcters($request['probationer_id']) : $probationer_Id = '';
        }
        else
        {
            $user_id = Auth::id();
            $probationer_Id = probationer_id($user_id);
			 if($probationer_Id != $request['probationer_id'])
                {
                    $response   = [
                        'code' => "401",
                        'message'  => "Unauthorised"
                    ];
                    return response()->json($response, 401);
                }
        }
            if (!is_numeric($probationer_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            $cadre   = remove_specialcharcters($request['cadre']);
            $cadreupdate = probationer::updateOrCreate(
                [
                    'id' => $probationer_Id,
                ],
                ['Cadre' => $cadre]
            );
            $response   = [
                'code' => "200",
                'status'    => "success",
                'message' => "Cadre Updated Successfully"
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status' => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }

    public function diimage(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));
         $user_Id   = remove_specialcharcters($request['id']);

            $user_role = Auth::user()->role;
            if($user_role === 'drillinspector' || $user_role === 'si' || $user_role === 'adi')
            {
                isset($request['id']) ? $user_Id = remove_specialcharcters($request['id']) : $user_Id = '';
                $d_user_id = Auth::id();
                if($d_user_id != $user_Id)
                {
                    $response   = [
                        'code' => "401",
                        'message'  => "Unauthorised"
                    ];
                    return response()->json($response, 401);
                }

            }else
            {
                $response   = [
                    'code' => "401",
                    'message'  => "Unauthorized"
                ];
                return response()->json($response, 401);
            }

            if (!is_numeric($user_Id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Probationer Id"
                ];
                return response()->json($response, 200);
            }
            $image = $request['image'];  // your base64 encoded
           // $image = str_replace('data:image/png;base64,', '', $image);
            // $image = str_replace(' ', '+', $image);
            $fileName = "di" . $user_Id . ".png";
            // $fileName1 = url('/storage').'/di'.$user_Id .".png";

            Storage::disk('local')->put($fileName, base64_decode($image));

            $data   = DB::table('users')->where('id', $user_Id)->update([
                'profile_photo_path' => $image
            ]);
            $response   = [
                'code' => "200",
                'status'    => "success",
            ];
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
        }
        return response()->json($response, 200);
    }

    public function digetimage(Request $request)
    {
        try {
            $request = (json_decode($request->getContent(), true));

            $user_role = Auth::user()->role;
            if ($user_role == 'drillinspector' || $user_role === 'si' || $user_role === 'adi') {
             $user_id = Auth::id();
                if($user_id != $request['id'])
                {
                    $response   = [
                        'code' => "401",
                        'message'  => "Unauthorised"
                    ];
                    return response()->json($response, 401);
                }

            } else {
                $response   = [
                    'code' => "401",
                    'status'    => "success",
                    'message'  => "No Access"
                ];
                return response()->json($response, 401);
            }

            if (!is_numeric($user_id)) {
                $response   = [
                    'code' => "201",
                    'status'    => "success",
                    'message'  => "Invalid Id"
                ];
                return response()->json($response, 200);
            }
            $data   = DB::table('users')->where('id', $user_id)->select('Dob', 'name', 'MobileNumber', 'email', 'profile_photo_path')->first();
            $basic_details = array(
                'dob' => isset($data->Dob) ? date('d-m-Y', strtotime($data->Dob)) : '',
                'name' => $data->name,
                'mobileNumber' => isset($data->MobileNumber) ? $data->MobileNumber : '',
                'email' => isset($data->email) ? $data->email : '',
               // 'profile_url' => isset($data->profile_photo_path) ? url('/storage') . '/' . $data->profile_photo_path : url('/images/photo_upload.png'),
                'profile_url' => isset($data->profile_photo_path) ? $data->profile_photo_path : '',
            );
            $response   = [
                'code' => "200",
                'status'    => "success",
                'data'      => $basic_details,

            ];
        } catch (\Illuminate\Database\QueryException $e) {
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
