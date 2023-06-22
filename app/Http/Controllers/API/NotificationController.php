<?php

namespace App\Http\Controllers\API;

use App\Models\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Exception;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userData = Auth::user();

        $Notifications  = [];

        $NF_query  = Notification::query();
        if ($userData->role === 'probationer') {
            $probationer   = \App\Models\probationer::where('user_id', $userData->id)->first();

            $squad_id = empty($probationer->squad_id) ? '0' : $probationer->squad_id;

            $NF_query->whereRaw("
                (recipient_type IS NULL OR recipient_type IN ('', '0', 'probationer'))
                AND
                (batch_id IS NULL OR batch_id IN ('', '0', $probationer->batch_id))
                AND
                (squad_id IS NULL OR squad_id IN ('', '0', $squad_id))
            ");
        } elseif ($userData->role === 'drillinspector' || $userData->role === 'si' || $userData->role === 'adi') {
            $NF_query->whereRaw("recipient_type IS NULL OR recipient_type IN ('', '0', 'drillinspector')");
        }

        $NF_data  = $NF_query->orderBy('id', 'desc')->get()->toArray();

        if (count($NF_data) > 0) {
            foreach ($NF_data as $NFdata) {
                $nf_id  = $NFdata['id'];

                $read_status   = \App\Models\NotificationReadStatus::where('notification_id', $nf_id)
                    ->where('user_id', $userData->id)->value('read_status');

                $new = ($read_status === 1) ? 0 : 1;

                $NFdata['is_new']   = $new;

                if (empty($NFdata['recipient_type'])) {
                    $NFdata['recipient_type']   = "";
                }
                if (!empty($NFdata['attachment'])) {
                    $attachment     = $NFdata['attachment'];
                    $NFdata['attachment'] = notification_attachment_url($attachment);
                } else {
                    $NFdata['attachment'] = "";
                }

                $createdAt  = date('d-M-Y, H:i', strtotime($NFdata['created_at']));
                $updatedAt  = date('d-M-Y, H:i', strtotime($NFdata['updated_at']));
                $NFdata['created_at']   = $createdAt;
                $NFdata['updated_at']   = $updatedAt;

                $Notifications[]  = $NFdata;
            }
        }


        $nfCount    = get_new_notifications_count($userData);

        return response()->json([
            'code'    => 200,
            'status'    => 'success',
            'message'   => '',
            'new_count' => $nfCount,
            'data'      => $Notifications,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
        $errors = [];

        // Validate form data

        isset($request->recipient_type) ? $recipient_type = remove_specialcharcters($request->recipient_type) : $recipient_type = '';
        isset($request->batch_id) ? $batch_id = remove_specialcharcters($request->batch_id) : $batch_id = '';
        isset($request->squad_id) ? $squad_id = remove_specialcharcters($request->squad_id) : $squad_id = '';
        // isset($request->title) ? $title = remove_specialcharcters($request->title) : $title = '';
        // isset($request->message) ? $message = remove_specialcharcters($request->message) : $message = '';
        // isset($request->attachment) ? $attachment = ($request->attachment) : $attachment = '';

        $title      = $request->title;
        $message    = $request->message;
        $attachment = $request->attachment;

        if (empty($recipient_type)) {
            $recipient_type   = 0;
        }
        if (empty($batch_id)) {
            $batch_id   = 0;
        }
        if (empty($squad_id)) {
            $squad_id   = 0;
        }

        if (empty($title)) {
            $errors[]   = "Provide a Title.";
        }
        if (empty($message)) {
            $errors[]   = "Provide a Message.";
        }

        $file_data  = "";
        $extension  = "";
        $file_name  = "";

        if (!empty($attachment)) {
            list($original_filename, $data) = explode(',', $attachment);

            $file_data = base64_decode($data);

            $extension  = pathinfo($original_filename, PATHINFO_EXTENSION);
            $file_name  = pathinfo($original_filename, PATHINFO_FILENAME);

            if (!in_array($extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $errors[]   = "Only (pdf, jpg, jpeg, png, gif, webp) files are allowed.";
            }

            if (strlen($file_data) > (1024 * 1024)) { // 1MB = 1024*1024
                $errors[]   = "File size more than 1 MB not allowed.";
            }
        }

        if (empty($errors)) {
            if (empty($recipient_type)) {
                $recipient_type = "";
            }

            $created_by = Auth::id();

            $Notification   = Notification::create([
                'recipient_type'    => $recipient_type,
                'batch_id'  => $batch_id,
                'squad_id'  => $squad_id,
                'title'     => $title,
                'message'   => $message,
                'attachment'    => "",
                'created_by'    => $created_by,
            ]);

            if (!empty($Notification)) {
                $nf_id  = $Notification->id;

                if (!empty($file_name)) {
                    $fileName   = $file_name . '-' . $nf_id . '.' . $extension;

                    $file_path  = storage_path('app/public/notification_attachments/'. $fileName);
                    file_put_contents($file_path, $file_data);

                    // Update file name in db
                    $Notification->attachment   = $fileName;
                    $Notification->save();
                }
            }

            return response()->json([
                'code'    => 200,
                'status'    => 'success',
                'message'   => 'Notification created successfully',
            ], 200);
        } else {
            return response()->json([
                'code'    => 400,
                'status'    => 'error',
                'message'   => implode('<br />', $errors),
            ], 200);
        }
    }
    catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        $response   = [
            'code' => "200",
            'status'    => "failed",
            'message' => 'Something went wrong Please try again'
        ];
        return response()->json($response, 200);
    }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
        $errors = [];

        $notification_id    = remove_specialcharcters($request->notification_id);
        $title              = remove_specialcharcters($request->title);
        $message            = remove_specialcharcters($request->message);

        isset($request->notification_id) ? $notification_id = remove_specialcharcters($request->notification_id) : $notification_id = '';
        // isset($request->title) ? $title = remove_specialcharcters($request->title) : $title = '';
        // isset($request->message) ? $message = remove_specialcharcters($request->message) : $message = '';

        $title      = $request->title;
        $message    = $request->message;

        if(empty($notification_id)) {
            $errors[]   = "Notification id missing.";
        }
        if(empty($title)) {
            $errors[]   = "Provide a Title.";
        }
        if(empty($message)) {
            $errors[]   = "Provide a Message.";
        }

        if(empty($errors)) {
            try {
                // Update notification
                Notification::find($notification_id)->update([
                    'title'     => $title,
                    'message'   => $message,
                ]);

                // clear read information
                \App\Models\NotificationReadStatus::where('notification_id', $notification_id)->delete();

                return response()->json([
                    'code'    => 200,
                    "status"    => "success",
                    "message"   => "Notification updated successfully",
                ], 200);
            } catch (Exception $e) {
                $errors[]   = $e->getMessage();
            }
        }

        return response()->json([
            'code'    => 400,
            "status"    => "error",
            "message"   => implode('<br />', $errors),
        ], 200);
    }
    catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        $response   = [
            'code' => "200",
            'status'    => "failed",
            'message' => 'Something went wrong Please try again'
        ];
        return response()->json($response, 200);
    }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try{
        if( !in_array(Auth::user()->role, ['drillinspector', 'faculty', 'admin']) ) {
            return response()->json([
                'code'    => 401,
                "status"    => "error",
                "message"   => 'Unauthorized access.',
            ], 401);
        }

        isset($request->notification_id) ? $notification_id = remove_specialcharcters($request->notification_id) : $notification_id = '';

        $Notification   = Notification::find($notification_id);
        if(!empty($Notification->attachment)) {
            $attachment = $Notification->attachment;
            Storage::delete('notification_attachments/'. $attachment);
        }
        $Notification->delete();

        return response()->json([
            'code'    => 200,
            "status"    => "success",
        ], 200);
    }
    catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        $response   = [
            'code' => "200",
            'status'    => "failed",
            'message' => 'Something went wrong Please try again'
        ];
        return response()->json($response, 200);
    }
    }

    public function sent_notifications(Request $request)
    {
        try{
        $user_id = Auth::id();

        $NF_data  = Notification::where('created_by', $user_id)->orderBy('id', 'desc')->get()->toArray();

        $Notifications  = [];
        if (count($NF_data) > 0) {
            foreach ($NF_data as $NFdata) {

                $NFdata['batch_name']   = "";
                $NFdata['squad_number']   = "";

                // Recipient type
                if (empty($NFdata['recipient_type'])) {
                    $NFdata['recipient_type']   = "All";
                }
                $NFdata['recipient_type']   = ucfirst($NFdata['recipient_type']);

                // Batch name
                if ( strtolower( $NFdata['recipient_type'] ) === "probationer") {
                    if (empty($NFdata['batch_id'])) {
                        $NFdata['batch_name']   = "All";
                        $NFdata['squad_number']   = "";
                    } else {
                        $NFdata['batch_name']   = batch_name($NFdata['batch_id']);

                        if (empty($NFdata['squad_id'])) {
                            $NFdata['squad_number']   = "All";
                        } else {
                            $NFdata['squad_number']   = squad_number($NFdata['squad_id']);
                        }
                    }
                }

                if (!empty($NFdata['attachment'])) {
                    $attachment     = $NFdata['attachment'];
                    $NFdata['attachment'] = notification_attachment_url($attachment);
                } else {
                    $NFdata['attachment'] = "";
                }

                $createdAt  = date('d-M-Y, H:i', strtotime($NFdata['created_at']));
                $updatedAt  = date('d-M-Y, H:i', strtotime($NFdata['updated_at']));
                $NFdata['created_at']   = $createdAt;
                $NFdata['updated_at']   = $updatedAt;

                $Notifications[]  = $NFdata;
            }
        }

        return response()->json([
            'code'    => 200,
            'status'    => 'success',
            'message'   => '',
            'data'      => $Notifications,
        ], 200);
    }
    catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        $response   = [
            'code' => "200",
            'status'    => "failed",
            'message' => 'Something went wrong Please try again'
        ];
        return response()->json($response, 200);
    }
    }

    public function notification_mark_read(Request $request)
    {
        try
        {
        $user_id = Auth::id();
        $requestData = (json_decode($request->getContent(), true));

        $nf_id   = "";
        if (isset($requestData['notification_id'])) {
            $nf_id   = remove_specialcharcters($requestData['notification_id']);
        }

        if (!empty($nf_id)) {
            notification_mark_read($nf_id, $user_id);

            return response()->json([
                'code'    => 200,
                'status'    => 'success',
                'message'   => 'Processed successfully.',
            ], 200);
        }

        return response()->json([
            'code'    => 400,
            'status'    => 'error',
            'message'   => 'notification_id missing',
        ], 200);
    }
        catch(\Illuminate\Database\QueryException $e){
            $errorCode = $e->errorInfo[1];
            $response   = [
                'code' => "200",
                'status'    => "failed",
                'message' => 'Something went wrong Please try again'
            ];
            return response()->json($response, 200);
        }
    }
}
