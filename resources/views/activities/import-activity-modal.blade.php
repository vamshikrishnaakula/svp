<div class="mt-4">
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#dataSheetTab">Download Data Sheet</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#uploadFileTab">Upload</a></li>
    </ul>

    <div class="tab-content p-3">
        <div id="dataSheetTab" class="tab-pane fade in active show">
            <form name="download_activityDatasheet_form" id="download_activityDatasheet_form" action="" method="post" class="mt-3" accept-charset="utf-8">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            @php
                                $batches = DB::table('batches')->get();
                            @endphp
                            <label class="for-required mb-0">Select Batch:</label>
                            <select name="data_batch_id" id="data_batch_id"  class="form-control reqField">
                                <option value="">Select batch...</option>
                                @if( !empty($batches) )
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            <div id="download_probationerDatasheet_status" class="mt-3"></div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary mr-4 btn-sm"  onclick="window.export_activities_submit();">Download</button>

               {{--   <a href="#" onclick="window.get_probationerImport_sample()" data-toggle="tooltip" title="Import Timetable"> <img src="{{ asset('images/import.png') }}" /></a>  --}}
            </div>
        </div>
        <div id="uploadFileTab" class="tab-pane fade">
            <form name="importActvity_form" id="importActvity_form" action="#" method="post" class="text-center mt-3" enctype="multipart/form-data" accept-charset="utf-8">
                <input type="file" name="activity_csv" accept=".csv" />
            </form>


            <div id="download_importActvity_status" class="mt-3"></div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary"  onclick="window.import_activityDataSheet_submit();">Submit</button>
            </div>
        </div>
    </div>

    <hr />
</div>
