
    <div class="fitness-user-manage">

        <ul class="nav nav-tabs nav-fill fitnessanlytics-tab">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#fitness">Fitness</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#endurance">Endurance</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#strength">Strength</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#flexibility">Flexibility</a></li>
        </ul>
        <div class="tab-content">
            <div id="fitness" class="tab-pane fade in active show mt-5">
                @foreach ($fitness_data as $fitness_values)
                    @if (in_array($fitness_values->fitness_name, ['weight', 'bmi', 'bodyfat', 'fitnessscore']))
                        <div class="row fitness-analytics mt-3">
                            <div class="col-6">{{ $fitness_values->fitness_name }}</div>
                            <div class="col-6"> {{ $fitness_values->fitness_value }} </div>
                        </div> 
                    @endif
                @endforeach
                
                    </div>
            <div id="endurance" class="tab-pane fade">

                @foreach ($fitness_data as $fitness_values)
                @if (in_array($fitness_values->fitness_name, ['endurancegrade']))
                    <div class="row fitness-analytics mt-3">
                        <div class="col-6">{{ $fitness_values->fitness_name }}</div>
                        <div class="col-6"> {{ $fitness_values->fitness_value }} </div>
                    </div> 
                @endif
            @endforeach
            </div> 


            <div id="strength" class="tab-pane fade">
                @foreach ($fitness_data as $fitness_values)
                @if (in_array($fitness_values->fitness_name, ['strengthgrade']))
                    <div class="row fitness-analytics mt-3">
                        <div class="col-6">{{ $fitness_values->fitness_name }}</div>
                        <div class="col-6"> {{ $fitness_values->fitness_value }} </div>
                    </div> 
                @endif
            @endforeach
            </div>

            <div id="flexibility" class="tab-pane fade">
                @foreach ($fitness_data as $fitness_values)
                @if (in_array($fitness_values->fitness_name, ['flexibilitygrade']))
                    <div class="row fitness-analytics mt-3">
                        <div class="col-6">{{ $fitness_values->fitness_name }}</div>
                        <div class="col-6"> {{ $fitness_values->fitness_value }} </div>
                    </div> 
                @endif
            @endforeach
            </div>
        </div>
    </div>
