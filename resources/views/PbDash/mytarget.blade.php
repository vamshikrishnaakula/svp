{{-- Extends Pb Dashboard Template --}}
<?php
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : 'layouts.pbdash.template')

{{-- Content --}}
@section('content')

<section id="mytarget" class="pt-4 mt-5">
    <div class="">
        <div class="row mt-3 mobile_mgn">
            <div class="col-md-6">
            @if(!$app_view )
                    <h4>My Target</h4>
            @endif
            </div>
        </div>

        <?php

        $userId = $user_id;
        $batchId = App\Models\probationer::where('user_id', $userId)->value('batch_id');
        $activities = App\Models\Activity::where('type', 'activity')->where('batch_id', $batchId)->get();
        ?>

        <div class="targets">
            <div class="row p-0">
                @foreach ($activities as $activity)

                <div class="col-md-4 mb-0">
                    <a href="{{ url('user-mytarget/'. $activity->id) }}">
                        <div class="borderbox mb-2">
                            <span>{{ $activity->name }}</span>
                        </div>
                    </a>
                </div>

                @endforeach

            </div>
        </div>
    </div>
</section>

@endsection
<style>
    .targets{
        position: relative;
        left:-25px;
        top:20px;
    }
    @media screen and (min-width:320px) and (max-width:767px){
        #mytarget{
            margin: 0px !important;
            padding: 0px !important;
        }
    }
</style>
