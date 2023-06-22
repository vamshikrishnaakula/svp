"use strict";

(function ($) {


    /** -------------------------------------------------------------------
     * Get notes
     * ----------------------------------------------------------------- */
    $(document).on("click", "#get_notes_btn", function() {
        window.get_notes_Submit();
    });

    if (!window.get_notes_Submit) {
        window.get_notes_Submit = function() {
            var formEl = $("form#get_notes_form");
            var statusDiv = $("#get_notes_status");

            $("#notes-container").html("");

            formEl.find(".form-control").each(function() {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".reqField").each(function() {
                if ($(this).val().trim() == "") {
                    $(this).addClass("input-error");

                    isValid = false;
                    // return isValid;
                    if (ErrorCount == 0) {
                        firstError = $(this);
                    }

                    ErrorCount++;
                } else {
                    $(this).removeClass("input-error");
                }
            });

            if (isValid == true) {
                var FrmData = new FormData(formEl[0]);

                FrmData.append('requestName', 'get_notes');

                $.ajax({
                    url: appUrl + "/notes/ajax",
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) {
                        window.loadingScreen("show");
                        statusDiv.html(`Please wait...`);
                    },
                    success: function(rData) {
                        window.loadingScreen("hide");
                        statusDiv.html("");
                        $("#notes-container").html(rData);
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Get Create Note Modal
     * ----------------------------------------------------------------- */
    $(document).on("click", "#createNoteBtn", function() {
        var reference   = $(this).attr("data-reference");
        var referenceId = $(this).attr("data-reference-id");

        $.ajax({
            url: appUrl + "/notes/ajax",
            data: {
                reference: reference,
                referenceId: referenceId,
                requestName: "get_createNoteForm"
            },
            type: "POST",
            beforeSend: function(xhr) {
                window.loadingScreen("show");
            },
            success: function(rData) {
                window.loadingScreen("hide");
                $("#createNoteModalContent").html(rData);
                $("#createNoteModal").modal("show");
            }
        });
    });

    /** -------------------------------------------------------------------
     * Get notes
     * ----------------------------------------------------------------- */
     $(document).on("click", "#createNoteSubmit", function() {
        var formEl = $("form#createNoteForm");
        var statusDiv = $("#createNote_status");

        formEl.find(".form-control").each(function() {
            $(this).removeClass("input-error");
        });

        var isValid = true;
        var firstError = "";
        var ErrorCount = 0;

        formEl.find(".reqField").each(function() {
            if ($(this).val().trim() == "") {
                $(this).addClass("input-error");

                isValid = false;
                // return isValid;
                if (ErrorCount == 0) {
                    firstError = $(this);
                }

                ErrorCount++;
            } else {
                $(this).removeClass("input-error");
            }
        });

        if (isValid == true) {
            var FrmData = new FormData(formEl[0]);

            $.ajax({
                url: appUrl + "/notes",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    window.loadingScreen("show");
                    statusDiv.html(`Please wait...`);
                },
                success: function(rData) {
                    window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        formEl[0].reset();

                        statusDiv.html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                        window.get_notes_Submit();

                        setTimeout(function(){
                            $("#createNoteModalContent").html("");
                            $("#createNoteModal").modal("hide");
                        }, 500)

                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        } else {
            firstError.focus();
            statusDiv.html('<div class="msg msg-danger">Fill all the required fields</div>');
        }
    });

    /** -------------------------------------------------------------------
     * Get Edit Note Modal
     * ----------------------------------------------------------------- */
     $(document).on("click", "span.edit-note-link", function() {
        var noteId   = $(this).closest('.note-item').attr("data-note-id");

        $.ajax({
            url: appUrl + "/notes/ajax",
            data: {
                note_id: noteId,
                requestName: "get_editNoteForm"
            },
            type: "POST",
            beforeSend: function(xhr) {
                window.loadingScreen("show");
            },
            success: function(rData) {
                window.loadingScreen("hide");

                $("#successModalContent").html(rData);
                $("#successModalBtns").hide();
                $("#successModal").modal("show");
            }
        });
    });


    /** -------------------------------------------------------------------
     * Edit note submit
     * ----------------------------------------------------------------- */
     $(document).on("click", "#editNoteSubmit", function() {
        var formEl = $("form#editNoteForm");
        var statusDiv = $("#editNote_status");

        formEl.find(".form-control").each(function() {
            $(this).removeClass("input-error");
        });

        var isValid = true;
        var firstError = "";
        var ErrorCount = 0;

        formEl.find(".reqField").each(function() {
            if ($(this).val().trim() == "") {
                $(this).addClass("input-error");

                isValid = false;
                // return isValid;
                if (ErrorCount == 0) {
                    firstError = $(this);
                }

                ErrorCount++;
            } else {
                $(this).removeClass("input-error");
            }
        });

        if (isValid == true) {
            var FrmData = new FormData(formEl[0]);
            var note_id = formEl.find("#edit_note_id").val();

            $.ajax({
                url: appUrl + "/notes/"+note_id,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    // window.loadingScreen("show");
                    statusDiv.html(`Please wait...`);
                },
                success: function(rData) {
                    // window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        formEl[0].reset();

                        statusDiv.html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                        window.get_notes_Submit();

                        setTimeout(function(){
                            $("#successModalContent").html("");
                            $("#successModal").modal("hide");
                        }, 500)

                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        } else {
            firstError.focus();
            statusDiv.html('<div class="msg msg-danger">Fill all the required fields</div>');
        }
    });

    /** -------------------------------------------------------------------
     * Get Delete Note Modal
     * ----------------------------------------------------------------- */
     $(document).on("click", "span.delete-note-link", function() {
        var noteId   = $(this).closest('.note-item').attr("data-note-id");

        $.ajax({
            url: appUrl + "/notes/ajax",
            data: {
                note_id: noteId,
                requestName: "get_deleteNoteForm"
            },
            type: "POST",
            beforeSend: function(xhr) {
                window.loadingScreen("show");
            },
            success: function(rData) {
                window.loadingScreen("hide");

                $("#successModalContent").html(rData);
                $("#successModalBtns").hide();
                $("#successModal").modal("show");
            }
        });
    });

    /** -------------------------------------------------------------------
     * Delete note submit
     * ----------------------------------------------------------------- */
     $(document).on("click", "#deleteNoteSubmit", function() {
        var formEl = $("form#deleteNoteForm");
        var statusDiv = $("#deleteNote_status");

        var FrmData = new FormData(formEl[0]);
        var note_id = formEl.find("#delete_note_id").val();

        $.ajax({
            url: appUrl + "/notes/"+note_id,
            data: FrmData,
            type: "POST",
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                // window.loadingScreen("show");
                statusDiv.html(`Please wait...`);
            },
            success: function(rData) {
                // window.loadingScreen("hide");

                let rObj = JSON.parse(rData);

                if (rObj.status == "success") {
                    formEl[0].reset();

                    statusDiv.html(
                        '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                    );
                    window.get_notes_Submit();

                    setTimeout(function(){
                        $("#successModalContent").html("");
                        $("#successModal").modal("hide");
                    }, 500)

                } else {
                    statusDiv.html(
                        '<div class="msg msg-danger msg-full">' + rObj.message + "</div>"
                    );
                }
            }
        });

    });
})(jQuery);
