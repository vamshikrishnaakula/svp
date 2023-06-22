{{-- Extends Pb Dashboard Template --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')

<section id="user_extrasession" class="content-wrapper_sub ">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Extra Session</h4>
            </div>
        </div>

        <div id="extrasessions" class="">

            <div>
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
            </div>


            <table class="table text-center mt-4" id="extrasession-table">
                <thead class="thead">
                    <tr>
                        <th>Sl No.</th>
                        <th>Activity</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</section>
<script>

$(document).ready (function (){
    $("#insert_datetimepicker").datetimepicker ({
        viewMode: 'months',
        format: 'MM-YYYY'
    });
});


</script>
@endsection
