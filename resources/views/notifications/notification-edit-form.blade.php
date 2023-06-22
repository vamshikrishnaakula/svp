<?php
$recipient_type = $notification->recipient_type;
$batch_id       = $notification->batch_id;
$squad_id       = $notification->squad_id;
$title      = $notification->title;
$message    = $notification->message;
$attachment = $notification->attachment;

$recipient  = empty($recipient_type)? 'All' : $recipient_type;
$recipient  = ucfirst($recipient);
if($recipient_type === 'probationer') {
    $batch  = empty($batch_id)? 'All' : batch_name($batch_id);
    $recipient  .= " > {$batch}";

    if( !empty($batch_id) ) {
        $squad  = empty($squad_id)? 'All' : squad_number($squad_id);
        $recipient  .= " > {$squad}";
    }
}

$attachment_url = "";
if(!empty($attachment)) {
    $attachment_url = notification_attachment_url($attachment);
}
?>
<form action="" id="editNotificationForm" data-notification-id="{{ $notification->id }}">
    <div class="form-group">
        <p>Recipients: {{ $recipient }}</p>
    </div>

    <div class="form-group">
        <label>Title:</label>
        <input type="text" name="title" id="notification_title" value="{{ $title }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Message:</label>
        <textarea name="message" id="notification_message" rows="5" class="form-control" required>{{ $message }}</textarea>
    </div>

    <div class="form-group">
        @if(!empty($attachment))
            <div class="notification-attachment">
                <div>
                    Attachment: <a href="{{ $attachment_url }}" target="_blank" title="{{ $attachment }}">{{ $attachment }}</a>
                    <a href="#" data-toggle="tooltip" title="Change" class="ml-2 change-attachment-icon"> <img src="{{ asset('/images/edit.png') }}" style="max-width:25px;"></a>
                </div>
                <div style="display:none;">
                    <input type="file" name="attachment" id="notification_attachment" class="form-control" />
                </div>
            </div>
        @endif
    </div>

    <div id="editNotification_status" class="my-2"></div>
</form>
