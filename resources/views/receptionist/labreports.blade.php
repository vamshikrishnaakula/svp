{{-- Extends layout --}}
@extends('layouts-Receptionist.default')

{{-- Content --}}
@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible">
            <p>{{ $message }}</p>
        </div>
    @elseif ($message = Session::get('delete'))
        <div class="alert alert-danger  alert-dismissible">
            <p>{{ $message }}</p>
        </div>
    @endif
    <section id="reports" class="p-5 content-wrapper_sub">
        <div class="row">
            <div class="col-md-11">
                <h4>Lab Reports / Prescription </h4>
            </div>
            <div class="col-md-1">
                <a href="{{ url('/receptionist') }}" class="back_to_reportrs"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Patient ID</label>
                    <input type="text" class="form-control" id="rollnumber" required />
                    @error('Probationer_Id')
                        <p class="text-danger">{{ 'Please Select the Probationer' }}</p>
                    @enderror
                    <input type="hidden" class="form-control" id="token" value="{{ csrf_token() }}" />
                </div>
            </div>
            <div class="col-md-3 getdata">
                <button type="button" class="btn" onclick="window.getData();">Get Data</button>
            </div>
        </div>

        <div class="reportinfo row mt-5">
            <div class="col-md-4">
                <label>Patient Name :</label>
                <span class="ml-3" id="Pname"></span>
            </div>
            <div class="col-md-3">
                <label>Gender :</label>
                <span class="ml-3" id="gender"></span>
            </div>

        </div>

        <div class="reportsblock mt-5">
            <form action="{{ url('fileupload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label>Document Type : </label>
                        <select class="form-control" id="doctype" name="doctype" required>
                            <option value="">Select...</option>
                            <option value="prescription">Prescription</option>
                            <option value="investigationreport">Investigation Report</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                </div>

                <div class="row" style="display: none;" id="dhname">
                    <div class="col-md-3">
                        <label> Doctor Name : </label>
                        <input type="text" class="form-control" name="doc_name" id="doc_name">
                    </div>
                    <div class="col-md-3">
                        <label> Hospital Name: </label>
                        <input type="text" class="form-control" name="hos_name" id="hos_name">
                    </div>
                </div>


                <div class="totalhide" style="display:none;">
                    <h6 class="mb-4" id="testnamehide">Test Name</h6>
                    <div class="row">
                        <div class="col-md-6" id="reportnamehide">
                            <input type="text" class="form-control" name="ReportName" />
                            @error('ReportName')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="col-md-6 usersubmitBtns">
                            <div class="upload-btn-wrapper">
                                <button class="upload">Browse</button>
                                <input type="file" class="form-control" name="file" id="file" />
                                <div class="file-label mt-2"></div>
                                @error('file')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <input type="hidden" class="form-control" name="prob_id" id="prob_id" />
                            <button type="submit" class="btn formBtn submitBtn" style="height: 37px;">Submit</button>
                        </div>
                    </div>
                </div>
        </div>
        </form>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('js/receptionist.js') }}" type="text/javascript"></script>
    <script>
        $('input[type="file"]').change(function(e) {
            var fileName = e.target.files[0].name;
            $('.file-label').html(fileName);
        });
    </script>


    <script>
        $(document).on('keydown.autocomplete', '#rollnumber', function(e) {

            $(this).autocomplete({
                source: "{{ route('prob_autosuggestion') }}",
                minLength: 1,
                select: function(event, ui) {
                    $("#prob_id").val(ui.item['id']);

                }
            });
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#doctype').on('change', function() {
            debugger;
            var val = $(this).val();
            if (val === "prescription") {
                $("#dhname, .totalhide").show();
                $("#testnamehide, #reportnamehide").hide();
            } else if (val === "") {
                $(".totalhide, #dhname").hide();
            } else {
                $("#dhname").hide();
                $(".totalhide, #testnamehide, #reportnamehide").show();
            }
        });
    </script>
@endsection
