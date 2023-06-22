"use strict";

(function ($) {
    /** -------------------------------------------------------------------
     * Get Extra Session Form
     * ----------------------------------------------------------------- */
    if (!window.get_extrasession_form) {
        window.get_extrasession_form = function () {
            var statusDiv = $("#get_create_extrasession_status");
            var container = $("#extrasession_form_container");
            var extraSessionForm = $("form#get_create_extrasession_form");

            extraSessionForm.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            extraSessionForm.find(".reqField").each(function () {
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

                FrmData.append('requestName', 'get_create_extraSession_form');

                $.ajax({
                    url: appUrl + "/extrasessions/ajax",
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        window.loadingScreen("show");
                        statusDiv.html('<div class="msg msg-info rl-margin-auto">Please wait...</div>');
                    },
                    success: function (rData) {
                        window.loadingScreen("hide");
                        statusDiv.html('');

                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            container.html(rObj.data);
                            $("#get_create_extrasession_form").slideUp();

                            if($("#subactivity_id").attr("data-has-subactivity") == "no") {
                                window.get_extraSession_probs(0);
                            }
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
     * Get Extra session Probationers List
     * ----------------------------------------------------------------- */
    if (!window.get_extraSession_probs) {
        window.get_extraSession_probs = function (subactivity_id) {

            var batch_id = $("input#batch_id").val();
            var activity_id = $("input#activity_id").val();

            $.ajax({
                url: appUrl + "/extrasessions/ajax",
                data: {
                    requestName: "get_extraSession_probationers",
                    batch_id: batch_id,
                    activity_id: activity_id,
                    subactivity_id: subactivity_id
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    $("#extrasession_probslist_container .probs_placeholder").addClass("hidden");
                    $("#extrasession_probslist_container .probs_data").removeClass("hidden");
                    if (rObj.status == "success") {
                        $("#extrasession_probslist_container .probs_data").html(rObj.data);
                        $("table.extrasession-sessions").removeClass("hidden");
                    } else {
                        $("#extrasession_probslist_container .probs_data").html(
                            `<div class="msg msg-danger msg-full">${rObj.message}</div>`
                        );
                        $("table.extrasession-sessions").addClass("hidden");
                    }
                }
            });
        }
    }


    /** -------------------------------------------------------------------
     * Create Extra Session Form Submit
     * ----------------------------------------------------------------- */
    if (!window.create_extra_session) {
        window.create_extra_session = function () {
            var statusDiv = $("#create_extrasession_status");
            var extraSessionForm = $("form#create_extrasession_form");

            extraSessionForm.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            extraSessionForm.find(".reqField").each(function () {
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
                $("#extrasession_addedprob_list input[name=extra_session_pb_cb]").each(function () {
                    probationer_ids.push($(this).val());
                });

                // console.log(probationer_ids);

                FrmData.append('probationer_ids', probationer_ids);
                FrmData.append('requestName', 'create_extrasession');

                $.ajax({
                    url: appUrl + "/extrasessions/ajax",
                    data: FrmData,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    beforeSend: function (xhr) {
                        window.loadingScreen("show");
                        statusDiv.html(`Please wait...`);
                    },
                    success: function (rData) {
                        window.loadingScreen("hide");
                        // statusDiv.html(rData);

                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            extraSessionForm[0].reset();
                            $("form#get_create_extrasession_form")[0].reset();

                            // $("#extrasession_addedprob_list tbody").find('tr:not(.no-data-tr)').remove();
                            // $("#extrasession_addedprob_list tbody").find('tr.no-data-tr').show();

                            $("#extrasession_form_container").html('<div class="msg msg-success mx-auto" style="width:75%;">' + rObj.message + '</div>');
                            $("#get_create_extrasession_form").slideDown();
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
     if (!window.get_extraSessions) {
        window.get_extraSessions = function () {
            var statusDiv = $("#get_extrasession_status");
            var extraSessionForm = $("form#get_extrasession_form");

            var FrmData = new FormData(extraSessionForm[0]);
            FrmData.append('requestName', 'get_extrasessions');

            $.ajax({
                url: appUrl + "/extrasessions/ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                    statusDiv.html(`<option value="">Please wait...</option>`);
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    statusDiv.html('');

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        // extraSessionForm[0].reset();
                        $("#extra_sessions_table tbody").html(rObj.data);
                        $("#extra_sessions_pagination").html('');
                        $("#session_list_title").html('Sessions list');
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
    if (!window.get_extraSessionsMeta) {
        window.get_extraSessionsMeta = function (sessionId) {
            var data = {
                sessionId: sessionId,
                requestName: "get_extra_sessions_meta",
            };

            $.ajax({
                url: appUrl + "/extrasessions/ajax",
                data: data,
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#successModalContent").html(rData);
                    $("#successModalBtns .modal-btn-primary").html('<button id="print_extra_session_btn" class="btn btn-info me-2">Print</button>');
                    $("#successModal").modal("show");
                    $("#successModal .modal-dialog").addClass("modal-lg");
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Get Extra Session Edit
     * ----------------------------------------------------------------- */
    if (!window.get_editExtraSession) {
        window.get_editExtraSession = function (sessionId) {
            var data = {
                session_id: sessionId,
                requestName: "get_extra_session_edit",
            };

            $.ajax({
                url: appUrl + "/extrasessions/ajax",
                data: data,
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#successModalContent").html(rData);
                    $("#successModalBtns .modal-btn-primary").html(`
                        <button type="button" class="btn btn-primary" id="editExtraSessionSubmit" onclick="window.editExtraSession();">Save</button>
                    `);
                    $("#successModal").modal("show");

                    window.getDatepicker();
                    // window.timerangeMask();
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Extra Session Submit
     * ----------------------------------------------------------------- */
    if (!window.editExtraSession) {
        window.editExtraSession = function (sessionId) {

            var statusDiv   = $("#editExtraSession_status");
            var formEL      = $("#editExtraSessionForm");
            var sessionId   = formEL.attr('data-session-id');

            var FrmData = new FormData(formEL[0]);
            FrmData.append('session_id', sessionId);
            FrmData.append('requestName', 'editExtraSession_submit');

            $.ajax({
                url: appUrl + "/extrasessions/ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        statusDiv.html(
                            '<div class="msg msg-success msg-full text-left">' + rObj.message + "</div>"
                        );

                        $("#extra_sessions_table").load(
                            location.href + " #extra_sessions_table>*"
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
        };
    }


    /** -------------------------------------------------------------------
     * Download Extara Session probationer's Datasheet
     * ----------------------------------------------------------------- */
    if (!window.download_extraSessionData) {
        window.download_extraSessionData = function (sessionId) {

            $.ajax({
                url: appUrl + '/extrasessions/ajax',
                data: {
                    session_id: sessionId,
                    requestName: "download_extraSessionData"
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
     if (!window.get_extraSessionImport_modal) {
        window.get_extraSessionImport_modal = function () {

            $.ajax({
                url: appUrl +'/extrasessions/ajax',
                data: {
                    requestName: "get_extraSessionImport_modal"
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
     if (!window.importExtraSessionData_submit) {
        window.importExtraSessionData_submit = function() {
            var statusDiv = $("#importExtraSessionData_status");

            var formEl = $("form#importExtraSessionData_form");
            var FrmData = new FormData(formEl[0]);

            FrmData.append('requestName', 'import_extraSession_data');

            var actionUrl = appUrl + "/extrasessions/ajax";

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
        $(document).on("click", "#close_create_extrasession_form", function () {
            $("#extrasession_form_container").html("");
            $("#get_create_extrasession_form").slideDown();
        });

        /** ----------------------------------------
         * Add more session
         * -------------------------------------- */
        $(document).on("click", "table.extrasession-sessions .plus-icon", function () {
            var table   = $("table.extrasession-sessions");

            var cTd = $(this).closest("td");
            var cTr = cTd.closest("tr");

            var content = cTr.html();

            table.append("<tr>"+content+"</tr>");

            var trCount = table.find("tr").length;
            table.find("tr:nth-child(" +trCount+ ") td:nth-child(1)").html("Session "+ (parseInt(trCount) - 1) );
            table.find("tr:nth-child(" +trCount+ ") input.datePicker").attr("id", "session_date_"+ (parseInt(trCount) - 1) )
                .removeClass("hasDatepicker");

            table.find("tr:nth-child(" +trCount+ ") input.session_probationers").val();
            table.find("tr:nth-child(" +trCount+ ") p.session_probationers_link").attr("id", "session_probationers_link_"+ (parseInt(trCount) - 1) );

            window.getDatepicker();
        });

        /** ----------------------------------------
         * Remove session
         * -------------------------------------- */
        $(document).on("click", "table.extrasession-sessions .cross-icon", function () {
            var cTr = $(this).closest("tr");
            cTr.remove();
        });


        /** ---------------------------------------
         * Get select probationer modal
         * -------------------------------------- */
        $(document).on("change", "#create_extrasession_form .subactivity_id", function () {
            var subactivity_id    = $(this).val();

            if(subactivity_id.length == 0) {
                $(this).addClass("input-error");
                $("#extrasession_probslist_container .probs_data").html("").addClass("hidden");
                $("#extrasession_probslist_container .probs_placeholder").removeClass("hidden");
                $("table.extrasession-sessions").addClass("hidden");
                return;
            }

            window.get_extraSession_probs(subactivity_id);
        });

        /** ---------------------------------------
         * Select / unselect probationer cb
         * -------------------------------------- */
        $(document).on("change", "input.extra-session-pb-cb", function () {
            var esPbCb = $(this);
            var esPb_id = $(this).val();

            // console.log(esPb_id);

            var pbTable = $("#extrasession_prob_list tbody");
            var memberTable = $("#extrasession_addedprob_list tbody");


            if (esPbCb.is(":checked")) {
                var pbData = pbTable.find("#extra-session-pb-tr-" + esPb_id);
                console.log(pbData.attr("data-pb-id"));

                memberTable.append(pbData[0].outerHTML);
                memberTable.find("#extra-session-pb-tr-" + esPb_id + " input[name=extra_session_pb_cb]").prop("checked", true);

                setTimeout(function () {
                    pbData.remove();
                }, 100);
            } else {
                var memberData = memberTable.find("#extra-session-pb-tr-" + esPb_id);
                pbTable.append(memberData[0].outerHTML);

                pbTable.find("tr.no-data-tr").remove();

                setTimeout(function () {
                    memberData.remove();
                }, 100);
            }

            setTimeout(function () {
                if(memberTable.find("tr").length < 2) {
                    memberTable.find("tr.no-data-tr").show();
                } else {
                    memberTable.find("tr.no-data-tr").hide();
                }

                if(pbTable.find("tr").length == 0) {
                    pbTable.html(`
                        <tr class="no-data-tr">
                            <td colspan="4">No data available in table</th>
                        </tr>
                    `);
                }
            }, 150);
        });
    });

})(jQuery);
