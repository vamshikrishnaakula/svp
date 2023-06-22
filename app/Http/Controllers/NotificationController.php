<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $Notifications = Notification::all();
        // return view('notifications.notifications',compact('Notifications'));

        $role   = strtolower(Auth::user()->role);

        if(in_array($role, ["admin", "superadmin"], true)) {
            return view('notifications.notifications');
        } else if($role === "faculty") {
                return view('notifications.faculty-notifications');
        } else if($role === "doctor") {
            return view('notifications.doctor-notifications');
        } else if($role === "probationer") {
            return view('notifications.user-notifications');
        }

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role   = Auth::user()->role;
        if( !in_array($role, ['superadmin', 'admin', 'faculty', 'doctor']) ) {
            return json_encode([
                "status"    => "error",
                "message"   => "Unauthorized access.",
            ]);
        }

        $errors = [];

        $recipient_type = $request->recipient_type;
        $batch_id       = $request->batch_id;
        $squad_id       = $request->squad_id;
        $title          = $request->title;
        $message        = $request->message;

        if(empty($title)) {
            $errors[]   = "Provide a Title.";
        }
        if(empty($message)) {
            $errors[]   = "Provide a Message.";
        }

        $original_filename  = "";
        $extension          = "";
        $file_name          = "";
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $original_filename  = $request->attachment->getClientOriginalName();
            $extension  = pathinfo($original_filename, PATHINFO_EXTENSION);
            $file_name  = pathinfo($original_filename, PATHINFO_FILENAME);

            if ( !in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']) ) {
                $errors[]   = "Only (pdf, jpg, jpeg, png, gif, webp) files are allowed.";
            }

            if ( $request->file('attachment')->getSize() > (1024*1024) ) { // 1MB = 1024*1024
                $errors[]   = "File size more than 1 MB not allowed.";
            }
        }

        if(empty($errors)) {
            if(empty($recipient_type)) {
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

            if(!empty($Notification)) {
                $nf_id  = $Notification->id;

                if(!empty($original_filename)) {
                    $fileName   = $file_name .'-'. $nf_id .'.'. $extension;

                    // Store the file
                    $request->attachment->storeAs('notification_attachments', $fileName);

                    // Update file name in db
                    $Notification->attachment   = $fileName;
                    $Notification->save();
                }
            }

            return json_encode([
                "status"    => "success",
                "message"   => "Notification submitted successfully",
            ]);
        } else {
            return json_encode([
                "status"    => "error",
                "message"   => implode('<br />', $errors),
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        $loginuser = Auth::User()->role;
        if($loginuser === "admin")
        {
            return view('notifications.notifications');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function edit(Notification $notification)
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
        $errors = [];

        $notification_id    = $request->notification_id;
        $title              = $request->title;
        $message            = $request->message;

        if(empty($notification_id)) {
            $errors[]   = "Notification id missing.";
        }
        if(empty($title)) {
            $errors[]   = "Provide a Title.";
        }
        if(empty($message)) {
            $errors[]   = "Provide a Message.";
        }

        $original_filename  = "";
        $extension          = "";
        $file_name          = "";
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $original_filename  = $request->attachment->getClientOriginalName();
            $extension  = pathinfo($original_filename, PATHINFO_EXTENSION);
            $file_name  = pathinfo($original_filename, PATHINFO_FILENAME);

            if ( !in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp']) ) {
                $errors[]   = "Only (pdf, jpg, jpeg, png, gif, webp) files are allowed.";
            }

            if ( $request->file('attachment')->getSize() > (1024*1024) ) { // 1MB = 1024*1024
                $errors[]   = "File size more than 1 MB not allowed.";
            }
        }

        if(empty($errors)) {
            try {
                // Update notification
                $Notification   = Notification::find($notification_id);
                $Notification->update([
                    'title'     => $title,
                    'message'   => $message,
                ]);

                // Update attachment
                if(!empty($original_filename)) {
                    $old_attachment = $Notification->attachment;

                    $fileName   = $file_name .'-'. $notification_id .'.'. $extension;

                    // Store the file
                    $request->attachment->storeAs('notification_attachments', $fileName);

                    // Update file name in db
                    $Notification->attachment   = $fileName;
                    $Notification->save();

                    // Delete old file
                    if($fileName !== $old_attachment) {
                        Storage::delete('notification_attachments/'. $old_attachment);
                    }
                }

                // clear read information
                \App\Models\NotificationReadStatus::where('notification_id', $notification_id)->delete();

                return json_encode([
                    "status"    => "success",
                    "message"   => "Notification updated successfully",
                ]);
            } catch (Exception $e) {
                $errors[]   = $e->getMessage();
            }
        }

        return json_encode([
            "status"    => "error",
            "message"   => implode('<br />', $errors),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notification $notification)
    {
        //
    }

    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax(Request $request)
    {
        $requestName    = $request->requestName;

        // Get Squads on select batch for recipient
        if ($requestName === "get_squads") {
            $batchId    = $request->batch_id;

            $squadOptions   = "";
            if( !empty($batchId) ) {
                $squads = \App\Models\Squad::where('Batch_Id', $batchId)
                    ->orderBy('SquadNumber', 'asc')->get();

                if( count($squads) > 0 ) {
                    $squadOptions   .= "<option value=\"0\">All</option>";

                    foreach($squads as $squad) {
                        $squad_id   = $squad->id;
                        $squad_no   = $squad->SquadNumber;

                        $squadOptions   .= "<option value=\"{$squad_id}\">Squad {$squad_no}</option>";
                    }
                } else {
                    $squadOptions   .= "<option value=\"0\">-- No Squad --</option>";
                }
            } else {
                $squadOptions   .= "<option value=\"0\">-- Batch Id Missing --</option>";
            }

            return $squadOptions;
        }

        if ($requestName === "get_new_notifications_count") {
            $userData   = auth()->user();

            $nfCount    = get_new_notifications_count($userData);
            if($nfCount > 0) {
                $nfCount    = "<span>{$nfCount}</span>";
            } else {
                $nfCount    = "";
            }

            return json_encode([
                'status'    => 'success',
                'count'     => $nfCount,
            ]);

        }

        if ($requestName === "notifications_mark_read") {
            $nf_id  = $request->notification_id;
            if(!empty($nf_id)) {
                $user_id = auth()->user()->id;

                notification_mark_read($nf_id, $user_id);

                return json_encode([
                    'status'    => 'success',
                    'message'   => "",
                ]);
            } else {
                return json_encode([
                    'status'    => 'error',
                    'message'   => "notification id missing",
                ]);
            }
        }

        /** -------------------------------------------
         * Get notification edit form
         */
        if ($requestName === "get_notification_edit") {
            $notification_id  = $request->notification_id;

            echo '<h4 class="text-center mb-5">Edit Notifications</h4>';
            if(!empty($notification_id)) {
                $notification   = Notification::find($notification_id);
                return view('notifications.notification-edit-form', compact('notification'));
            } else {
                return json_encode([
                    'status'    => 'error',
                    'message'   => "notification id missing",
                ]);
            }
        }

        /** -------------------------------------------
         * Get notification delete form
         */
        if ($requestName === "get_notification_delete") {
            $notification_id  = $request->notification_id;

            echo '<h4 class="text-center mb-5">Delete Notifications</h4>';
            if(!empty($notification_id)) {
                $notification   = Notification::find($notification_id);
                return view('notifications.notification-delete-form', compact('notification'));
            } else {
                return json_encode([
                    'status'    => 'error',
                    'message'   => "notification id missing",
                ]);
            }
        }

        /** -------------------------------------------
         * Notification delete submit
         */
        if ($requestName === "notification_delete") {
            $notification_id  = $request->notification_id;

            if( !in_array(Auth::user()->role, ['drillinspector', 'faculty', 'admin']) ) {
                return json_encode([
                    "status"    => "error",
                    "message"   => 'Unauthorized access.',
                ]);
            }

            $Notification   = Notification::find($notification_id);
            if(!empty($Notification->attachment)) {
                $attachment = $Notification->attachment;
                Storage::delete('notification_attachments/'. $attachment);
            }
            $Notification->delete();


            return json_encode([
                'status'    => 'success',
                'message'   => "Notification deleted.",
            ]);
        }
    }
}
