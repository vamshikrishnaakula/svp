{{-- Extends layout --}}
@extends('layouts.default')

{{-- Content --}}
@section('content')

@php
  $i = 1;
@endphp

@if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
  @elseif ($message = Session::get('delete'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>

    @endif

<section id="createsquad" class="content-wrapper_sub">
      <div class="user_manage">
        <div class="row">
        <div class="col-md-10">
              <h4>Edit Squad</h4>
            </div>
            <div class="col-md-2">
                <div class="userBtns">
                    <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="{{ asset('images/import.png') }}" /></a>
                    <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="{{ asset('images/excel.png') }}" /></a> -->
                </div>
            </div>
      </div>
      <form action="{{ route('squads.update', $squad->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="row mt-5">
            <div class="col-sm-4">
                    <label>Select Batch</label>
                            <select class="form-control" id="Batch_Id" name='Batch_Id' required>
                                <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                            </select>
                </div>

                <div class="col-sm-4">
                    <label for="squadno">Squad Number</label>
                    <input type="text" class="form-control" id="SquadNumber" name='SquadNumber' value = "{{ $squad->SquadNumber }}" required>
                </div>
                <div class="col-sm-4">
                            <label>Drill Inspector</label>
                            <select class="form-control" id="DrillInspector_Id" name='DrillInspector_Id' required>
                            <option value="">Select drill Inspector</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}" {{$squad->DrillInspector_Id == $staff->id  ? 'selected' : ''}}>{{ $staff->name }}</option>
                             @endforeach
                              </select>
                </div>
            </div>


          <div class="row mt-5">
              <div class="col-md-5">
                <div class="probationerlist">
                    <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Probationers List</h6>
                        </div>
                         <div class="col-md-7">
                              <div class="form-group has-search">
                                <span class="fa fa-search form-control-feedback"></span>
                                <input type="text" id="search" name='search' class="form-control" placeholder="Search">
                            </div>
                        </div>
                    </div>
                </div>
                    <table id="prob_list"  class="createsquadproblist"  style="margin-top: -1px !important">
                        <thead>
                                <tr>
                                    <th width="10%"></th>
                                    <th>Batch No</th>
                                    <th>Roll No</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                        </thead>
                            <tbody>
                            @foreach($probationers as $probationer)
                                <tr>
                                    <td><input type=checkbox class=form-control name = check-tab1 /></td>
                                    <td>{{ $probationer->BatchName }}</td>
                                    <td>{{ $probationer->RollNumber }}</td>
                                    <td>{{ $probationer->Name }}</td>
                                    <td style="display:none;">{{ $probationer->id }}</td>
                                </tr>

                                @endforeach
                            </tbody>
                    </table>
                </div>
              </div>
            <div class="swapBtn col-md-2">
                <button type="button" class="btn btn-secondary" onclick="tab2_To_tab1();">
                    <i class="fas fa-angle-left"></i>
                </button>
                <button type="button" class="btn btn-secondary" onclick="tab1_To_tab2();">
                    <i class="fas fa-angle-right"></i>
                </button>
            </div>
              <div class="col-md-5">
                <div class="probationerlist">
                    <div class="squadbg">
                    <div class="row">
                        <div class="col-md-5">
                            <h6>Added Members</h6>
                        </div>
                        <!-- <div class="col-md-7">
                            <input type="search" class="form-control" placeholder="search" />
                        </div> -->
                    </div>
                </div>
                    <table id="Addedprob" class="createsquadaddedlist table-sortable has-position_number" name="Addedprob">
                    {{ csrf_field() }}
                        <thead>
                                <tr>
                                    <th></th>
                                    <th>Batch No</th>
                                    <th>Roll No</th>
                                    <th colspan="2">Name</th>
                                </tr>
                        </thead>
                        <tbody>
                            @foreach($assignedProbationers as $AssignedPb)
                                <tr data-position="{{ $AssignedPb->position_number }}">
                                    <td><input type='checkbox' class='form-control' name='check-tab2'></td>
                                    <td>{{ $AssignedPb->BatchName }}</td>
                                    <td>{{ $AssignedPb->RollNumber }}</td>
                                    <!-- <td>{{ $AssignedPb->Cadre }}</td> -->
                                    <td>{{ $AssignedPb->Name }}</td>
                                    <td>{{ $AssignedPb->id }}</td>
                                    <td>
                                        <input type="hidden" value="{{ $AssignedPb->id }}" name="pid[]" />
                                        <input type="hidden" name="position_number[{{ $AssignedPb->id }}]" value='{{ $AssignedPb->position_number }}' class="pb_position_number" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

              </div>
          </div>
</div>

          <div class="usersubmitBtns mt-5">
            <div class="mr-4">
                <button type="submit" class="btn formBtn submitBtn">Submit</button>
            </div>
            <!-- <div>
                <button type="button" class="btn formBtn cancelBtn">Cancel</button>
            </div> -->
        </div>
        </form>
</section>
@endsection

@section('scripts')
<script>

    function tab1_To_tab2() {


        var table1 = document.getElementById("prob_list"),
            table2 = document.getElementById("Addedprob"),
            checkboxes = document.getElementsByName("check-tab1");
        var rowCount = $('#Addedprob tbody tr').length;
        if (rowCount == '0') {
            $('#Addedprob tbody').append('<tr></tr>');
        }

        var rowCount1 = $('#Addedprob tbody tr').length;

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                var pb_id = table1.rows[i + 1].cells[4].innerHTML;

                // create new row and cells
                var newRow = table2.insertRow(table2.length),
                    cell1 = newRow.insertCell(0),
                    cell2 = newRow.insertCell(1),
                    cell3 = newRow.insertCell(2),
                    cell4 = newRow.insertCell(3);
                    cell5 = newRow.insertCell(4);
                    cell6 = newRow.insertCell(5);
                // console.log(newRow);
                // add values to the cells
                cell1.innerHTML = "<input type='checkbox' class='form-control' name='check-tab2'>";
                cell2.innerHTML = table1.rows[i + 1].cells[1].innerHTML;
                cell3.innerHTML = table1.rows[i + 1].cells[2].innerHTML;
                cell4.innerHTML = table1.rows[i + 1].cells[3].innerHTML;
                cell5.innerHTML = table1.rows[i + 1].cells[4].innerHTML;
                cell6.innerHTML = `
                    <input type="hidden" value="${pb_id}" name=pid[] />
                    <input type="hidden" name="position_number[${pb_id}]" value="" class="pb_position_number" />
                `;



                // remove the transfered rows from the first table [table1]
                var index = table1.rows[i + 1].rowIndex;
                table1.deleteRow(index);
                // we have deleted some rows so the checkboxes.length have changed
                // so we have to decrement the value of i
                i--;
                //  console.log(checkboxes.length);
            }
        }
        $("tr:empty").remove();

        // Regenerate position number
        window.generatePositionNumber(table2.find('tbody'));
    }


    function tab2_To_tab1() {

        var table1 = document.getElementById("prob_list"),
            table2 = document.getElementById("Addedprob"),
            checkboxes = document.getElementsByName("check-tab2");
        var rowCount = $('#prob_list tbody tr').length;
        if (rowCount == '0') {
            $('#prob_list tbody').append('<tr></tr>');
        }
        for (var i = 0; i < checkboxes.length; i++)
            if (checkboxes[i].checked) {
                // create new row and cells
                var newRow = table1.insertRow(table1.length),
                    cell1 = newRow.insertCell(0),
                    cell2 = newRow.insertCell(1),
                    cell3 = newRow.insertCell(2),
                    cell4 = newRow.insertCell(3);
                cell5 = newRow.insertCell(4);

                // add values to the cells
                cell1.innerHTML = "<input type='checkbox' class='form-control' name='check-tab1'>";
                cell2.innerHTML = table2.rows[i + 1].cells[1].innerHTML;
                cell3.innerHTML = table2.rows[i + 1].cells[2].innerHTML;
                cell4.innerHTML = table2.rows[i + 1].cells[3].innerHTML;
                cell5.innerHTML = table2.rows[i + 1].cells[4].innerHTML;


                // remove the transfered rows from the second table [table2]
                var index = table2.rows[i + 1].rowIndex;
                table2.deleteRow(index);
                // we have deleted some rows so the checkboxes.length have changed
                // so we have to decrement the value of i
                i--;
            }
        $("tr:empty").remove();
    }

    $("#search").keyup(function () {
        var value = this.value.toLowerCase().trim();

        $("#prob_list tr").each(function (index) {
            if (!index) return;
            $(this).find("td").each(function () {
                var id = $(this).text().toLowerCase().trim();
                var not_found = (id.indexOf(value) == -1);
                $(this).closest('tr').toggle(!not_found);
                return not_found;
            });
        });
    });
</script>

@endsection
