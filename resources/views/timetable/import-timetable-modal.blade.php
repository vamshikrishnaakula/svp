<div class="mt-4">
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#dataSheetTab">Download Data Sheet</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#uploadFileTab">Upload</a></li>
    </ul>

    <div class="tab-content">
        <div id="dataSheetTab" class="tab-pane fade in active show">
            <form name="download_timetableDatasheet_form" id="download_timetableDatasheet_form" action="#" method="post" class="mt-3" accept-charset="utf-8">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            @php
                                $batches = DB::table('batches')->get();
                            @endphp
                            <label class="for-required mb-0">Select Batch:</label>
                            <select name="data_batch_id" id="data_batch_id" onchange="window.select_batchId_changed(this, 'data_squad_id');" class="form-control reqField">
                                <option value="">Select batch...</option>
                                @if( !empty($batches) )
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="mb-0">Select Squad:</label>
                            <select name="data_squad_id" id="data_squad_id" class="form-control">
                                <option value="">Select...</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="for-required mb-0">Date (From):</label>
                        <div class="form-group">
                            <input type="text" name="data_from_date" id="data_from_date" class="form-control datePicker reqField" placeholder="YYYY-MM-DD" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="for-required mb-0">Date (To):</label>
                        <div class="form-group">
                            <input type="text" name="data_to_date" id="data_to_date" class="form-control datePicker reqField" placeholder="YYYY-MM-DD" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="mb-0">Sessions per day:</label>
                        <div class="form-group">
                            <input type="number" name="sessions_per_day" id="sessions_per_day" value="5" class="form-control reqField" placeholder="0" min="1" max="20" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </form>

            <div id="download_timetableDatasheet_status" class="mt-3"></div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary"  onclick="window.download_timetableDatasheet();">Download</button>
            </div>
        </div>
        <div id="uploadFileTab" class="tab-pane fade">
            <form name="importTimetable_form" id="importTimetable_form" action="#" method="post" class="text-center mt-3" enctype="multipart/form-data" accept-charset="utf-8">
                <input type="file" name="timetable_csv" accept=".csv" />
            </form>

            <div id="importTimetable_status" class="mt-3"></div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary"  onclick="window.importTimetable_submit();">Submit</button>
            </div>
        </div>
    </div>

    <hr />

    <div class="font-family-roboto mt-3" style="font-size: 90%">
        <h5>Instructions</h5>
        <p>Follow this steps to create/update timetables:</p>
        <ol>
            <li>Download the data sheet (xlsx file) from <i>"Download Data Sheet"</i> tab for a selected batch and date range for which you want to create/update timetables.</li>
            <li>Open the downloaded file and select the 1st sheet i.e. 'Timetable' then update the informations in activity_name, subactivity_name, time_start (HH:MM) and time_end (HH:MM) column only. Do not change informations in any other columns.</li>
            <li>Do not change the column title in first row.</li>
            <li>Save the updated file (using <b>Save As...</b> option under <b>File</b> menu) as &ldquo;<b>CSV (Comma delimited)(*.csv)</b>&rdquo; format. And Upload the new (.csv file) here in <i>"Upload"</i> tab.</li>
        </ol>
    </div>
</div>
