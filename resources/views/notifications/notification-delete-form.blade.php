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
<form action="" id="deleteNotificationForm" data-notification-id="{{ $notification->id }}">
    <div class="form-group">
        <p>Recipients: {{ $recipient }}</p>
    </div>

    <div class="form-group">
        <label>Title:</label>
        <input type="text" name="title" id="notification_title" value="{{ $title }}" class="form-control" disabled>
    </div>

    <div class="form-group">
        <label>Message:</label>
        <textarea name="message" id="notification_message" rows="5" class="form-control" disabled>{{ $message }}</textarea>
    </div>

    <div class="form-group">
        @if(!empty($attachment))
            <div class="notification-attachment">
                Attachment: <a href="{{ $attachment_url }}" target="_blank" title="{{ $attachment }}">{{ $attachment }}</a>
            </div>
        @endif
    </div>

    <div id="deleteNotification_status" class="my-2"></div>
</form>
