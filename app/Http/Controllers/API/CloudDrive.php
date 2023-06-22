<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CloudDriveFile;
use App\Models\CloudDriveFolder;

use Exception;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CloudDrive extends Controller
{
    /** -----------------------------------------------------------------------
     * API Name: cloud-drive/get-assets
     * User Role: probationer, drillinspector
     * Description: To get files and folders [using parent folder id (optional)]
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function get_assets(Request $request)
    {
        $errors = [];
        $user   = Auth::user();

        $req = json_decode($request->getContent());

        $reference      = $req->reference ? remove_specialcharcters($req->reference) : "";
        $reference_id   = $req->reference_id ? remove_specialcharcters($req->reference_id) : 0;
        $parent_id      = $req->parent_id ? remove_specialcharcters($req->parent_id) : 0;

        if($user->role === 'drillinspector' || $user->role === 'si' || $user->role === 'adi') {
            if(empty($reference) || !in_array($reference, ['probationer', 'squad'])) {
                $errors[]   = "Invalid reference.";
            }
            if( empty($reference_id) || !is_numeric($reference_id) ) {
                $errors[]   = "Invalid reference_id.";
            }
        } elseif($user->role === 'probationer') {
            $reference      = "probationer";
            $reference_id   = probationer_id($user->id);
        } else {
            return response()->json([
                'code'  => "401",
                'status'    => "error",
                'message'   => "Unauthorized access."
            ], 200);
        }

        $is_main_folder    = false;

        if( empty($errors) ) {
            if( empty($parent_id) ) {
                $is_main_folder    = true;

                $mainFolder   = CloudDriveFolder::where('reference', $reference)
                    ->where('reference_id', $reference_id)
                    ->where('name', 'Main Folder')
                    ->whereNull('parent_id')
                    ->first();

                if($mainFolder) {
                    $parent_id  = $mainFolder->folder_id;
                } else {
                    $mainFolder1    = CloudDriveFolder::create([
                        "name"      => 'Main Folder',
                        "parent_id"     => NULL,
                        "reference"     => $reference,
                        "reference_id"  => $reference_id,
                        "created_by"  => $user->id,
                    ]);

                    $parent_id  = $mainFolder1->folder_id;
                }
            } elseif( !is_numeric($parent_id) ) {
                $errors[]   = "Invalid parent_id.";
            }
        }

        if( !empty($errors) ) {
            return response()->json([
                'code'  => "400",
                'status'    => "error",
                'message'   => implode(" ", $errors)
            ], 200);
        }

        // current folder id
        $current_folder_id  = $parent_id;

        // Get parent folder id
        $parent_folder_id   = CloudDriveFolder::where('folder_id', $parent_id)->value('parent_id');
        $parent_folder_id   = empty($parent_folder_id) ? 0 : $parent_folder_id;

        // Get folders
        $folders    = CloudDriveFolder::where('reference', $reference)
            ->where('reference_id', $reference_id)
            ->where('parent_id', $parent_id)
            ->orderBy('updated_at', 'desc')
            ->get()->toArray();



        // $assets  = [];

        $foldersArray  = [];
        foreach($folders as $folder) {
            if(empty($folder['parent_id'])) {
                $folder['parent_id']   = 0;
            }

            // Count Items
            $folderCount    = CloudDriveFolder::where('parent_id', $folder["folder_id"])->count();
            $fileCount      = CloudDriveFile::where('folder_id', $folder["folder_id"])->count();
            $itemCount  = $folderCount + $fileCount;
            $folder['items']   = $itemCount;

            if(empty($folder['updated_by'])) {
                $folder['updated_by']   = "";
            }

            $folder['created_at']   = date('d-M-Y H:i', strtotime($folder['created_at']));
            $folder['updated_at']   = date('d-M-Y H:i', strtotime($folder['updated_at']));

            $folder['type']   = 'folder';
            $foldersArray[]    = $folder;
        }

        // Get files
        $filesArray  = [];
        if(!empty($parent_id)) {
            $files    = CloudDriveFile::where('folder_id', $parent_id)
                ->orderBy('updated_at', 'desc')
                ->get()->toArray();

            foreach($files as $file) {
                if(empty($file['updated_by'])) {
                    $file['updated_by']   = "";
                }
                $file['created_at']   = date('d-M-Y H:i', strtotime($file['created_at']));
                $file['updated_at']   = date('d-M-Y H:i', strtotime($file['updated_at']));

                $fileUrl    = Storage::disk('s3')->temporaryUrl(
                    $file['file_path'], now()->addMinutes(60)
                );
                $file['file_url']   = $fileUrl;
                $file['type']   = 'file';
                $filesArray[]    = $file;
            }
        }

        return response()->json([
            'code'  => "200",
            'status'    => "success",
            'data'   => [
                'folders'   => $foldersArray,
                'files'   => $filesArray,
                'current_folder_id'  => $current_folder_id,
                'parent_folder_id'   => $parent_folder_id,
                'is_main_folder'   => $is_main_folder,
            ]
        ], 200);
    }

    /** -----------------------------------------------------------------------
     * API Name: cloud-drive/create-folder
     * User Role: probationer, drillinspector
     * Description: To get create folder
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function create_folder(Request $request)
    {
        $errors = [];
        $user   = Auth::user();

        $req = json_decode($request->getContent());

        $reference      = $req->reference ? remove_specialcharcters($req->reference) : "";
        $reference_id   = $req->reference_id ? remove_specialcharcters($req->reference_id) : 0;
        $parent_id      = $req->parent_id ? remove_specialcharcters($req->parent_id) : 0;
        $folder_name    = $req->folder_name ? $req->folder_name : "";

        $folder_name    = filter_var($folder_name, FILTER_SANITIZE_STRING);

        if($user->role === 'drillinspector' ||  $user->role === 'si' || $user->role === 'adi') {
            if(empty($reference) || !in_array($reference, ['probationer', 'squad'])) {
                $errors[]   = "Invalid reference.";
            }
            if( empty($reference_id) || !is_numeric($reference_id) ) {
                $errors[]   = "Invalid reference_id.";
            }
        } elseif($user->role === 'probationer') {
            $reference      = "probationer";
            $reference_id   = probationer_id($user->id);
        } else {
            return response()->json([
                'code'  => "401",
                'status'    => "error",
                'message'   => "Unauthorized access."
            ], 200);
        }

        if( empty($parent_id) || !is_numeric($parent_id) ) {
            $errors[]   = "Invalid parent_id.";
        }
        if( empty($folder_name) ) {
            $errors[]   = "Folder name is required.";
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
            $folder    = CloudDriveFolder::create([
                "name"      => $folder_name,
                "parent_id"     => $parent_id,
                "reference"     => $reference,
                "reference_id"  => $reference_id,
                "created_by"  => $user->id,
            ]);

            return response()->json([
                'code'  => "200",
                'status'    => "success",
                'data'   => $folder
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
     * API Name: cloud-drive/upload-file
     * User Role: probationer, drillinspector
     * Description: To get upload file
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function upload_file(Request $request)
    {
        $errors = [];
        $user   = Auth::user();

        $reference      = $request->reference ? remove_specialcharcters($request->reference) : "";
        $reference_id   = $request->reference_id ? remove_specialcharcters($request->reference_id) : 0;
        $folder_id      = $request->folder_id ? remove_specialcharcters($request->folder_id) : 0;

        $attachment     = $request->filedata;

        if($user->role === 'drillinspector' ||  $user->role === 'si' || $user->role === 'adi') {
            if(empty($reference) || !in_array($reference, ['probationer', 'squad'])) {
                $errors[]   = "Invalid reference.";
            }
            if( empty($reference_id) || !is_numeric($reference_id) ) {
                $errors[]   = "Invalid reference_id.";
            }
        } elseif($user->role === 'probationer') {
            $reference      = "probationer";
            $reference_id   = probationer_id($user->id);
        } else {
            return response()->json([
                'code'  => "401",
                'status'    => "error",
                'message'   => "Unauthorized access."
            ], 401);
        }

        if( empty($folder_id) || !is_numeric($folder_id) ) {
            $errors[]   = "Invalid folder_id.";
        }

        $file_data  = "";
        $extension  = "";
        $file_name  = "";

        if (!empty($attachment)) {
            list($original_filename, $data) = explode(',', $attachment);

            $file_data = base64_decode($data);

            $extension  = pathinfo($original_filename, PATHINFO_EXTENSION);
            $file_name  = pathinfo($original_filename, PATHINFO_FILENAME);

            $allowedExtns   = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'txt', 'doc', 'docx', 'csv', 'xlsx', 'mp4', 'mp3'];

            if (!in_array($extension, $allowedExtns)) {
                $errors[]   = "Only (". implode(', ', $allowedExtns) .") files are allowed.";
            }

            if (strlen($file_data) > (50 * 1024 * 1024)) { // 50MB = 50*1024*1024
                $errors[]   = "File size more than 50 MB not allowed.";
            }
        } else {
            $errors[]   = "No file attached.";
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
                $filePath   = 'cloud-drive/'. Str::random(30) . '.' . $extension;

                // $file_path  = storage_path('app/public/notification_attachments/'. $fileName);
                // file_put_contents($file_path, $file_data);

                Storage::disk('s3')->put($filePath, $file_data);


            $file    = CloudDriveFile::create([
                "folder_id"      => $folder_id,
                "original_name"     => $original_filename,
                "disk"     => 's3',
                "file_path"  => $filePath,
                "file_extn"  => $extension,
                "created_by"  => $user->id,
            ]);

            return response()->json([
                'code'  => "200",
                'status'    => "success",
                'data'   => $file
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
     * API Name: cloud-drive/edit-file
     * User Role: probationer, drillinspector
     * Description: To edit file name
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function edit_file(Request $request)
    {
        $errors = [];
        $user   = Auth::user();

        $file_id      = $request->file_id ? remove_specialcharcters($request->file_id) : 0;
        $filename    = $request->file_name ? $request->file_name : "";

        if (empty($filename)) {
            $errors[]   = "File name is empty.";
        }
        if (strlen($filename) > 255) { // no mb_* since we check bytes
            $errors[]   = "File name should be less than 255 characters long.";
        }
        $invalidCharacters = '|\'\\?*&<";:>+[]=/';
        if (false !== strpbrk($filename, $invalidCharacters)) {
            $errors[]   = "File name contains invalid character.";
        }

        if(!empty($errors)) {
            return response()->json([
                'code'  => "400",
                'status'    => "error",
                'message'   => "ERROR: ". implode(' ', $errors)
            ], 200);
        }

        $File   = CloudDriveFile::find($file_id);
        if($File) {
            if($File->created_by !== $user->id) {
                return response()->json([
                    'code'  => "401",
                    'status'    => "error",
                    'message'   => "You don't have permission to delete this file.",
                ], 200);
            }

            try {
                $folder_id  = $File->folder_id;

                $File->original_name    = $filename;
                $File->save();

                return response()->json([
                    'code'  => "200",
                    'status'    => "success",
                    'message'   => 'File name updated successfully.',
                    'parent_folder_id'   => $folder_id,
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'code'  => "400",
                    'status'    => "error",
                    'message'   => "Something going wrong, please try again after sometime.". $e->getMessage()
                ], 200);
            }
        }

        return response()->json([
            'code'  => "404",
            'status'    => "error",
            'message'   => "File not exist."
        ], 200);
    }


    /** -----------------------------------------------------------------------
     * API Name: cloud-drive/edit-folder
     * User Role: probationer, drillinspector
     * Description: To edit folder name
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function edit_folder(Request $request)
    {
        $errors = [];
        $user   = Auth::user();

        $folder_id    = $request->folder_id ? remove_specialcharcters($request->folder_id) : 0;
        $folder_name  = $request->folder_name ? $request->folder_name : "";

        if (empty($folder_name)) {
            $errors[]   = "Folder name is empty.";
        } else {
            $folder_name    = filter_var($folder_name, FILTER_SANITIZE_STRING);
        }
        if (strlen($folder_name) > 255) { // no mb_* since we check bytes
            $errors[]   = "Folder name should be less than 255 characters long.";
        }

        if(!empty($errors)) {
            return response()->json([
                'code'  => "400",
                'status'    => "error",
                'message'   => "ERROR: ". implode(' ', $errors)
            ], 200);
        }

        $Folder   = CloudDriveFolder::find($folder_id);
        if($Folder) {
            if($Folder->created_by !== $user->id) {
                return response()->json([
                    'code'  => "401",
                    'status'    => "error",
                    'message'   => "You don't have permission to delete this file.",
                ], 200);
            }

            try {
                $Folder->name    = $folder_name;
                $Folder->save();

                return response()->json([
                    'code'  => "200",
                    'status'    => "success",
                    'message'   => 'Folder name updated successfully.',
                    'parent_folder_id'   => $Folder->parent_id,
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'code'  => "400",
                    'status'    => "error",
                    'message'   => "Something going wrong, please try again after sometime.". $e->getMessage()
                ], 200);
            }
        }

        return response()->json([
            'code'  => "404",
            'status'    => "error",
            'message'   => "Folder not exist."
        ], 200);
    }

    /** -----------------------------------------------------------------------
     * API Name: cloud-drive/delete-file
     * User Role: probationer, drillinspector
     * Description: To delete file
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function destroy_file(Request $request)
    {
        $errors = [];
        $user   = Auth::user();

        $file_id      = $request->file_id ? remove_specialcharcters($request->file_id) : 0;
        $File   = CloudDriveFile::find($file_id);
        if($File) {
            if($File->created_by !== $user->id) {
                return response()->json([
                    'code'  => "401",
                    'status'    => "error",
                    'message'   => "You don't have permission to delete this file.",
                ], 200);
            }

            try {
                $folder_id  = $File->folder_id;

                Storage::disk('s3')->delete($File->file_path);
                $File->delete();

                return response()->json([
                    'code'  => "200",
                    'status'    => "success",
                    'message'   => 'File deleted successfully.',
                    'parent_folder_id'   => $folder_id,
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'code'  => "400",
                    'status'    => "error",
                    'message'   => "Something going wrong, please try again after sometime.". $e->getMessage()
                ], 200);
            }
        }

        return response()->json([
            'code'  => "404",
            'status'    => "error",
            'message'   => "File not exist."
        ], 200);
    }

    /** -----------------------------------------------------------------------
     * API Name: cloud-drive/delete-folder
     * User Role: probationer, drillinspector
     * Description: To delete folder
     *
     * Author: https://github.com/rahaman-m
     * --------------------------------------------------------------------- */
    public function destroy_folder(Request $request)
    {
        $errors = [];
        $user   = Auth::user();

        $folder_id      = $request->folder_id ? remove_specialcharcters($request->folder_id) : 0;
        $Folder   = CloudDriveFolder::find($folder_id);
        if($Folder) {

            if($Folder->created_by !== $user->id) {
                return response()->json([
                    'code'  => "401",
                    'status'    => "error",
                    'message'   => "You don't have permission to delete this folder.",
                ], 200);
            }

            try {

                $parent_id  = $Folder->parent_id;
                $files      = [];
                $folders    = [$parent_id];

                // function recurse_assets($parent_id, &$files, &$folders) {
                //     // Get sub folders
                //     $sub_folders    = CloudDriveFolder::where('parent_id', $parent_id)->get();
                //     foreach($sub_folders as $sub_folder) {

                //     }
                // }

                $filesQ = CloudDriveFile::where('folder_id', $folder_id);
                $files  = $filesQ->get();
                foreach($files as $file) {
                    Storage::disk('s3')->delete($file->file_path);
                }
                $filesQ->delete();
                $Folder->delete();

                return response()->json([
                    'code'  => "200",
                    'status'    => "success",
                    'message'   => 'Folder deleted successfully.',
                    'parent_folder_id'   => $parent_id,
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'code'  => "400",
                    'status'    => "error",
                    'message'   => "Something going wrong, please try again after sometime.". $e->getMessage()
                ], 200);
            }
        }

        return response()->json([
            'code'  => "404",
            'status'    => "error",
            'message'   => "Folder not exist."
        ], 200);
    }
}
