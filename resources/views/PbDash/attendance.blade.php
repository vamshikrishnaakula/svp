{{-- Extends Pb Dashboard Template --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')

<section id="user-attendance" class="content-wrapper_sub tab-content attendance_report">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Attendance Report</h4>
            </div>
        </div>

        <ul class="nav nav-tabs nav-fill mt-4 attendance-tab">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#regularsessions">Regular Sessions</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#monthlysessions">Monthly Sessions</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#missedsessions">Missed Sessions</a></li>
            {{-- <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#extrasessions">Extra Sessions</a></li> --}}
        </ul>

        {{-- ***************************************************** Regular Sessions start **************************************************** --}}

        <div class="tab-content">
            <div id="regularsessions" class="tab-pane fade in active show monthlyattendance-report">

                <form name="user_attendance_form" id="user_attendance_form" action="{{ url('user-ajax') }}"
                    method="post" class="mt-5 mx-auto" accept-charset="utf-8" style="max-width: 650px;">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Year:</label>
                                <select name="year" id="year" class="form-control reqField">
                                    <option value="">Select year...</option>
                                    @php
                                    $cYear  = date('Y');

                                    for ($y=2020; $y<=$cYear; $y++) {
                                        $ySelected  = ($y == $cYear)? "selected" : "";

                                        echo "<option value=\"{$y}\" {$ySelected}>{$y}</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Month:</label>
                                <select name="month" id="month" class="form-control reqField">
                                    <option value="">Select month...</option>
                                    @php
                                    $cMonth  = date('m');

                                    for ($m=1; $m<=12; $m++) {
                                        $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                                        $mSelected  = ($m == $cMonth)? "selected" : "";

                                        echo "<option value=\"{$m}\" {$mSelected}>{$month}</option>";
                                    }
                                    @endphp
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="usersubmitBtns mt-4">
                                <div class="mr-4">
                                    <button type="button" onclick="window.get_user_attendance();"
                                        class="btn formBtn submitBtn">Proceed</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div id="user_attendance-container" class="table-responsive mt-5"></div>
            </div>


            {{-- ***************************************************** Monthly Report start **************************************************** --}}

            <div id="monthlysessions" class="tab-pane fade in monthlyattendance-report mx-auto">

                <div class="row mt-5 monthlysession-selection">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-3 ">
                        <div class="form-group">
                            <label for="activity">Select Month:</label>
                            <div class="input-group" id="monthlysession-monthpicker" data-target-input="nearest" name="Dob">
                                <input type="text" class="form-control datetimepicker-input reqField" data-target="#monthlysession-monthpicker"
                                    data-toggle="datetimepicker" name="Dob" id="monthlyreport_month" autocomplete="off" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-1">
                        <div class="viewtargetsubmit" style="margin-top: 30px">
                            <a href="#" class="desktop-button" onclick="window.get_monthlysessions_table()"><img src="{{ asset('images/submit.png') }}" /></a>
                            <a href="#" class="btn btn-success btn-sm mobile-button" onclick="window.get_monthlysessions_table()">Submit</a>

                        </div>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div id="user_monthly_session_table" class="table-responsive"></div>

            </div>


            {{-- ***************************************************** Missed Sessions start **************************************************** --}}

            <div id="missedsessions" class="tab-pane fade in">

                <div class="row mt-5 missedsession-selection">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-3 ">
                        <div class="form-group">
                            <label for="activity">Select Month:</label>
                            <div class="input-group" id="missedsession-monthpicker" data-target-input="nearest" name="Dob">
                                <input type="text" class="form-control datetimepicker-input reqField" data-target="#missedsession-monthpicker"
                                    data-toggle="datetimepicker" name="Dob" id="missedsession_month" autocomplete="off" required />
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-1">
                        <div class="viewtargetsubmit" style="margin-top: 30px">
                            <a href="#" class="desktop-button" onclick="window.get_missedsessions_table()"><img src="{{ asset('images/submit.png') }}" /></a>
                            <a href="#" class="btn btn-success btn-sm mobile-button" onclick="window.get_missedsessions_table()">Submit</a>
                        </div>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div id="user_missed_session_table"></div>

            </div>
            {{-- ***************************************************** Extra Sessions start **************************************************** --}}

            {{-- <div id="extrasessions" class="tab-pane fade in ">

                <form name="#" id="#" action="#"
                    method="post" class="fitness-form-date mt-5" accept-charset="utf-8">
                    @csrf

                    <div class="fitness-month-selector">
                        <div class="month-selector">
                            <div class="form-group">
                                <label>Select Day:</label>
                                <div class="input-group" id="fitness_monthpicker" data-target-input="nearest" name="Dob">
                                    <input type="text" name="extrasession_date" id="extrasession_date" class="form-control datePicker reqField" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="usersubmitBtns mt-4">
                                <div class="mr-4">
                                    <button type="button" onclick="window.get_extrasession_data()"
                                        class="btn formBtn submitBtn" >Proceed</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


                <table class="table text-center mt-4" id="extrasession-table">
                    <thead class="thead">
                        <tr>
                            <th>Sl No.</th>
                            <th>Activity</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div> --}}
        </div>
    </div>

</section>

<script>

$(document).ready (function (){
    $("#monthlysession-monthpicker").datetimepicker ({
        viewMode: 'months',
        format: 'MM-YYYY'
    });

    $("#missedsession-monthpicker").datetimepicker ({
        viewMode: 'months',
        format: 'MM-YYYY'
    });
});



</script>

@endsection
