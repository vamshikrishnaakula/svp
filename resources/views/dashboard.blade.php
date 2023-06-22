{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')



<section id="dashboard" class="content-wrapper tab-content">

    <div class="row">
        
        <div class="col-md-3">
            <div class="performer">
                <div class="performer_head">
                    <img src="./images/performer.png" />
                    <h6>Best Performer <br>(Individual)</h6>
                </div>
                <div class="performer_details mt-3">
                    <img src="./images/best_performer1.png" />
                    <h6>Roll No : 123456 </h6>
                    <h6>Name: dnweoewio</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="eventsInfo">
                <h6 class="text-center">Events</h6>
                <ul class="mt-2">
                    <li>Dikshant Parade of IPS Probationers of 71RR (2018 batch)</li>
                    <li>Training of Trainers programme for faculty of State Police Academies</li>
                    <li>Mid Career Training Programme Phase-III /15th Programme</li>
                    <li>05 days Course on ‘Counter Terrorism - VII'</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="academy_circular">
                <h5>Academy Newsletter</h5>
                <ul class="circular">
                    <li>New Director 12</li>
                    <li>Independence Day 14</li>
                    <li>33rd SVP Memorial Lecture by Hon’ble Mr. Justice Ranjan Gogoi, The Chief Justice of India 15
                    </li>
                    <li>Blood Donation Camp 16</li>
                </ul>
            </div>


        </div>
        <div class="col-md-3">
            <div class="performer">
                <div class="performer_head">
                    <h6>Best Performer <br>(squad)</h6>
                </div>
                <div class="performer_details mt-2">
                    <img src="./images/performer_squad.png" />
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="weather"> 
                <h6>Weather</h6>
                <h2 class="text-end">32&deg;C</h2>
                <h5>HYDERABAD</h5>
                <div class="text-center">
                    <img src="./images/weather.png" />
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
