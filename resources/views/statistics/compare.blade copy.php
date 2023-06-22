{{-- Extends layout --}}
<?php
$app_view = session('app_view');
?>
@extends(($app_view) ? 'layouts.pbdash.mobile-template' : 'layouts.default')

{{-- Content --}}
@section('content')

<div id="error"></div>
@if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
  @elseif ($message = Session::get('delete'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>

    @endif
<section id="addstaff" class="content-wrapper_sub">
    <div class="row">
        <div class="col-md-12">
            <h3>Compare</h3>
        </div>
    </div>
    <div class="row mt-3 compare_header align-items-center">
        <div class="col-md-6">
                <div class="row align-items-center">
                    <label class="mb-0">Batch</label>
                    <div class="col-sm-4">
                        <select class="form-control mb-0" id="batch_id" name="batch_id">
                            <option value="">Select Batch</option>
                            @if( !empty($batches) )
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                     <a class="pl-2" class="btn formBtn submitBtn"><img src="{{ asset('images/submit.png') }}" width="25" /></a>
                </div>
        </div>
        <div class="col-md-6 text-right">
            <div class="download_section ">
                <a class="mr-4"><img src="{{ asset('images/download1.png') }}" width="25" /></a>
                <a><img src="{{ asset('images/print_view_icon.svg') }}" width="25" /></a>
            </div>
        </div>
    </div>
  <div class="row mt-4 user_card_section">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                    <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Admin" class="rounded-circle"
                        width="100">
                </div>
                <div class="mt-5">
                    <div class="form-group">
                        <select name="squad_id" id="squad_id" class="form-control" onchange="window.select_squad_id_changed(this, 'probationer_id');" required>
                            <option value="">Select Squad</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                         <select name="probationer_id" id="probationer_id" class="form-control" required>
                            <option value="">Select Probationer</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                    <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Admin" class="rounded-circle"
                        width="100">
                </div>
                <div class="mt-5">
                    <div class="form-group">
                       <select name="squad_id" id="squad_id" class="form-control" onchange="window.select_squad_id_changed(this, 'probationer_id');" required>
                            <option value="">Select Squad</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                         <select name="probationer_id" id="probationer_id" class="form-control" required>
                            <option value="">Select Probationer</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                    <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Admin" class="rounded-circle"
                        width="100">
                </div>
                <div class="mt-5">
                    <div class="form-group">
                        <select name="squad_id" id="squad_id" class="form-control" onchange="window.select_squad_id_changed(this, 'probationer_id');" required>
                            <option value="">Select Squad</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                         <select name="probationer_id" id="probationer_id" class="form-control" required>
                            <option value="">Select Probationer</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center text-center">
                    <img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Admin" class="rounded-circle"
                        width="100">
                </div>
                <div class="mt-5">
                    <div class="form-group">
                       <select name="squad_id" id="squad_id" class="form-control" onchange="window.select_squad_id_changed(this, 'probationer_id');" required>
                            <option value="">Select Squad</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                         <select name="probationer_id" id="probationer_id" class="form-control" required>
                            <option value="">Select Probationer</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="compare_btn text-center">
            <button type="buttton" class="btn btn-primary">Compare</button>
        </div>
    </div>
</div>
<div class="copare_Section_hide">
<div class="filter_block">
<div class="row mt-3">
    <div class="col-md-1 pr-0">
        <div class="filter_title">
            <label>Filters :</label>
        </div>
    </div>
    <div class="col-md-11">
        <div class="fliter_section row">
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Physical Training
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Weapon Training
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Drill
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">FC & Tactics
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Equation
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Games
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
        </div>
    </div>
    <div class="col-md-1 pr-0"></div>
    <div class="col-md-11">
        <div class="fliter_section row mt-3">
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Attendence
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">UAC
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Health Profile
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Medicall Records
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button">Fitness Evalution
                <span class="hidden close_icon" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
            <button type="button" data-toggle="collapse" class="col-sm-2 button selectAll">Select all
                 <span class="hidden close_icon_all" aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>

        </div>
    </div>
</div>
<div class="row mt-4 select_group">
    <div class="col-md-1"></div>
    <div class="col-md-11 pl-0">
        <div class="row">
    <div class="col-md-2">
        <div class="form-group">
            <select class="form-control">
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <select class="form-control">
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <select class="form-control">
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <select class="form-control">
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <select class="form-control">
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
                <option>AAA</option>
            </select>
        </div>
    </div>
</div>
</div>
</div>
</div>
<div class="row mt-3">
    <div class="col-md-12">
        <div class="filter_activity_header text-center">
            <h5>Activity : Physical Trainig</h5>
        </div>
    </div>
    <div class="table-responsive filter_table">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sub Activity : Endurance</th>
                    <th>Grade</th>
                    <th>Count</th>
                    <th>Grade</th>
                    <th>Count</th>
                    <th>Grade</th>
                    <th>Count</th>
                    <th>Grade</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                 <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</div>
</section>
@endsection



@section('scripts')
    <script>
        $(document).ready(function () {


            $('.hidden').hide();
            $('.button').click(function () {
                $(this).addClass("blue").removeClass("transpo")
                $(this).find('span').each(function () {
                    $(this).show();
                });

            });

            $(".close_icon").on('click', function () {
                debugger
                var test = this.parentElement;
                $(test).removeClass("blue").addClass("transpo")
                $(this).hide();
                return false;
            });
            /** Select All**/
            $(".selectAll").click(function () {
                $(".button").addClass("blue").removeClass("transpo")
                $('.hidden').show();
            });
            $(".close_icon_all").on("click", function () {
                $(".button").removeClass("blue").addClass("transpo")
                $('.hidden').hide();
                return false;

            });

            $(".copare_Section_hide").hide();
            $(".compare_btn button").on("click", function () {
                $(".copare_Section_hide").toggle("500");
            });
        });
</script>
@endsection
