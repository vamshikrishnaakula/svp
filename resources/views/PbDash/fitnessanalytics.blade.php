{{-- Extends Pb Dashboard Template --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')

<section id="user-attendance" class="content-wrapper tab-content">
    <div class="user_manage attendance-height">
        <div class="row">
            <div class="col-md-6">
                <h4>Fitness Analytics</h4>
            </div>
        </div>

        <div id="fitnessAnalytics" class="mt-5">

            <div class="fitness-month-selector">
                <div class="month-selector">
                    <div class="form-group">
                        <label>Select Month:</label>
                        <div class="input-group" id="fitness_monthpicker" data-target-input="nearest" name="Dob">
                            <input type="text" class="form-control datetimepicker-input reqField" data-target="#fitness_monthpicker"
                                data-toggle="datetimepicker" name="Dob" id="fitness_month" autocomplete="off" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="usersubmitBtns mt-4">
                        <div class="mr-4">
                            <button type="button" onclick="window.get_fitness_data()"
                                class="btn formBtn submitBtn" >Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="fitnessanalytics-data"></div>

    </div>

</section>

<script>

$(document).ready (function (){
    $("#fitness_monthpicker").datetimepicker({
        viewMode: 'months',
        format: 'MM-YYYY'
    })
});

</script>

@endsection
