<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PersonalNote;

use Exception;
use Illuminate\Support\Facades\Auth;

class PersonalNoteAPI extends Controller
{
    /** -----------------------------------------------------------------------
     * API Name: personal-notes
     * User Role: probationer, drillinspector
     * Description: To get personal notes
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function index(Request $request)
    {
        $errors = [];
        $user       = Auth::user();
        $user_id    = $user->id;

        $req = json_decode($request->getContent());

        $reference      = $req->reference ? remove_specialcharcters($req->reference) : "";
        $reference_id   = $req->reference_id ? remove_specialcharcters($req->reference_id) : 0;

        if($user->role === 'drillinspector' || $user->role === 'si' || $user->role === 'adi') {
            if(empty($reference) || !in_array($reference, ['probationer', 'squad'])) {
                $errors[]   = "Invalid reference.";
            }
            if( empty($reference_id) || !is_numeric($reference_id) ) {
                $errors[]   = "Invalid reference_id.";
            }
        } elseif($user->role === 'probationer') {
            $reference      = "probationer";
            $reference_id   = $user_id;
        } else {
            return response()->json([
                'code'  => "401",
                'status'    => "error",
                'message'   => "Unauthorized access."
            ], 200);
        }

        if( !empty($errors) ) {
            return response()->json([
                'code'  => "400",
                'status'    => "error",
                'message'   => implode(" ", $errors)
            ], 200);
        }

        // Get folders
        $getNotes    = PersonalNote::where('reference', $reference)
            ->where('reference_id', $reference_id)
            ->where('user_id', $user_id)
            ->orderBy('updated_at', 'desc')
            ->get()->toArray();


        $Notes  = [];
        foreach($getNotes as $note) {
            $note['created_at']   = date('d-M-Y H:i', strtotime($note['created_at']));
            $note['updated_at']   = date('d-M-Y H:i', strtotime($note['updated_at']));

            $Notes[]    = $note;
        }

        return response()->json([
            'code'  => "200",
            'status'    => "success",
            'data'   => $Notes
        ], 200);
    }

    /** -----------------------------------------------------------------------
     * API Name: personal-notes/create
     * User Role: probationer, drillinspector
     * Description: To create personal note
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function create(Request $request)
    {
        $errors = [];
        $user       = Auth::user();
        $user_id    = $user->id;

        $req = json_decode($request->getContent());

        $reference      = $req->reference ? remove_specialcharcters($req->reference) : "";
        $reference_id   = $req->reference_id ? remove_specialcharcters($req->reference_id) : 0;
        $title      = $req->title;
        $text       = $req->text;

        $title    = filter_var($title, FILTER_SANITIZE_STRING);

        if($user->role === 'drillinspector') {
            if(empty($reference) || !in_array($reference, ['probationer', 'squad'])) {
                $errors[]   = "Invalid reference.";
            }
            if( empty($reference_id) || !is_numeric($reference_id) ) {
                $errors[]   = "Invalid reference_id.";
            }
        } elseif($user->role === 'probationer') {
            $reference      = "probationer";
            $reference_id   = $user->id;
        } else {
            return response()->json([
                'code'  => "401",
                'status'    => "error",
                'message'   => "Unauthorized access."
            ], 200);
        }

        if( empty($title) ) {
            $errors[]   = "Title is required.";
        }
        if( empty($text) ) {
            $errors[]   = "Text is required.";
        }

        if( !empty($errors) ) {
            return response()->json([
                'code'  => "400",
                'status'    => "error",
                'message'   => implode(" ", $errors)
            ], 200);
        }

        // Create folder
        try {
            $Note    = PersonalNote::create([
                "user_id"  => $user_id,
                "reference"     => $reference,
                "reference_id"  => $reference_id,
                "title"     => $title,
                "text"      => $text,
            ]);

            return response()->json([
                'code'  => "200",
                'status'    => "success",
                'data'   => $Note,
                'message'   => "Note created successfully."
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'code'  => "400",
                'status'    => "error",
                'message'   => "Something going wrong, please try again after sometime.". $e->getMessage()
            ], 200);
        }
    }

    /** -----------------------------------------------------------------------
     * API Name: personal-notes/{note_id}/update
     * User Role: probationer, drillinspector
     * Description: To update personal note with note_id
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function update(Request $request)
    {
        $errors = [];
        $user       = Auth::user();
        $user_id    = $user->id;

        $req = json_decode($request->getContent());

        $note_id    = intval($req->note_id);
        $title      = $req->title;
        $text       = $req->text;

        $title    = filter_var($title, FILTER_SANITIZE_STRING);

        $Note   = PersonalNote::find($note_id);
        if($Note) {
            if($Note->user_id === $user_id) {
                $Note->title    = $title;
                $Note->text     = $text;
                $Note->save();

                return response()->json([
                    'code'  => "200",
                    'status'    => "success",
                    'data'    => $Note,
                    'message'   => "Note updated successfully."
                ], 200);
            } else {
                return response()->json([
                    'code'  => "401",
                    'status'    => "error",
                    'message'   => "Unauthorized access."
                ], 401);
            }
        } else {
            return response()->json([
                'code'  => "404",
                'status'    => "error",
                'message'   => "Something going wrong, unable to retrive note data."
            ], 200);
        }
    }

    /** -----------------------------------------------------------------------
     * API Name: personal-notes/{note_id}/delete
     * User Role: probationer, drillinspector
     * Description: To delete personal note with note_id
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function destroy(Request $request)
    {
        $errors = [];
        $user       = Auth::user();
        $user_id    = $user->id;

        $req = json_decode($request->getContent());
        $note_id    = $req->note_id;

        $Note   = PersonalNote::find($note_id);
        if($Note) {
            if($Note->user_id === $user_id) {
                $Note->delete();

                return response()->json([
                    'code'  => "200",
                    'status'    => "success",
                    'message'   => "Note deleted successfully."
                ], 200);
            } else {
                return response()->json([
                    'code'  => "401",
                    'status'    => "error",
                    'message'   => "Unauthorized access."
                ], 200);
            }
        } else {
            return response()->json([
                'code'  => "404",
                'status'    => "error",
                'message'   => "Something going wrong, unable to retrive note data."
            ], 200);
        }
    }
}
