{{-- Extends layout --}}
@extends('layouts.doctor.template')

{{-- Content --}}
@section('content')

<section id="appointments" class="content-wrapper_sub tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h5>Today's Appointments</h5>
            </div>
            {{-- <div class="col-md-3">
              <div class="form-group">
                <label>Date:</label>
                <div class="form-group">
                  <div class="input-group date" id="datetimepicker3" data-target-input="nearest" style="width: 100% !important; margin-left: 0px;">
                      <input type="text" class="form-control datetimepicker-input"
                          data-target="#datetimepicker3"  data-toggle="datetimepicker"/>
                  </div>
              </div>
              </div>
            </div> --}}
        </div>

          <div class="appointments mt-4">
            <div id="today">
                <ul>
                @if(!$today_appoinments->isEmpty())
                @foreach($today_appoinments as $today_appoinment)
                    <li>
                      <a href="{{ route('prescription', $today_appoinment->appoinmentid) }}" >
                        <div class="row">
                            <div class="col-md-7">
                                <span>{{ $today_appoinment->Name}}</span>
                            </div>
                            <div class="col-md-2">
                                <span>{{date('h:i a', strtotime($today_appoinment->Appoinment_Time))}}</span>
                            </div>
                            <div class="col-md-3">
                                <label>Status :</label><span class="ml-5">{{ $today_appoinment->Status}}</span>

                            </div>
                        </div>
                      </a>
                    </li>
                @endforeach
                @else
                 <tr>
                   <td colspan="4">No Appointments</td>
                 </tr>
                @endif

                </ul>
            </div>
                </div>
            </div>

            <!-- <div class="user_manage">
         <div class="appointments mt-4">
            <div id="today">
                <ul>
                @foreach($today_appoinments_closed as $today_appoinment)
                    <li>
                      <a href="{{ route('prescription', $today_appoinment->appoinmentid) }}" >
                        <div class="row">
                            <div class="col-md-7">
                                <span>{{ $today_appoinment->Name}}</span>
                            </div>
                            <div class="col-md-2">
                                <span>{{date('h:i a', strtotime($today_appoinment->Appoinment_Time))}}</span>
                            </div>
                            <div class="col-md-3">
                                <label>Status :</label><span class="ml-5">{{ $today_appoinment->Status}}</span>

                            </div>
                        </div>


                      </a>
                    </li>
                @endforeach

                </ul>
            </div>
                </div>
            </div> -->
</section>
@endsection
