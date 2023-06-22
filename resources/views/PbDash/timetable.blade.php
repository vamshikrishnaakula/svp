{{-- Extends Pb Dashboard Template --}}
@extends('layouts.pbdash.template')

{{-- Content --}}
@section('content')

<section id="user-timetable" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>View Time Table</h4>
            </div>
        </div>

        <div class="">

            <form name="user_timetable_form" id="user_timetable_form" action="{{ url('user-ajax') }}"
                method="post" class="fitness-form-date mt-5" accept-charset="utf-8">
                @csrf

                <div class="timetable-selector">
                    <div class="timetable-time-selector">
                        <div class="form-group">
                            <label>Select Day:</label>
                            <select name="timetable-date" id="timetable_date" class="form-control">
                                <option value="Today">Today</option>
                                <option value="Tomorrow">Tomorrow</option>
                                <option value="Week">Week</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="usersubmitBtns mt-4">
                            <div class="mr-4">
                                <button type="button" onclick="window.get_user_timetable()"
                                    class="btn formBtn submitBtn">Proceed</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div id="user_timetable-container" class="table-responsive mt-5"></div>

    </div>

</section>

@endsection
