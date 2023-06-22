{{-- Extends layout --}}
@extends('layouts.pbdash.template')

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

                    </div>
                </div>

                <?php
                $userData = auth()->user();
                $pb_data   = App\Models\probationer::where('user_id', $userData->id)->first();
                $squad_id = (is_null($pb_data->squad_id)) ? '0' : $pb_data->squad_id;

                $Notifications  = [];
                if(!empty($pb_data)) {
                    $Notifications  = App\Models\Notification::whereRaw("
                            (recipient_type IS NULL OR recipient_type IN ('', '0', 'probationer'))
                            AND
                            (batch_id IS NULL OR batch_id IN ('', '0', $pb_data->batch_id))
                            AND
                            (squad_id IS NULL OR squad_id IN ('', '0', $squad_id))
                        ")
                        ->orderBy('id', 'desc')->get();
                }
                ?>

                <div id="notification_list" class="notification-list">
                    @if(count($Notifications)>0)
                        @foreach ($Notifications as $Notification)
                            <?php
                                $nf_id      = $Notification->id;

                                $attachment     = $Notification->attachment;
                                $attachment_url = "";
                                if(!empty($attachment)) {
                                    $attachment_url = notification_attachment_url($attachment);
                                }

                                $createdAt  = $Notification->created_at;
                                $createdAt  = date('d F Y, H:i', strtotime($createdAt));

                                $read_status   = App\Models\NotificationReadStatus::where('notification_id', $nf_id)
                                    ->where('user_id', $userData->id)->value('read_status');

                                $read_class = ($read_status === 1) ? "read-notification" : "unread-notification";
                            ?>
                            <div class="notification-item {{ $read_class }}" data-nf-id="{{ $nf_id }}">
                                <div class="notification-title-bar">
                                    <h5 class="notification-title">{{ $Notification->title }}</h5>
                                    <p class="notification-timestamp">{{ $createdAt }}</p>
                                </div>
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
                                </div>
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
