<?php


namespace App\Helpers;
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiAuth extends Controller
{

    public function login(Request $request)
    {
        $VERSION_NAME = "1.6";
        if(isset($request->version_name) ? $version_number   = $request->version_name : $version_number ='');
        $password   = $request->password;
        if($VERSION_NAME != $version_number)
        {
            $version_code = '101';
            $version_message = 'You are not using latest version, to continue please upgrade to latest version.';
        }
        else{
            $version_code = '100';
            $version_message = '';
        }

        $header = $request->header('Authentication');

        // if (authentication($header) == false) {
        //     return response()->json([
        //         'status'    => 'error',
        //         'message'   => 'Invalid Authorization'
        //     ], 404);
        // }

            $ldap_dn = "dc=svpnpa,dc=gov,dc=in";
            $uid = str_replace("@svpnpa.gov.in","",$request->email);
            $email1 = $uid."@svpnpa.gov.in";
            $ldap_password = $request->password;

            $ldap_con = ldap_connect(env('LDAP_HOST'));
            ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3);
             if(@ldap_bind($ldap_con, $uid."@svpnpa.gov.in", $ldap_password)) {
                $details = User::where('email', $email1)->first();
                if(!empty($details))
                {
                    Auth::login($details);
                    $login_verification = true;
                }
                else
                {
                    $login_verification = false;
                }


            } else {
                $login_verification = Auth::attempt(['email' => $request->email, 'password' => $password]);
        }



        $email = $request->email;

        // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //     return response()->json([
        //         'status'    => 'error',
        //         'message'   => 'Invalid Email Address'
        //     ], 401);
        // }

        if($login_verification == true) {
            $user   = Auth::user();
            $date  = date('Y-m-d');
            if($user->role == 'probationer')
            {
                $check_prob_status   = DB::table('probationers')->where('user_id', $user->id)->first();
                $timetable = DB::table('probationers')->where('user_id', $user->id)->whereDate('timetables.date', $date)->join('timetables', 'probationers.squad_id', '=', 'timetables.squad_id')->join('activities', 'timetables.activity_id', '=', 'activities.id')->select('timetables.*', 'activities.name', 'activities.id','probationers.id')->get();
                $prob_other_details   = DB::table('probationers')->where('user_id', $user->id)->leftJoin('squads', 'squads.id', '=', 'probationers.squad_id')->leftJoin('batches', 'batches.id', '=', 'probationers.batch_id')->select('probationers.id', 'probationers.Name', 'probationers.RollNumber', 'probationers.Cadre','squads.id as squadid', 'squads.SquadNumber', 'batches.id as b_id', 'batches.BatchName as b_name')->first();

               if(count($timetable) != '0')
               {
                foreach($timetable as $time)
                {

                    $timetables[] = array(
                        'key'=> "Session ".$time->session_number,
                        'value' => $time->name,
                    );
                }
               }
                else
                {
                    $timetables = array();
                }
                $Cadre  = $prob_other_details->Cadre;
                $Cadre  = empty($Cadre)? "" : $Cadre;


                // Delete previous access tokens
                DB::table('oauth_access_tokens')
                    ->where('user_id', $user->id)
                    ->where('name', 'NpaApp')
                    ->delete();
                // Create fresh access token
                $accessToken    = $user->createToken('NpaApp')->accessToken;

                if($check_prob_status->Religion != '' || $check_prob_status->Religion != null)
                {

                    $responseArray  = [
                        'token' => $accessToken,
                        'password_change' => Auth::user()->force_password_change,
                        'Editable' => 'No',
                        'id'    => $user->id,
                        'probationer_id' => $prob_other_details->id,
                        'probationer_name' => $prob_other_details->Name,
                        'roll_number' => $prob_other_details->RollNumber,
                        'cadre' => $Cadre,
                        'squad_id' => $prob_other_details->squadid,
                        'squad_number' => $prob_other_details->SquadNumber,
                        'batch_id' => $prob_other_details->b_id,
                        'batch_name' => $prob_other_details->b_name,
                        'role'  => $user->role,
                        'version_code' => $version_code,
                        'version_message' => $version_message,
                        'schedule' => $timetables,
                    ];

                     return response()->json($responseArray, 200);
                }
                else
                {
                    $responseArray  = [
                        'token' => $accessToken,
                        'password_change' => Auth::user()->force_password_change,
                        'Editable' => 'Yes',
                        'id'    => $user->id,
                        'probationer_id' => $prob_other_details->id,
                        'probationer_name' => $prob_other_details->Name,
                        'roll_number' => $prob_other_details->RollNumber,
                        'cadre' => $Cadre,
                        'squad_id' => $prob_other_details->squadid,
                        'squad_number' => $prob_other_details->SquadNumber,
                        'batch_id' => $prob_other_details->b_id,
                        'batch_name' => $prob_other_details->b_name,
                        'role'  => $user->role,
                        'version_code' => $version_code,
                        'version_message' => $version_message,
                        'schedule' => $timetables,
                        'userdetails' => $user,
                        'profile_url' => $check_prob_status->profile_url,
                    ];
                return response()->json($responseArray, 200);
                }

            }
            elseif($user->role == 'drillinspector' || $user->role == 'si' || $user->role == 'adi'){
                $DI_id  = $user->id;

                $squad_ids   = DB::table('squad_activity_trainer')
                    ->select('squad_id')
                    ->where('staff_id', $DI_id)
                    ->groupBy('squad_id')->pluck('squad_id');

                if(count($squad_ids) > 0) {
                    $Batch_Ids   = DB::table('squads')
                        ->where('DrillInspector_Id', $DI_id)
                        ->orWhereIn('id', $squad_ids)
                        ->groupBy('Batch_Id')->pluck('Batch_Id');
                } else {
                    $Batch_Ids   = DB::table('squads')
                        ->where('DrillInspector_Id', $DI_id)
                        ->groupBy('Batch_Id')->pluck('Batch_Id');
                }

                $eBatch_Ids   = DB::table('extra_classes')
                        ->where('drillinspector_id', $DI_id)
                        ->groupBy('batch_id')->pluck('batch_id');

                        $mBatch_Ids   = DB::table('extra_sessions')
                        ->where('drillinspector_id', $DI_id)
                        ->groupBy('batch_id')->pluck('batch_id');

                $eBatches = $Batch_Ids->merge($eBatch_Ids);
                $tBatches_ids = $eBatches->merge($mBatch_Ids);



                $batches   = DB::table('batches')
                    ->whereIn('id', $tBatches_ids)
                    ->orderBy('id', 'DESC')
                    ->get();

                $responseArray  = [
                    'token' => $user->createToken('NpaApp')->accessToken,
                    'password_change' => Auth::user()->force_password_change,
                    'id'    => $DI_id,
                    'name'  => $user->name,
                    'role'  => $user->role,
                    'version_code' => $version_code,
                    'version_message' => $version_message,
                    'Batches' => $batches,
                ];

            return response()->json($responseArray, 200);
            }
            else
            {
                $responseArray = [
                    "unauthorised" => 401,
                    "message" => 'Unauthorised Role',
                    'token' => '',
                  //  'id'    => 0,
                    'name'  => '',
                    'role'  => $user->role,
                    'version_code' => $version_code,
                    'version_message' => $version_message,
                    'Batches' => array(),
                ];
                return response()->json($responseArray, 200);
            }


        } else {
            $responseArray = [
                "unauthorised" => 401,
                "message" => 'Invalid credentials',
                'token' => '',
                //'id'    => 0,
                'name'  => '',
                'role'  => '',
                'version_code' => $version_code,
                'version_message' => $version_message,
                'Batches' => array(),
            ];
            return response()->json($responseArray, 200);
        }
    }

    public function password_change(Request $request)
    {
        $request = (json_decode($request->getContent(), true));

            isset($request['confirm_new_password']) ? $cPassword = $request['confirm_new_password'] : $cPassword = '';
            isset($request['current_password']) ? $oPassword = ($request['current_password']) : $oPassword = '';
            isset($request['new_password']) ? $nPassword = ($request['new_password']) : $nPassword = '';
            $id = Auth::user()->id;

            if($nPassword != $cPassword)
            {
                $response   = [
                    'code'  => 201,
                    'status'    => "error",
                    'message' => 'Password and Confirm password should be Same',
                ];
            }

            $hashedPassword = Auth::user()->getAuthPassword();


            if (Hash::check($oPassword, $hashedPassword)) {
                User::where('id', $id)->update([
                    'password' => Hash::make(trim($nPassword)),
                    'force_password_change' => '0',
                ]);
                $response   = [
                    'code'  => 200,
                    'status'    => "success",
                    'message' => 'Password Changed Succesfully',
                ];
            }
            else
            {
                $response   = [
                    'code'  => 201,
                    'status'    => "error",
                    'message' => "Current password doesn't matched.",
                ];
            }
            return response()->json($response, 200);
    }

}
