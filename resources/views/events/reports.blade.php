{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

<script>

</script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>

    <section id="event" class="content-wrapper_sub">
        <div class="row">
            <div class="col-md-10">
                <h4>Reports</h4>
    
            </div>
            <div class="col-md-2 text-right">
                
            </div>
        </div> 
        <div class="bg-white px-3 py-3 report__sec">
        <div class="row">
            <div class="col-md-12">
                <h6>Individual Events</h6>
                <div class="ml-5 my-5">
                    <a href="#" class="mr-4">Winner List</a>
                    <a href="#">Disqualifiers List</a>
                </div>
            </div>
        </div>
          <div class="row">
            <div class="col-md-12">
                <h6>Team Events</h6>
                <div class="ml-5 my-5">
                    <a href="#" class="mr-4">Winner List</a>
                    <a href="#">Disqualifiers List</a>
                </div>
            </div>
        </div>
    </div>
    </section>
    
@endsection

