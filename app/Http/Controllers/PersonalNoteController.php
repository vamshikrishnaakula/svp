<?php

namespace App\Http\Controllers;

use App\Models\PersonalNote;
use Illuminate\Http\Request;

use Exception;
use Illuminate\Support\Facades\Auth;

class PersonalNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user   = Auth::user();
        return view('personal-notes.notes', compact('user'));
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
        $result = [];
        $errors  = [];

        $user   = Auth::user();

        $reference      = $request->reference;
        $reference_id   = $request->reference_id;
        $note_title     = $request->note_title;
        $note_text      = $request->note_text;

        if(empty($reference)) {
            $errors[]   = "Reference type missing.";
        }
        if(empty($reference_id)) {
            $errors[]   = "Reference id missing.";
        }
        if(empty($note_title)) {
            $errors[]   = "Note Title is empty.";
        }
        if(empty($note_text)) {
            $errors[]   = "Note Text is empty.";
        }

        if(!empty($errors)) {
            return json_encode([
                'status'    => 'error',
                'message'   => implode("<br />", $errors)
            ]);
        }

        try {
            PersonalNote::create([
                "user_id"     => $user->id,
                "reference"     => $reference,
                "reference_id"  => $reference_id,
                "title"     => $note_title,
                "text"     => $note_text,
            ]);

            return json_encode([
                'status'    => 'success',
                'message'   => "Details saved successfully."
            ]);
        } catch(Exception $e) {
            return json_encode([
                'status'    => 'error',
                'message'   => "ERROR: ". $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PersonalNote  $personalNote
     * @return \Illuminate\Http\Response
     */
    public function show(PersonalNote $personalNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PersonalNote  $personalNote
     * @return \Illuminate\Http\Response
     */
    public function edit(PersonalNote $personalNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PersonalNote  $personalNote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PersonalNote $personalNote)
    {
        // return print_r($request->all());
        $result = [];
        $errors  = [];

        $user   = Auth::user();

        $note_title = $request->note_title;
        $note_text  = $request->note_text;

        $note_id_enc    = $request->note_id_enc;
        $note_id        = data_crypt($note_id_enc, 'd');

        if(empty($note_title)) {
            $errors[]   = "Note title is empty.";
        }
        if(empty($note_text)) {
            $errors[]   = "Note text is empty.";
        }

        if(empty($note_id)) {
            $errors[]   = "Note id missing.";
        } else {
            $Note   = PersonalNote::find($note_id);
            if($Note) {
                if($Note->user_id !== $user->id) {
                    return json_encode([
                        'status'    => 'error',
                        'message'   => 'ERRORS:<br /> You are not authorized to edit this note.'
                    ]);
                }
            }
        }

        if(!empty($errors)) {
            return json_encode([
                'status'    => 'error',
                'message'   => 'ERRORS:<br />'. implode("<br />", $errors)
            ]);
        }

        try {
            $Note->title    = $note_title;
            $Note->text     = $note_text;
            $Note->save();

            return json_encode([
                'status'    => 'success',
                'message'   => 'Details saved successfully.'
            ]);
        } catch(Exception $e) {
            return json_encode([
                'status'    => 'error',
                'message'   => 'ERRORS:<br />'. $e->getMessage()
            ]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PersonalNote  $personalNote
     * @return \Illuminate\Http\Response
     */
    public function destroy(PersonalNote $personalNote, Request $request)
    {
        // return print_r($request->all());
        $result = [];
        $errors  = [];

        $user   = Auth::user();

        $note_id_enc    = $request->note_id_enc;
        $note_id        = data_crypt($note_id_enc, 'd');

        if(empty($note_id)) {
            $errors[]   = "Note id missing.";
        } else {
            $Note   = PersonalNote::find($note_id);
            if($Note) {
                if($Note->user_id !== $user->id) {
                    return json_encode([
                        'status'    => 'error',
                        'message'   => 'ERRORS:<br /> You are not authorized to delete this note.'
                    ]);
                }
            }
        }

        if(!empty($errors)) {
            return json_encode([
                'status'    => 'error',
                'message'   => 'ERRORS:<br />'. implode("<br />", $errors)
            ]);
        }

        try {
            $Note->delete();

            return json_encode([
                'status'    => 'success',
                'message'   => 'Note deleted successfully.'
            ]);
        } catch(Exception $e) {
            return json_encode([
                'status'    => 'error',
                'message'   => 'ERRORS:<br />'. $e->getMessage()
            ]);
        }
    }


    /**
     * Process ajax requests.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function ajax(Request $request)
    {
        $requestName    = $request->requestName;

        $user   = Auth::user();

        // Get notes
        if ($requestName === "get_notes") {
            $result = [];
            $errors  = [];

            $reference      = $request->reference;
            $batch_id       = $request->batch_id;
            $squad_id       = $request->squad_id;
            $probationer_id = $request->probationer_id;

            if(empty($reference)) {
                $errors[]   = "Select a radio option for squad / batch.";
            }
            if(empty($batch_id)) {
                $errors[]   = "Select a batch.";
            }
            if(empty($squad_id)) {
                $errors[]   = "Select a squad.";
            }

            if( ($reference === "probationer") && empty($probationer_id) ) {
                $errors[]   = "Select a probationer.";
            }

            if(!empty($errors)) {
                return '<p class="msg msg-danger msg-full">ERRORS:<br />'. implode("<br />", $errors) .'</p>';
            }

            $reference_id   = ($reference === "probationer") ? $probationer_id : $squad_id;
            // Get notes
            $Notes  = PersonalNote::where('user_id', $user->id)
                ->where('reference', $reference)
                ->where('reference_id', $reference_id)
                ->orderBy('updated_at', 'desc')
                ->get();

            $notes_count    = $Notes->count();

            $data   = <<<EOL
                <div class="row g-1">
                    <div class="col-6 pt-2">{$notes_count} Note(s)</div>
                    <div class="col-6 text-right">
                        <button type="button" id="createNoteBtn" data-reference="{$reference}" data-reference-id="{$reference_id}" class="btn btn-primary text-white"><i class="fas fa-plus"></i> New</button>
                    </div>
                </div>
                <hr class="mt-2" />
            EOL;

            if($notes_count>0) {

                foreach ($Notes as $Note) {
                    $note_id     = $Note->note_id;

                    $createdAt  = $Note->created_at;
                    $createdAt  = date('d F Y, H:i', strtotime($createdAt));

                    $updatedAt  = $Note->updated_at;
                    $updatedAt  = date('d F Y, H:i', strtotime($updatedAt));

                    $data .= <<<EOL

                        <div class="note-item read-note" data-note-id="{$note_id}">
                            <div class="note-title-bar">
                                <h5 class="note-title">{$Note->title}</h5>
                                <p class="note-timestamp">{$createdAt}</p>
                            </div>
                            <p class="note-metadata">
                                Last updated: {$updatedAt}
                                <span class="note-action-links"> |
                                    <span class="edit-note-link text-primary">Edit</span>
                                    <span class="delete-note-link text-danger">Delete</span>
                                </span>
                            </p>
                            <div class="note-message">{$Note->text}</div>
                        </div>
                    EOL;
                }
            } else {
                $data .= <<<EOL
                <div class="note-item">
                    <div class="msg msg-info msg-full text-left">
                        No note found
                    </div>
                </div>
                EOL;
            }

            return $data;
        }

        if($requestName === "get_createNoteForm") {
            $result = [];
            $errors  = [];

            $reference      = $request->reference;
            $reference_id   = $request->referenceId;

            if(empty($reference)) {
                $errors[]   = "Reference type missing.";
            }
            if(empty($reference_id)) {
                $errors[]   = "Reference id missing.";
            }

            if(!empty($errors)) {
                return '<p class="msg msg-danger msg-full">ERRORS:<br />'. implode("<br />", $errors) .'</p>';
            }

            $data   = <<<EOL
                <form id="createNoteForm">
                    <div class="form-group">
                        <label for="note_title">Title:</label>
                        <input type="text" name="note_title" id="note_title" class="form-control reqField">
                    </div>
                    <div class="form-group">
                        <label for="note_text">Text:</label>
                        <textarea name="note_text" id="note_text" rows="6" class="form-control reqField"></textarea>
                    </div>

                    <div class="hidden">
                        <input type="hidden" name="reference" value="{$reference}" class="hidden">
                        <input type="hidden" name="reference_id" value="{$reference_id}" class="hidden">
                    </div>

                    <div id="createNote_status" class="my-2"></div>
                    <div class="usersubmitBtns">
                        <button type="button" class="btn formBtn cancelBtn mr-4" data-dismiss="modal">Close</button>
                        <button type="button" id="createNoteSubmit" class="btn formBtn submitBtn">Save</button>
                    </div>
                </form>
            EOL;

            return $data;
        }

        /** -------------------------------------------------------------------
         * Get Edit Note Form
         * ----------------------------------------------------------------- */
        if($requestName === "get_editNoteForm") {
            $result = [];
            $errors  = [];

            $note_id      = $request->note_id;

            if(empty($note_id)) {
                $errors[]   = "Note id missing.";
            } else {
                $Note   = PersonalNote::find($note_id);
                if($Note) {
                    if($Note->user_id !== $user->id) {
                        $errors[]   = "You are not authorized to edit this note.";
                    }
                }
            }

            if(!empty($errors)) {
                return '<p class="msg msg-danger msg-full">ERRORS:<br />'. implode("<br />", $errors) .'</p>';
            }

            $note_id_enc    = data_crypt($note_id);

            $data   = <<<EOL
                <form id="editNoteForm">
                    <h4 class="text-center">Edit Note</h4>

                    <div class="form-group">
                        <label for="note_title">Title:</label>
                        <input type="text" name="note_title" id="note_title" value="{$Note->title}" class="form-control reqField">
                    </div>
                    <div class="form-group">
                        <label for="note_text">Text:</label>
                        <textarea name="note_text" id="note_text" rows="6" class="form-control reqField">{$Note->text}</textarea>
                    </div>

                    <div class="hidden">
                        <input type="hidden" name="note_id" id="edit_note_id" value="{$note_id}" class="hidden">
                        <input type="hidden" name="note_id_enc" value="{$note_id_enc}" class="hidden">
                        <input type="hidden" name="_method" value="PUT" class="hidden">
                    </div>

                    <div id="editNote_status" class="my-2"></div>
                    <div class="usersubmitBtns">
                        <button type="button" class="btn formBtn cancelBtn mr-4" data-dismiss="modal">Close</button>
                        <button type="button" id="editNoteSubmit" class="btn formBtn submitBtn">Save</button>
                    </div>
                </form>
            EOL;

            return $data;
        }

        /** -------------------------------------------------------------------
         * Get Delete Note Form
         * ----------------------------------------------------------------- */
        if($requestName === "get_deleteNoteForm") {
            $result = [];
            $errors  = [];

            $note_id      = $request->note_id;

            if(empty($note_id)) {
                $errors[]   = "Note id missing.";
            } else {
                $Note   = PersonalNote::find($note_id);
                if($Note) {
                    if($Note->user_id !== $user->id) {
                        $errors[]   = "You are not authorized to delete this note.";
                    }
                }
            }

            if(!empty($errors)) {
                return '<p class="msg msg-danger msg-full">ERRORS:<br />'. implode("<br />", $errors) .'</p>';
            }

            $note_id_enc    = data_crypt($note_id);

            $data   = <<<EOL
                <form id="deleteNoteForm">
                    <h4 class="text-center">Delete Note</h4>

                    <div class="form-group">
                        <label for="note_title">Title:</label>
                        <input type="text" value="{$Note->title}" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="note_text">Text:</label>
                        <textarea rows="6" class="form-control" readonly>{$Note->text}</textarea>
                    </div>

                    <div class="hidden">
                        <input type="hidden" name="note_id" id="delete_note_id" value="{$note_id}" class="hidden">
                        <input type="hidden" name="note_id_enc" value="{$note_id_enc}" class="hidden">
                        <input type="hidden" name="_method" value="DELETE" class="hidden">
                    </div>

                    <div id="deleteNote_status" class="my-2"></div>

                    <p class="text-center text-danger my-2">Are you sure, you want to delete this note?</p>

                    <div class="usersubmitBtns">
                        <button type="button" class="btn formBtn cancelBtn mr-4" data-dismiss="modal">No, Cancel</button>
                        <button type="button" id="deleteNoteSubmit" class="btn formBtn submitBtn">Yes, Delete</button>
                    </div>
                </form>
            EOL;

            return $data;
        }
    }
}
