{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="createactivity" class="content-wrapper_sub activities-wrapper tab-content">
    <div class="user_manage">
        <div class="row">
            <div class="col-md-6">
                <h4>Staff List</h4>
            </div>
            <div class="col-md-6">

            </div>
        </div>

        <div id="squad_list" class="listdetails mt-5">
            <?php
            $Staffs = App\Models\User::where('role', '!=', 'probationer')->get();
            ?>

            <div class="squadlisthead">
                <div class="row">
                    <div class="col-md-6">
                        <div class="group">
                            <img src="/images/staff.png" />
                        </div>
                    </div>

                </div>
            </div>
            <div>
                <table class="table" id="stafflist" style="width: 100% !important">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-left">Email</th>
                            <th>Date Of Birth</th>
                            <th>Mobile number</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($Staffs) > 0)

                            @foreach($Staffs as $Staff)
                            <tr>
                                <td class="text-left">{{ $Staff->name }}</td>
                                <td class="text-left">{{ $Staff->email }}</td>
                                <td>{{date('d-m-Y', strtotime($Staff->Dob))}}</td>
                                <td>{{ $Staff->MobileNumber }}</td>
                                <td>{{ $Staff->role }}</td>
                            </tr>
                            @endforeach

                        @else
                        {{ 'No staff found' }}
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
    <script>
        $('#stafflist').DataTable({
            "bLengthChange": false,
            language: { search: "", searchPlaceholder: "Search..." },
        });
    </script>
@endsection
