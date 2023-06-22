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

    <section id="addstaff" class="content-wrapper_sub">
        <div class="user_manage">
            <div class="row">
                <div class="col-md-10">
                    <h4>Change password</h4>
                </div>
                <div class="col-md-2">
                    <div class="userBtns">
                        <!-- <a href="#" data-toggle="tooltip" title="import"> <img src="./images/import.png" /></a>
                        <a href="#" data-toggle="tooltip" title="excel"> <img style="width:29px !important"  src="./images/excel.png" /></a> -->
                    </div>
                </div>
            </div>
            <form class="userform" id="eStaff" action="/change-reset-password" method="POST" autocomplete="off">
                @csrf
                <div class="row">
                    <div class="col">
                        <label>Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="col">

                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col">

                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <span id='message'></span>
                    </div>
                    <div class="col">
                    </div>
                </div>
                <div class="usersubmitBtns mt-5">
                    <div class="mr-4">
                        <button class="btn formBtn submitBtn">Submit</button>
                    </div>
                </div>
            </form>

    </section>
@endsection

@section('scripts')
    <script>

        $('#password, #confirm_password').on('keyup', function () {
            if ($('#password').val() == $('#confirm_password').val()) {

                if ($('#current_password').val() == $('#password').val()) {
                    $('#message').html('Current password and old passwords should not be same').css('color', 'red');
                    $('.submitBtn').prop('disabled', true);
                }
                else if($('#password').val() != ''){
                    $('#message').html('Matched').css('color', 'green');
                    $('.submitBtn').prop('disabled', false);
                }

            } else
            {
                if($('#confirm_password').val() != ''){
                    $('#message').html('Not Matching').css('color', 'red');
                     $('.submitBtn').prop('disabled', true);
                }
            }

          });
    </script>
@endsection
