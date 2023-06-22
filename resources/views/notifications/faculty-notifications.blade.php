{{-- Extends layout --}}
@extends('layouts.faculty.template')

{{-- Content --}}
@section('content')

<section id="notifications" class="content-wrapper tab-content">

    <div class="row">
        <div class="col-md-12">
            <div class="notifications-wrapper">
                <div class="row mt-2 mb-3">
                    <div class="col-md-6">
                        <h4 class="notifications-heading">Notifications</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="text-right">
                            <button type="button" id="createNotificationBtn" data-toggle="modal" data-target="#createNotificationModal" class="btn btn-primary text-white"><i class="far fa-edit"></i> Create New</button>
                        </div>
                    </div>
                </div>

                <?php
                $user_id    = auth()->id();

                $Notifications  = App\Models\Notification::query()
                    ->whereRaw("(recipient_type IS NULL OR recipient_type IN ('', '0', 'faculty')) OR created_by = {$user_id}")
                    ->orderBy('id', 'desc')->get();
                ?>

                <div id="notification_list" class="notification-list">
                    @if(count($Notifications)>0)
                        @foreach ($Notifications as $Notification)
                            <?php
                                $notification_id     = $Notification->id;

                                $attachment     = $Notification->attachment;
                                $attachment_url = "";
                                if(!empty($attachment)) {
                                    $attachment_url = notification_attachment_url($attachment);
                                }

                                $createdAt  = $Notification->created_at;
                                $createdAt  = date('d F Y, H:i', strtotime($createdAt));

                                $created_by  = $Notification->created_by;
                                $createdBy   = '';
                                if(!empty($created_by) ) {
                                    if($created_by === $user_id) {
                                        $createdBy = 'Me';
                                    } else {
                                        $user       = \App\Models\User::where('id', $created_by)->select('name', 'role')->first();
                                        if(!empty($user)) {
                                            $createdBy = ucfirst($user->name);
                                        }
                                    }
                                }
                            ?>
                            <div class="notification-item read-notification" data-notification-id="{{ $notification_id }}">
                                <div class="notification-title-bar">
                                    <h5 class="notification-title">{{ $Notification->title }}</h5>
                                    <p class="notification-timestamp">{{ $createdAt }}</p>
                                </div>
                                <p class="notification-metadata">
                                    Created By: {{ $createdBy }}
                                    @if($created_by === $user_id)
                                    <span class="notofication-action-links">
                                        |
                                        <span class="edit-notofication-link text-primary">Edit</span>
                                        <span class="delete-notofication-link text-danger">Delete</span>
                                    </span>
                                    @endif
                                </p>
                                <div class="notification-message">{{ $Notification->message }}</div>
                                @if(!empty($attachment))
                                    <div class="notification-attachment">
                                        <a href="{{ $attachment_url }}" target="_blank" title="{{ $attachment }}">{{ $attachment }}</a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="notification-item">
                            <div class="msg msg-info msg-full text-left">
                                No notification found
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Create Notification Modal --}}
    <div class="modal fade" id="createNotificationModal" tabindex="-1" role="dialog" aria-labelledby="createNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-body">
                    <h4 class="">Create Notifications</h4>

                    <div id="createNotificationModalContent">
                        <form action="" id="createNotificationForm">
                            <div class="row mt-5">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>Recipients:</label>
                                        <select name="recipient_type" id="recipient_type" onchange="window.recipient_type_selected();" class="form-control">
                                            <option value="">All</option>
                                            <option value="drillinspector">Drill Inspector</option>
                                            <option value="doctor">Doctor</option>
                                            <option value="faculty">Faculty</option>
                                            <option value="probationer">Probationers</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-7 notification-select-batch-squad">
                                    <div>
                                        <div class="select-batch-container form-group">
                                            <label>Batch:</label>
                                            <select name="batch_id" id="batch_id" class="form-control">
                                                <option value="0">All</option>

                                                @php
                                                    $batches = DB::table('batches')->get();
                                                @endphp
                                                @if( !empty($batches) )
                                                    @foreach($batches as $batch)
                                                        <option value="{{ $batch->id }}" @if($batch->id == Session::get('current_batch')) selected @endif>{{ $batch->BatchName }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="select-squad-container form-group">
                                            <label for="sel1">Squad:</label>
                                            <select name="squad_id" id="squad_id" class="form-control">
                                                <option value="0">All</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Title:</label>
                                        <input type="text" name="title" id="notification_title" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label>Message:</label>
                                        <textarea name="message" id="notification_message" rows="5" class="form-control" required></textarea>
                                    </div>

                                    <div class="form-group">
                                        <input type="file" name="attachment" id="notification_attachment" class="form-control" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <div id="createNotification_status" class="my-2"></div>
                                    <button type="submit" class="btn formBtn submitBtn">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
