{{-- Extends Pb Dashboard Template --}}
@extends('layouts.doctor.template')

{{-- Content --}}
@section('content')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3/dist/fullcalendar.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.0/fullcalendar.min.css"/>

<section id="doctor-appointments" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Appointments</h4>
            </div>
        </div>

        <ul class="nav nav-tabs mt-4 appointment-tab">
            <li class="nav-item"><a href="#current" class="nav-link active" data-toggle="tab">Current</a></li>
         <li class="nav-item"><a href="#history" class="nav-link" data-toggle="tab">History</a></li>

        </ul>

        <div class="tab-content">

            <div id="current" class="tab-pane fade in active show mt-4">


                <div class="current-appointments">
                    <table class="table table-bordered table-hover inpatient_list_table">
                         <thead class="thead-dark">
                            <tr>
                                <th width="2%">S.No</th>
                                <th>Patient Name</th>
                                <th>Appointment Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                               @if(!$today_appoinments->isEmpty())
                             @foreach($today_appoinments as $today_appoinment)
                               <tr class="go_prescription_url" onclick="window.location='{{ route('prescription', $today_appoinment->appoinmentid) }}';">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $today_appoinment->Name}}</td>
                                    <td>{{date('h:i a', strtotime($today_appoinment->Appoinment_Time))}}</td>
                                    <td>{{ $today_appoinment->Status}}</td>
                                </tr>

                            @endforeach
                            @else
                             <tr>
                                <td colspan="4">No Appointments</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="history" class="tab-pane fade mt-4">

              <div class="row">
                    <div class="col-md-10">
                </div>
                    <div class="col-md-2">
                        <div class="history_date">
                            <input class="form-control" type="text" id="getDate" value=""/>
                        </div>
                    </div>
                </div>
                <!--Full Calendar-->

                <div class="row">
                    <div class="col-md-12">
                         <div id="calendar"></div>
                    </div>
                </div>
                <div class="history-appointments">
                    <table class="table table-borderless" id="historys">
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>

    $(document).ready(function () {
    $("#getDate, .history-appointments").hide();
     $('#calendar').fullCalendar({
            header: {
                left: 'prev,title,next',
                right: '',
                center:''
            },
            defaultDate: new Date(),
            showNonCurrentDates: false,
            navLinks: false,
            editable: true,
            eventLimit: true,
            selectable: true,
            dayClick: function(date) {
                $("#getDate").val(date.format("DD-MMM-YYYY"));
                $("#getDate, .history-appointments").show();
                $("#calendar").hide();
                var date = date.format("DD-MM-YYYY");
                $.ajax({
                    url: '/inpatients_history',
                    type: "POST",
                    data:{
                        "_token": "{{ csrf_token() }}",
                            "date":date,
                        },
                    success: function(data){
                    if(data != '')
                        {
                             $.each(data, function(i) {
                                 var dt = new Date(data[i].Appoinment_Time);
                                 var hours = dt.getHours();
                                var minutes = dt.getMinutes();
                                var ampm = hours >= 12 ? 'pm' : 'am';
                                hours = hours % 12;
                                hours = hours ? hours : 12; // the hour '0' should be '12'
                                minutes = minutes < 10 ? '0'+minutes : minutes;
                                var time = hours + ':' + minutes + ' ' + ampm;
                                 var url = "'{{ url ('/appointment_summary/:id' )}}'";
                                 url = url.replace(':id', data[i].appoinmentid);
                                 var now = new Date(data[i].Appoinment_Time);
                                $('#historys tbody').append('<tr class="dynamic_row" onclick="window.location ='+ url +';"><td><b>Patient Name : &nbsp;</b>'+ data[i].Name+'</td><td><b>Appointment Time : &nbsp;</b>'+ time +'</td><td><b>Status : &nbsp;</b>'+ data[i].Status+'</td></tr>');
                             });
                             $('table tr').click(function(){
                                location.href = url;
                            });
                        }
                        else
                        {
                            $('#historys tbody').append('<tr><td></td><td></td><td></td></tr>');
                        }
                    }
                })
            }
        });
        $('.fc-left div > h2').html(function (i, v) {
            return v.replace(/(\s)(\w+)/, '$1<span class="title-year ">$2</span>');
        });
        handleYear = () => {
            $('.fc-left div > h2').html(function (i, v) {
                return v.replace(/(\s)(\w+)/, '$1<span class="title-year ">$2</span>');
            });
        }

        $('.fc-prev-button').click(function () {
            handleYear();
            $(".fc-content-skeleton tbody tr td ").hide();
        });

        $('.fc-next-button').click(function () {
            handleYear();
        });

        $(".fc-left").append('<select class="select_year custom-select"></select>');
        $(".select_year").on("change", function (event) {
            $('#calendar').fullCalendar('changeView', 'month', this.value);
            $('#calendar').fullCalendar('gotoDate', this.value);
            handleYear();
        });
        var nowY = new Date().getFullYear(),
            options = "";

        for (var Y = nowY; Y >= 2015; Y--) {
            options += "<option>" + Y + "</option>";
        }
        $(".select_year").append(options);

        $("#getDate").focus(function(){
            $('#historys tbody').empty();
            $(this).hide(500);
            $(".history-appointments").hide();
             $("#calendar").show(500);
        })

        /* Rendering fullcalendar  End*/
function formatAMPM(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'pm' : 'am';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ' ' + ampm;
  return strTime;
}


    })
</script>

@endsection
