{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

@if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
  @elseif ($message = Session::get('delete'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>

    @endif

    <section id="squadlist1" class="content-wrapper_sub tab-content">
        <div class="user_manage">
          <div class="row">
          <div class="col-md-10">
              <h4>Squad List</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns d-flex justify-content-end">
                    {{-- <a href="#" data-toggle="tooltip" title="download"> <img src="{{ asset('images/download1.png') }}" /></a>
                    <a href="#" class="ml-3" data-toggle="tooltip" title="print"> <img src="{{ asset('images/print1.png') }}" /></a> --}}
                    <a href="{{ url('squads') }}" class="text-center ml-3" data-toggle="tooltip" title="Create Squad">
                        <img src="{{ asset('images/plus-icon-rounded.png') }}">
                        <p class="my-0">Add</p>
                    </a>
                </div>
            </div>
          </div>


          <div class="listdetails mt-4">
            <div class="squadlisthead">
            <div class="row">
                  <div class="group col-md-1">
                    <img src="{{ asset('images/Group.png') }}" />
                  </div>
                  <div class="col-md-7">
                      <div class="row group">
                    {{-- <label class="col-md-4" for="squadno">Select Batch</label> --}}
                    <select class="form-control col-md-4" id="batch_id" name="batch_id">
                      <option>Select  Batch Number</option>
                      @foreach($batch as $batches)
                        <option value="{{ $batches->id }}"  {{$batches->id == $batch_id  ? 'selected' : ''}} >{{ $batches->BatchName }}</option>
                      @endforeach
                    </select>
                </div>
                </div>
                <div class="col-md-4">
                    <!-- <div class="group group_right">
                        <input type="search" placeholder="search" class="form-control" />
                    </div> -->
                  </div>
                  </div>
            </div>
                        <div class="table-responsive">
                            <table class="table"  id="squadlist" name="squadlist">
                              <thead>
                                <tr>
                                 <th>S.NO</th>
                                  <th>Squad Number</th>
                                  <th>Drill Instructor</th>
                                  <th></th>
                                </tr>
                              </thead>
                              <tbody id = "squadlistbody">
                                @foreach ($squadnumber as $lSquad)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lSquad->SquadNumber }}</td>
                                    <td>{{ $lSquad->name }}</td>
                                    <td>
                                        <a href="/squads/view-probationers/{{$lSquad->id}}" data-toggle="modal" data-target="#myModal" id = "probationerdata". {{$loop->iteration}} onclick="Myfunction(this);"><img src="/images/view.png" /><span></span></a>
                                        <a href="{{ route('squads.edit',$lSquad->id) }}"><img src="/images/edit.png" /><span></span></a>
                                        <a onclick = "deletesquad({{$lSquad->id}});"><img src="/images/trash.png" /></a></td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                  </div>

                  <div class="modal" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title">Probationer List</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                  <div class="table-responsive">
                 <table class="table"  id="probationerslist" name="probationerslist">
                  <thead>
                    <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Roll Number</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>

                  </tbody>
                 </table>
                </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

              </div>
            </div>
          </div>
                </div>

              </div>
</section>
@endsection
@section('scripts')

<script>
    $('#batch_id').change(function() {
        $('#squadlist tbody tr').remove();
        var id = $(this).val();
        $.ajax({
            url: '/getsquaddata',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id},


            success: function(data){
              var x=1;
              var y=1;
                $.each(data, function(i) {
                  var url = "{{ url ('/squads/view-probationers/:id')}}";
                  var route = "{{ route('squads.edit', ':id')}}";
                  var route_delete = "{{ url ('/squads/deletesquad/:id')}}";
                  url = url.replace(':id', data[i].id);
                  route = route.replace(':id', data[i].id);
                  route_delete = route_delete.replace(':id', data[i].id);
                    $('#squadlist #squadlistbody').append('<tr><td>' + x++ + '</td><td>' + data[i].SquadNumber + '</td><td>' + data[i].name + '</td><td><a href=' + url +' data-toggle="modal" data-target="#myModal" id = "probationerdata'+ y++ +'" onclick="Myfunction(this);"><img src="/images/view.png" /><span></span></a><a href='+ route +'><img src="/images/edit.png" /><span></span></a><a onclick = "deletesquad('+ data[i].id +');"><img src="/images/trash.png" /></a></td></tr>');
                });
            }
        });
    });
    function Myfunction(elem)
    {
      $('#probationerslist tbody tr').remove();
      var url = $(elem).attr('href');
       parts = url.split("/"),
       last_part = parts[parts.length-1];
      $.ajax({
            url: '/squads/view-probationers',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":last_part,
                },
                beforeSend: function (xhs) {
                        window.loadingScreen("show");
                  },
            success: function(data){
              window.loadingScreen("hide");
              if(data != '')
              {
              var x=1;
              $.each(data, function(i) {
                    $('#probationerslist tbody').append('<tr id = "id'+data[i].sid+'"><td>'+ x++ +'</td><td>' + data[i].Name + '</td><td>' + data[i].RollNumber + '</td></tr>');
                })
              }

            }
        })
    }

    function deleteProb(id)
    {
    var id = id;
      $.ajax({
            url: '/squads/delete',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id,
                },
            success: function(data){

            }
        })
    }

    function deletesquad(id)
    {
    var result = confirm("Do you want to delete this Squad?");
    if (result) {
    var id = id;
      $.ajax({
            url: '/squads/deletesquad/id',
            type: "POST",
            data:{
                "_token": "{{ csrf_token() }}",
                 "id":id,
                },
            success: function(data){
              window.location.reload();
            }
        })
    }
    }

  //   $('#probationerslist').DataTable({
  //   "bLengthChange": false,
  //   "ordering": false
  // });

</script>
@endsection

