"use strict";

(function ($) {
    /* ---------------------- On select Recipient Type --------------- */
    if (!window.recipient_type_selected) {
        window.recipient_type_selected = function () {
            var recipientType = $("#recipient_type").val();
            if (recipientType == "probationer") {
                $("#createNotificationForm .select-batch-container").show();
            } else {
                $("#createNotificationForm .select-batch-container").hide();
                $("#createNotificationForm .select-squad-container").hide();

                $("#batch_id").val(0);
                $("#squad_id").html('<option value="0">All</option>');
            }
        };
    }

    /** --------------------------------------------------------------
     * Get New notifications count
     * ------------------------------------------------------------ */
    if (!window.get_new_notifications_count) {
        window.get_new_notifications_count = function () {
            $.ajax({
                url: appUrl + "/notifications/ajax",
                data: {
                    requestName: "get_new_notifications_count"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    //
                },
                success: function (rData) {
                    let rObj = JSON.parse(rData);
                    if (rObj.status == "success") {
                        $(".topbar-notification-links .notification-count").html(rObj.count);
                    } else {
                        console.log("get_new_notifications_count error: "+ rObj.message);
                    }
                }
            });
        };
    }

    $(function () {
        /* ---------------- Get New notifications count on topbar icon ----------- */
        window.get_new_notifications_count();

        /* ------------------ Notification Mark read on click ---------------- */
        $(document).on("click", "#notification_list .notification-item.unread-notification", function(){
            var nfId    = $(this).attr("data-nf-id");
            $.ajax({
                url: appUrl + "/notifications/ajax",
                data: {
                    notification_id: nfId,
                    requestName: "notifications_mark_read"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    // window.loadingScreen("show");
                },
                success: function (rData) {
                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        $("#notification_list").load(
                            location.href + " #notification_list>*"
                        );
                        window.get_new_notifications_count();
                    } else {
                        console.log("notifications_mark_read error: "+ rObj.message);
                    }
                    // window.loadingScreen("hide");
                }
            });
        });

        /* ---------------- Get squads on batch select ----------- */
        $(document).on("change", "#createNotificationForm #batch_id", function () {
            var batchId = $(this).val();

            if (batchId > 0) {

                // Display Squad select options
                $("#createNotificationForm .select-squad-container").show();

                // Get Squad dropdown options for the batch
                var data = {
                    batch_id: batchId,
                    requestName: "get_squads",
                };

                var actionUrl = appUrl + "/notifications/ajax";

                $.ajax({
                    url: actionUrl,
                    data: data,
                    type: "POST",
                    // processData: false,
                    // contentType: false,
                    beforeSend: function (xhr) {
                        // window.loadingScreen("show");

                        $("#squad_id").html(
                            '<option value="">Please wait...</option>'
                        );
                    },
                    success: function (rData) {
                        // window.loadingScreen("hide");

                        $("#squad_id").html(rData);
                    }
                });
            } else {
                $("#createNotificationForm .select-squad-container").hide();
                $("#squad_id").html(
                    '<option value="0">All</option>'
                );
            }
        });

        /* ---------------------- Create Notification Submit --------------- */
        $(document).on("submit", "#createNotificationForm", function (e) {
            e.preventDefault();

            var statusDiv = $("#createNotification_status");
            var form = $(this);

            var FrmData = new FormData(form[0]);

            $.ajax({
                url: appUrl + "/notifications",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html(
                        '<p class="text-info">Please wait while processing your request</p>'
                    );
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        form[0].reset();

                        statusDiv.html(
                            '<div class="msg msg-success msg-full text-left">' + rObj.message + "</div>"
                        );

                        $("#notification_list").load(
                            location.href + " #notification_list>*"
                        );

                        setTimeout(function () {
                            statusDiv.html('');
                            $("#createNotificationModal").modal("hide");
                        }, 1000);
                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full text-left">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        });


        /* ************ Get Notification Edit *************** */
        $(document).on("click", ".notification-metadata .edit-notofication-link", function(){
            var nfId    = $(this).closest('.notification-item').attr("data-notification-id");

            $.ajax({
                url: appUrl + "/notifications/ajax",
                data: {
                    notification_id: nfId,
                    requestName: 'get_notification_edit',
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    $("#successModalContent").html('');
                    $("#successModalBtns .modal-btn-primary").html('');
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $("#successModalContent").html(rData);
                    $("#successModalBtns .modal-btn-primary").html(`
                        <button type="button" id="notificationEdit_submit" class="btn btn-primary">Save</button>
                    `);
                    $("#successModal").modal("show");
                }
            });
        });

        /* ---------------------- Edit Notification Submit --------------- */
        $(document).on("click", "#notificationEdit_submit", function (e) {
            e.preventDefault();

            var statusDiv = $("#editNotification_status");
            var formEL  = $("#editNotificationForm");
            var nfId    = formEL.attr('data-notification-id');

            var FrmData = new FormData(formEL[0]);
            FrmData.append('notification_id', nfId);

            $.ajax({
                url: appUrl + "/notification/update",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html(
                        '<p class="text-info">Please wait while processing your request</p>'
                    );
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        statusDiv.html(
                            '<div class="msg msg-success msg-full text-left">' + rObj.message + "</div>"
                        );

                        $("#notification_list").load(
                            location.href + " #notification_list>*"
                        );

                        setTimeout(function () {

                            $("#successModal").modal("hide");
                            $("#successModalContent").html('');
                            $("#successModalBtns .modal-btn-primary").html('');
                        }, 800);
                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full text-left">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        });

        $(document).on('click', '#editNotificationForm .change-attachment-icon', function(e) {
            e.preventDefault();

            var attachDiv   = $(this).closest('div');
            attachDiv.slideUp();
            attachDiv.siblings('div').slideDown();
        });

        /* ************ Get Notification Delete *************** */
        $(document).on("click", ".notification-metadata .delete-notofication-link", function(){
            var nfId    = $(this).closest('.notification-item').attr("data-notification-id");

            $.ajax({
                url: appUrl + "/notifications/ajax",
                data: {
                    notification_id: nfId,
                    requestName: 'get_notification_delete',
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    $("#successModalContent").html('');
                    $("#successModalBtns .modal-btn-primary").html('');
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $("#successModalContent").html(rData);
                    $("#successModalBtns .modal-btn-primary").html(`
                        <button type="button" id="notificationDelete_submit" class="btn btn-danger">Delete</button>
                    `);
                    $("#successModal").modal("show");
                }
            });
        });

        /* ---------------------- Delete Notification Submit --------------- */
        $(document).on("click", "#notificationDelete_submit", function (e) {
            e.preventDefault();

            var statusDiv = $("#deleteNotification_status");
            var formEL  = $("#deleteNotificationForm");
            var nfId    = formEL.attr('data-notification-id');

            var FrmData = new FormData(formEL[0]);
            FrmData.append('notification_id', nfId);
            FrmData.append('requestName', 'notification_delete');

            $.ajax({
                url: appUrl + "/notifications/ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html(
                        '<p class="text-info">Please wait while processing your request</p>'
                    );
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        statusDiv.html(
                            '<div class="msg msg-success msg-full text-left">' + rObj.message + "</div>"
                        );

                        $("#notification_list").load(
                            location.href + " #notification_list>*"
                        );

                        setTimeout(function () {

                            $("#successModal").modal("hide");
                            $("#successModalContent").html('');
                            $("#successModalBtns .modal-btn-primary").html('');
                        }, 800);
                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full text-left">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        });
    });
})(jQuery);
