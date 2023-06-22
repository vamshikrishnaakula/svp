<div class="mt-4">
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#dataSheetTab">Download Data Sheet</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#uploadFileTab">Upload</a></li>
    </ul>

    <div class="tab-content">
        <div id="dataSheetTab" class="tab-pane fade in active show">
            <form name="downloadDataSheet_form" id="downloadDataSheet_form" action="#" method="post" class="mt-3" accept-charset="utf-8">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            @php
                                $batches = DB::table('batches')->get();
                            @endphp
                            <label class="mb-0">Select Batch:</label>
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
                            <select name="data_squad_id" id="data_squad_id" class="form-control reqField">
                                <option value="">Select...</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="mb-0">Date (From):</label>
                        <div class="form-group">
                            <input type="text" name="data_from_date" id="data_from_date" class="form-control datePicker reqField" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="mb-0">Date (To):</label>
                        <div class="form-group">
                            <input type="text" name="data_to_date" id="data_to_date" class="form-control datePicker reqField" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </form>

            <div id="downloadDataSheet_status" class="mt-3"></div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary"  onclick="window.downloadDataSheet_Submit();">Download</button>
            </div>
        </div>
        <div id="uploadFileTab" class="tab-pane fade">
            <form name="importDataSheet_form" id="importDataSheet_form" action="#" method="post" class="text-center mt-3" enctype="multipart/form-data" accept-charset="utf-8">
                <input type="file" name="data_csv" accept=".csv" />
            </form>

            <div id="importDataSheet_form_status" class="mt-3"></div>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-primary"  onclick="window.importDataSheet_Submit();">Submit</button>
            </div>
        </div>
    </div>

    <hr />

    <div class="font-family-roboto mt-3" style="font-size: 90%">
        <h5>Instructions</h5>
        <p>Follow this steps to update the required statistics data:</p>
        <ol>
            <li>Download the data sheet (csv file) from <i>"Download Data Sheet"</i> tab for a selected batch, squad and date range for which you want to update the statistics data.</li>
            <li>Open the downloaded file and select the 1st sheet i.e. 'AttendanceData' then update the informations in component, attendance, count and grade column only. Do not change informations in any other columns.</li>
            <li>Do not change the column title in first row.</li>
            <li>Save the updated file (using <b>Save As...</b> option under <b>File</b> menu) as &ldquo;<b>CSV (Comma delimited)(*.csv)</b>&rdquo; format. And Upload the new (.csv file) here in <i>"Upload"</i> tab.</li>
        </ol>
    </div>
</div>
