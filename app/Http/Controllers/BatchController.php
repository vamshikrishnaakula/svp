<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use App\Models\User;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $batches = Batch::all();

        // $users = User::select('email')->get();
        // foreach($users as $user)
        // {
        //     $username = substr($user->email, 0, strpos($user->email, "@"));
        //     try
        //     {
        //         User::where('email', $user->email)->update([
        //             'username' => $username,
        //         ]);
        //     }
        //     catch (\Illuminate\Database\QueryException $e)
        //     {
        //         User::where('email', $user->email)->update([
        //             'username' => $user->email,
        //         ]);
        //     }

        // }


        

        return view('batch.batch',compact('batches'));
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
        try
        {
        $request->validate([
            'BatchName' => 'required',
        ]);

        Batch::create($request->all());
        return redirect()->route('batch.index')
                        ->with('success','Batch created successfully.');
        }
        catch(\Illuminate\Database\QueryException $e){
        $errorCode = $e->errorInfo[1];
        if($errorCode == '1062'){
        return redirect()->route('batch.index')->with('delete','Batch Already Registered.');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function show(Batch $batch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function edit(Batch $batch)
    {
        $batches = Batch::all();
        $row = Batch::where('id', $batch->id)->first();
        return view('batch.updatebatch',compact('batches', 'row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Batch $batch)
    {
        try{
        $request->validate([
            'BatchName' => 'required',
        ]);
        $batch->update($request->all());
        return redirect()->route('batch.index')
                        ->with('success','Batch Updated successfully.');
      }
       catch(\Illuminate\Database\QueryException $e){
       $errorCode = $e->errorInfo[1];
        if($errorCode == '1062'){
              return redirect()->route('batch.index')->with('delete','Batch Already Registered.');
         }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Batch $batch)
    {
        // $batch = Batch::find($batch);
        $batch->delete();
        return redirect()->route('batch.index')->with('delete','Batch deleted successfully');
    }
}
