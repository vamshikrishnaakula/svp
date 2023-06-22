"use strict";


(function($) {

    /** -------------------------------------------------------------------
     * Get Extra Session Form
     * ----------------------------------------------------------------- */
    if (!window.get_extraclass_form) {
        window.get_extraclass_form = function() {
            var statusDiv = $("#get_create_extraclass_status");
            var container = $("#extraclass_form_container");
            var extraSessionForm = $("form#get_create_extraclass_form");

            extraSessionForm.find(".form-control").each(function() {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            extraSessionForm.find(".reqField").each(function() {
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
                var FrmData = new FormData(extraSessionForm[0]);

                // console.log(probationer_ids);

                FrmData.append('requestName', 'get_create_extraClass_probationers');

                $.ajax({
                    url: appUrl + "/extraclasses/ajax",
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function(xhr) {
                        window.loadingScreen("show");
                        statusDiv.html('<div class="msg msg-info rl-margin-auto">Please wait...</div>');
                    },
                    success: function(rData) {
                        window.loadingScreen("hide");
                        statusDiv.html('');

                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            container.html(rObj.data);

                            $("#get_create_extraclass_form").slideUp();
                            window.getDatepicker();
                        } else {
                            container.html(
                                '<div class="msg msg-danger">' + rObj.message + "</div>"
                            );
                        }
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger rl-margin-auto">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Create Extra Session Form Submit
     * ----------------------------------------------------------------- */
    if (!window.create_extra_class) {
        window.create_extra_class = function() {
            var statusDiv = $("#create_extraclass_status");
            var extraSessionForm = $("form#create_extraclass_form");

            extraSessionForm.find(".form-control").each(function() {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            extraSessionForm.find(".reqField").each(function() {
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
                var FrmData = new FormData(extraSessionForm[0]);

                // Probationers ids
                var probationer_ids = new Array();
                $("#extrasession_addedprob_list input[name=extra_session_pb_cb]").each(function() {
                    probationer_ids.push($(this).val());
                });

                // console.log(probationer_ids);

                FrmData.append('probationer_ids', probationer_ids);
                FrmData.append('requestName', 'create_extraclass');

                $.ajax({
                    url: appUrl + "/extraclasses/ajax",
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
                        // statusDiv.html(rData);

                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            extraSessionForm[0].reset();
                            $("form#get_create_extraclass_form")[0].reset();

                            // $("#extrasession_addedprob_list tbody").find('tr:not(.no-data-tr)').remove();
                            // $("#extrasession_addedprob_list tbody").find('tr.no-data-tr').show();

                            $("#extraclass_form_container").html('<div class="msg msg-success mx-auto" style="width:75%;">' + rObj.message + '</div>');
                            $("#get_create_extraclass_form").slideDown();
                        } else {
                            statusDiv.html(
                                '<div class="msg msg-danger">' + rObj.message + "</div>"
                            );
                        }
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Get SubActivities Dropdown
     * ----------------------------------------------------------------- */
    if (!window.get_extraClasses) {
        window.get_extraClasses = function() {
            var statusDiv = $("#get_extraclass_status");
            var extraSessionForm = $("form#get_extraclass_form");

            var FrmData = new FormData(extraSessionForm[0]);
            FrmData.append('requestName', 'get_extraclasses');

            $.ajax({
                url: appUrl + "/extraclasses/ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    window.loadingScreen("show");
                    statusDiv.html(`<option value="">Please wait...</option>`);
                },
                success: function(rData) {
                    window.loadingScreen("hide");
                    statusDiv.html('');

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        // extraSessionForm[0].reset();
                        $("#extra_classes_table tbody").html(rObj.data);
                        $("#extra_classes_pagination").html('');
                        $("#session_list_title").html('Class list');
                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Get Extra Session Meta
     * ----------------------------------------------------------------- */
    if (!window.get_extraClassMetas) {
        window.get_extraClassMetas = function(sessionId) {
            var data = {
                sessionId: sessionId,
                requestName: "get_extra_class_metas",
            };

            $.ajax({
                url: appUrl + "/extraclasses/ajax",
                data: data,
                type: "POST",
                beforeSend: function(xhr) {
                    window.loadingScreen("show");
                },
                success: function(rData) {
                    window.loadingScreen("hide");

                    $("#successModalContent").html(rData);
                    $("#successModalBtns .modal-btn-primary").html('<button id="print_extra_class_btn" class="btn btn-info me-2">Print</button>');
                    $("#successModal").modal("show");
                    $("#successModal .modal-dialog").addClass("modal-lg");
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Get Extra Class Edit
     * ----------------------------------------------------------------- */
    if (!window.get_editExtraClass) {
        window.get_editExtraClass = function(classId) {
            var data = {
                class_id: classId,
                requestName: "get_extra_class_edit",
            };

            $.ajax({
                url: appUrl + "/extraclasses/ajax",
                data: data,
                type: "POST",
                beforeSend: function(xhr) {
                    window.loadingScreen("show");
                },
                success: function(rData) {
                    window.loadingScreen("hide");

                    $("#successModalContent").html(rData);
                    $("#successModalBtns .modal-btn-primary").html(`
                        <button type="button" class="btn btn-primary" id="editExtraClassSubmit" onclick="window.editExtraClass();">Save</button>
                    `);
                    $("#successModal").modal("show");

                    window.getDatepicker();
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Extra Class Submit
     * ----------------------------------------------------------------- */
    if (!window.editExtraClass) {
        window.editExtraClass = function() {

            var statusDiv = $("#editExtraClass_status");
            var formEL = $("#editExtraClassForm");
            var classId = formEL.attr('data-class-id');

            var FrmData = new FormData(formEL[0]);
            FrmData.append('class_id', classId);
            FrmData.append('requestName', 'editExtraClass_submit');

            $.ajax({
                url: appUrl + "/extraclasses/ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    window.loadingScreen("show");
                },
                success: function(rData) {
                    window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        statusDiv.html(
                            '<div class="msg msg-success msg-full text-left">' + rObj.message + "</div>"
                        );

                        $("#extra_classes_table").load(
                            location.href + " #extra_classes_table>*"
                        );

                        setTimeout(function() {

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
        };
    }


    /** -------------------------------------------------------------------
     * Download Extara Session probationer's Datasheet
     * ----------------------------------------------------------------- */
    if (!window.download_extraClassData) {
        window.download_extraClassData = function (sessionId) {

            $.ajax({
                url: appUrl + '/extraclasses/ajax',
                data: {
                    session_id: sessionId,
                    requestName: "download_extraClassData"
                },
                type: "POST",
                // processData: false,
                // contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        let newTab = window.open(rObj.datasheet_url);
                    } else {
                        $("#successModalContent").html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");
                    }
                }
            });
        }
    };

    /** -------------------------------------------------------------------
     * Get Extra Session Import Modal
     * ----------------------------------------------------------------- */
     if (!window.get_extraClassImport_modal) {
        window.get_extraClassImport_modal = function () {

            $.ajax({
                url: appUrl +'/extraclasses/ajax',
                data: {
                    requestName: "get_extraClassImport_modal"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#dataImportModalTitle").html("Import Session Data");
                    $("#dataImportModalContent").html(rData);
                    $("#dataImportModalBtns").hide();
                    $("#dataImportModal").modal("show");

                    window.getDatepicker();
                }
            });
        }
    };

    /** -------------------------------------------------------------------
     * Import Extra Session Form Submit
     * ----------------------------------------------------------------- */
     if (!window.importExtraClassData_submit) {
        window.importExtraClassData_submit = function() {
            var statusDiv = $("#importExtraClassData_status");

            var formEl = $("form#importExtraClassData_form");
            var FrmData = new FormData(formEl[0]);

            FrmData.append('requestName', 'import_extraClass_data');

            var actionUrl = appUrl + "/extraclasses/ajax";

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function(rData) {
                    window.loadingScreen("hide");
                    formEl[0].reset();

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        statusDiv.html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        };
    }

    $(function () {
        /** ----------------------------------------
         * Close Create Extra Session Form Wrapper
         * -------------------------------------- */
         $(document).on("click", "#close_create_extraclass_form", function () {
            $("#extraclass_form_container").html("");
            $("#get_create_extraclass_form").slideDown();
        });
    });

})(jQuery);
