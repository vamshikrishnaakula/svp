{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="generalassesment" class="content-wrapper_sub">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-9">
                <h4>General Assessment</h4>
            </div>
        </div>
        <form method="POST" class="width-half rl-margin-auto">
            @csrf
            <div class="row mt-5">
                <div class="col-md-6">
                    <label>Select Batch</label>
                    <select class="form-control" id="batch_id" name="batch_id" onchange="window.select_batchId_changed(this, 'squad_id');" required>
                        <option value="">Select Batch</option>
                        @if( !empty($batches) )
                        @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                        @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Select Squad</label>
                    <select name="squad_id" id="squad_id" class="form-control">
                        <option value="">Select Squad</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="usersubmitBtns mt-5">
                        <div class="mr-4">
                            <button type="button" class="btn formBtn submitBtn" onclick="problist('batch_id', 'squad_id')">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
        <div class="squadlisthead mt-5" style="display: none">
            <div class="row">
                <div class="col-md-9">
                    <div class="activityhead">
                        <h5 class="mb-0 ml-4">Probationer List</h5>
                    </div>
                </div>
                <div class="col-md-3 patient_userBtns">
                </div>
            </div>
        </div>
        <div class="listdetails">
            <div class="table-responsive">
                <table class="table" id="probationerslist">
                    <thead></thead>
                    <tbody></tbody>

                </table>
            </div>
        </div>

    </div>
</section>

@endsection

@section('scripts')
<script>
    function problist(Bid, Sid)
        {
            var sid = $('#squad_id').val();
            $.ajax({
           url: '/squads/view-probationers',
           type: "POST",
           data:{
               "_token": "{{ csrf_token() }}",
                "id":sid,
               },
           success: function(data){
             var x=1;
             $(".squadlisthead").show();
             $("#probationerslist thead").empty();
             $("#probationerslist tbody").empty();
             $('#probationerslist thead').append('<tr><th>S.No</th><th>Roll Number</th><th>Name</th><th></th></tr>');
             if(data != '')
             {
             $.each(data, function(i) {
                var url = "{{ url ('/general-assessment/:id')}}";
                url = url.replace(':id', data[i].id);
                   $('#probationerslist tbody').append('<tr id = "id'+data[i].RollNumber+'"><td>'+ x++ +'</td><td>' + data[i].RollNumber + '</td><td>' + data[i].Name + '</td><td><a href='+ url +'><img src="/images/view.png" /></a></td></tr>');
               });
             }


           }
       })
        }
</script>
@endsection
