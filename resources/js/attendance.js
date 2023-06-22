"use strict";

(function($) {
    $(document).on(
        "mouseenter",
        ".attendance_table tbody tr td:not(:first-child)",
        function() {
            $(this).addClass("attendance");
            $(".attendance_table tbody .attendance img").css(
                "display",
                "inline-block"
            );
        }
    );

    $(document).on(
        "mouseleave",
        ".attendance_table tbody tr td:not(:first-child)",
        function() {
            //
            $(this).removeClass("attendance");
            $(".attendance_table tbody tr td:not(:first-child) img").hide();
            $(".attendance_table tbody tr td ul li.active img").show();
        }
    );

    $(document).on("click", ".attendance_table ul.attendance-list li", function() {
        var probationerId   = $(this).closest('tr.probationer-attendance-tr').attr('data-probationer-id');
        var timetableId     = $(this).closest('td.attendance-td').attr('data-timetable-id');
        var attendance      = $(this).attr('data-attendance');

        $("#attendance_"+probationerId+"_"+timetableId).val(attendance);

        $(this)
            .addClass("active")
            .siblings()
            .removeClass("active");


        $(".attendance_table ul.attendance-list li img").hide();
        $(".attendance_table ul.attendance-list li.active img").show();
    });

    /** -------------------------------------------------------------------
     * Get ManualAttendance form submit
     * ----------------------------------------------------------------- */

    if (!window.get_manualAttendance_Submit) {
        window.get_manualAttendance_Submit = function() {
            var statusDiv = $("#manualAttendance_form_status");

            var theFormElement = $("form#get_manualAttendance_form");
            var FrmData = new FormData(theFormElement[0]);

            FrmData.append("requestName", "get_manualAttendanceForm");

            var actionUrl = theFormElement.attr("action");

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    window.loadingScreen("show");
                },
                success: function(rData) {
                    window.loadingScreen("hide");

                    $("#manualAttendance-container").html(rData);
                    // $(
                    //     "#manualAttendance-container .attendance_table tbody tr td:not(:first-child) "
                    // ).append(`
                    // <ul>
                    //   <li><img src='/images/present.png' /></li>
                    //   <li><img src='/images/absent.png' /></li>
                    //   <li><img src='/images/sickleave.png' /></li>
                    //   <li><img src='/images/mdo.png' /></li>
                    //   </ul>
                    // `);
                }
            });
        };
    }


    /** -------------------------------------------------------------------
     * ManualAttendance form submit
     * ----------------------------------------------------------------- */
    if (!window.manualAttendance_Submit) {
        window.manualAttendance_Submit = function () {
            var statusDiv = $("#manualAttendance_form_status");

            var manualAttendanceForm = $("form#manualAttendance_form");
            var FrmData = new FormData(manualAttendanceForm[0]);

            FrmData.append('requestName', 'submit_manualAttendanceForm');

            var actionUrl = manualAttendanceForm.attr("action");

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    // $("#manualAttendance_form_status").html(rData);

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        $("#successModalContent").html(
                            '<div class="success-msg">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");

                        window.get_manualAttendance_Submit();
                    } else {
                        statusDiv.html(
                            '<div class="error-msg">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * monthlyReport form submit
     * ----------------------------------------------------------------- */
    if (!window.get_monthlyReport_Submit) {
        window.get_monthlyReport_Submit = function () {
            var statusDiv = $("#monthlyReport_form_status");
            var formEl = $("form#monthlyReport_form");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".reqField").each(function () {
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
                FrmData.append('requestName', 'get_monthly_report_table');

                $.ajax({
                    url: appUrl +'/attendance/ajax',
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        window.loadingScreen("show");

                        statusDiv.html("");
                    },
                    success: function (rData) {
                        window.loadingScreen("hide");

                        $("#monthlyReport-container").html(rData);
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger mx-auto">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Monthly Report Download
     * ----------------------------------------------------------------- */
    if (!window.get_monthlyReport_Download) {
        window.get_monthlyReport_Download = function () {
            var statusDiv = $("#monthlyReport_form_status");
            var formEl = $("form#monthlyReport_form");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });
            $('#squad_id').removeClass('reqField');

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".reqField").each(function () {
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
                FrmData.append('requestName', 'validate_monthly_report_download');

                $.ajax({
                    url: appUrl +'/attendance/ajax',
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        window.loadingScreen("show");

                        statusDiv.html("");
                    },
                    success: function (rData) {
                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            var actionUrl   = appUrl + '/attendance/monthly-report-download';
                            var csrfToken  = $('meta[name="csrf-token"]').attr('content');
                            var formData    = JSON.stringify(rObj.data);

                            var downloadForm = $(`
                                <form method="post" action="${actionUrl}" class="hidden">
                                    <input type="hidden" name="form_data" value='${formData}' />
                                    <input type="hidden" name="_token" value="${csrfToken}" />
                                </form>
                            `);
                            $("body").append(downloadForm);
                            downloadForm.submit();
                        } else {
                            window.loadingScreen("hide");
                        }
                        window.loadingScreen("hide");
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger mx-auto">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * monthlySessions form submit
     * ----------------------------------------------------------------- */
    if (!window.get_monthlySessions_submit) {
        window.get_monthlySessions_submit = function () {
            var statusDiv = $("#get_monthlySessions_status");

            var monthlySessionsForm = $("form#monthlySessions_form");
            var FrmData = new FormData(monthlySessionsForm[0]);

            FrmData.append('requestName', 'get_monthlySessions_table');

            var actionUrl = monthlySessionsForm.attr("action");

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#monthlySessions-container").html(rData);
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Monthly Session Download
     * ----------------------------------------------------------------- */
     if (!window.get_monthlySessions_Download) {
        window.get_monthlySessions_Download = function () {
            var statusDiv = $("#get_monthlySessions_status");
            var formEl = $("form#monthlySessions_form");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            $('#squad_id').removeClass('reqField');

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".reqField").each(function () {
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
                FrmData.append('requestName', 'validate_monthly_sessions_download');

                $.ajax({
                    url: appUrl +'/attendance/ajax',
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        window.loadingScreen("show");

                        statusDiv.html("");
                    },
                    success: function (rData) {
                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            var actionUrl   = appUrl + '/attendance/monthly-sessions-download';
                            var csrfToken  = $('meta[name="csrf-token"]').attr('content');
                            var formData    = JSON.stringify(rObj.data);

                            var downloadForm = $(`
                                <form method="post" action="${actionUrl}" class="hidden">
                                    <input type="hidden" name="form_data" value='${formData}' />
                                    <input type="hidden" name="_token" value="${csrfToken}" />
                                </form>
                            `);
                            $("body").append(downloadForm);
                            downloadForm.submit();
                        } else {

                            statusDiv.html('<div class="msg msg-danger mx-auto">No Timetable found</div>');
                            window.loadingScreen("hide");
                        }
                        window.loadingScreen("hide");
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger mx-auto">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * missedSessions form submit
     * ----------------------------------------------------------------- */
    if (!window.get_missedSessions_submit) {
        window.get_missedSessions_submit = function () {
            var statusDiv = $("#get_missedSessions_status");

            var missedSessionsForm = $("form#get_missedSessions_form");
            var FrmData = new FormData(missedSessionsForm[0]);

            FrmData.append('requestName', 'get_missedSessions_table');

            var actionUrl = missedSessionsForm.attr("action");

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#missedSessions-container").html(rData);
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Missed Session Download
     * ----------------------------------------------------------------- */
     if (!window.get_missedSessions_Download) {
        window.get_missedSessions_Download = function () {
            var statusDiv = $("#get_missedSessions_status");
            var formEl = $("form#get_missedSessions_form");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".reqField").each(function () {
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
                FrmData.append('requestName', 'validate_missed_sessions_download');

                $.ajax({
                    url: appUrl +'/attendance/ajax',
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        window.loadingScreen("show");

                        statusDiv.html("");
                    },
                    success: function (rData) {
                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            var actionUrl   = appUrl + '/attendance/missed-sessions-download';
                            var csrfToken  = $('meta[name="csrf-token"]').attr('content');
                            var formData    = JSON.stringify(rObj.data);

                            var downloadForm = $(`
                                <form method="post" action="${actionUrl}" class="hidden">
                                    <input type="hidden" name="form_data" value='${formData}' />
                                    <input type="hidden" name="_token" value="${csrfToken}" />
                                </form>
                            `);
                            $("body").append(downloadForm);
                            downloadForm.submit();
                        } else {
                            window.loadingScreen("hide");
                        }
                        window.loadingScreen("hide");
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger mx-auto">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Get Extra Sessions form submit
     * ----------------------------------------------------------------- */
    if (!window.get_extraSessions_submit) {
        window.get_extraSessions_submit = function () {
            var statusDiv = $("#get_extraSessions_status");

            var extraSessionsForm = $("form#get_extraSessions_form");
            var FrmData = new FormData(extraSessionsForm[0]);

            FrmData.append('requestName', 'get_extraSessions_form');

            var actionUrl = extraSessionsForm.attr("action");

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#timetable-form-container").html(rData);
                    window.load_timetableSubactivities();
                }
            });
        };
    }


    /** -------------------------------------------------------------------
     * Add Extra Session form submit
     * ----------------------------------------------------------------- */
    if (!window.add_extraSessions_submit) {
        window.add_extraSessions_submit = function () {
            var statusDiv = $("#get_extraSessions_status");

            var addExtraSessionsForm = $("form#add_extraSessions_form");
            var FrmData = new FormData(addExtraSessionsForm[0]);

            FrmData.append('session_type', 'extra');

            FrmData.append('requestName', 'submit_timetableUpdateForm');

            var actionUrl = addExtraSessionsForm.attr("action"); // timetables/ajax

            $.ajax({
                url: actionUrl,
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    // $("#get_extraSessions_status").html(rData);

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        $("#successModalContent").html(
                            '<div class="success-msg">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");

                        window.get_extraSessions_submit();
                    } else {
                        statusDiv.html(
                            '<div class="error-msg">' + rObj.message + "</div>"
                        );
                    }
                }
            });
        };
    }
})(jQuery);
