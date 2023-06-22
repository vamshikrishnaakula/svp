"use strict";


(function($) {

    /** -------------------------------------------------------------------
     * Get Activities Dropdown
     * ----------------------------------------------------------------- */
    if (!window.get_activity_dropdowns) {
        window.get_activity_dropdowns = function(el) {
            var batch_id = $(el).val();
            var data = {
                batch_id: batch_id,
                requestName: "get_activities_dropdown",
            };

            $.ajax({
                url: appUrl + "/timetables/ajax",
                data: data,
                type: "POST",
                beforeSend: function(xhr) {
                    // window.loadingScreen("show");
                    $("#activity_id").html(`<option value="">Please wait...</option>`);
                },
                success: function(rData) {
                    // window.loadingScreen("hide");
                    $("#activity_id").html(rData);
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Get SubActivities Dropdown
     * ----------------------------------------------------------------- */
    if (!window.get_subactivity_dropdowns) {
        window.get_subactivity_dropdowns = function(el) {
            var activity_id = $(el).val();
            var data = {
                activity_id: activity_id,
                requestName: "get_subactivities_dropdown",
            };

            $.ajax({
                url: appUrl + "/timetables/ajax",
                data: data,
                type: "POST",
                beforeSend: function(xhr) {
                    // window.loadingScreen("show");
                    $("#subactivity_id").html(`<option value="">Please wait...</option>`);
                },
                success: function(rData) {
                    // window.loadingScreen("hide");
                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        $("#subactivity_id").html(rObj.data);
                        if (rObj.count > 0) {
                            $("#subactivity_id").addClass("reqField");
                        } else {
                            $("#subactivity_id").removeClass("reqField");
                        }
                    }
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Get Subactivities
     * ----------------------------------------------------------------- */
    if (!window.load_timetableSubactivities) {
        window.load_timetableSubactivities = function() {
            $("#timetable-form-container .timetable-activity > select").each(function() {
                var activity_id = $(this).val();
                var tt_id = $(this).closest("td").attr("data-timetable-id");
                var tt_date = $(this).closest("td").attr("data-timetable-date");
                var tt_sequence = $(this).closest("td").attr("data-sequence-id");

                get_timetableSubactivities(this, activity_id, tt_id, tt_date, tt_sequence);
            });
        };
    }

    $(function() {
        $(document).on("change", "#timetable-form-container .timetable-activity > select", function() {
            var activity_id = $(this).val();
            var tt_id = $(this).closest("td").attr("data-timetable-id");
            var tt_date = $(this).closest("td").attr("data-timetable-date");
            var tt_sequence = $(this).closest("td").attr("data-sequence-id");

            get_timetableSubactivities(this, activity_id, tt_id, tt_date, tt_sequence);

        });
    });

    function get_timetableSubactivities(el, activity_id, timetable_id, tt_date, tt_sequence) {
        var timetableSubactivity = $(el).parent().siblings(".timetable-subactivity");

        // $(this).parent().siblings(".timetable-subactivity").html(activity);

        // console.log("tt: "+tt_id);
        $.ajax({
            url: appUrl + "/timetables/ajax",
            data: {
                requestName: "timetableSubactivities",
                activity_id: activity_id,
                timetable_id: timetable_id,
                timetable_date: tt_date,
                sequence_id: tt_sequence,
            },
            type: "POST",
            beforeSend: function(xhr) {
                window.loadingScreen("show");
            },
            success: function(rData) {
                window.loadingScreen("hide");
                timetableSubactivity.html(rData);
            }
        });
    }

    /** -------------------------------------------------------------------
     * Add more session (column) in create timetable
     * ----------------------------------------------------------------- */

    $(document).on("click", "table.timetable .add_more_session", function() {
        var theTable = $(this).closest("table.timetable");
        var tIndex = parseInt($(this).closest("th").index());
        theTable.find('thead tr').each(function() {
            var trow = $(this);

            var sessionNum = tIndex;
            var $html = `
                Session ${sessionNum}
                <span class="add_more_session"><i class="fas fa-plus"></i> Add</span>
            `;
            trow.append('<th>' + $html + '</th>');
        });

        theTable.find('tbody tr').each(function() {
            var trow = $(this);
            console.log(trow);

            var tcell = trow.find("td").eq(tIndex - 2);

            console.log(tcell);


            var ttDate = tcell.attr("data-timetable-date");

            var sequenceId = parseInt(tcell.attr("data-sequence-id")) + 1;

            var activities = tcell.find(".timetable-activity select").html();
            activities = activities.replace('selected', '');

            var $html = `
                <td data-sequence-id="${sequenceId}" data-timetable-id="" data-timetable-date="${ttDate}">
                    <div class="form-group">
                        <div>
                            <div class="timetable-activity">
                                <select name="activity_id[${ttDate}][${sequenceId}]" class="form-control">${activities}</select>
                            </div>
                            <div class="timetable-subactivity"></div>
                        </div>

                        <div class="session-timerange-row">
                            <div class="session-timerange-col">
                                <input type="text" name="session_time_start[${ttDate}][${sequenceId}]" value="" placeholder="HH:MM" data-valid-example="08:30" class="form-control jquery-timeinput-mask mt-2">
                            </div>
                            <div class="session-timerange-col">-</div>
                            <div class="session-timerange-col">
                                <input type="text" name="session_time_end[${ttDate}][${sequenceId}]" value="" placeholder="HH:MM" data-valid-example="08:30" class="form-control jquery-timeinput-mask mt-2">
                            </div>
                        </div>

                        <input type="hidden" name="activity_date[${ttDate}][${sequenceId}]" value="${ttDate}" class="hidden">
                        <input type="hidden" name="session_number[${ttDate}][${sequenceId}]" value="${sequenceId}" class="hidden">
                    </div>
                </td>
            `;

            trow.append($html);
        });
    });

    /** -------------------------------------------------------------------
     * Get Timetable Create form submit
     * ----------------------------------------------------------------- */

    if (!window.get_timetableView) {
        window.get_timetableView = function() {

            var statusDiv = $("#get_timetableView_status");

            var getTimetableForm = $("form#get_timetableView_form");
            var FrmData = new FormData(getTimetableForm[0]);

            FrmData.append('requestName', 'get_timetableView');

            var actionUrl = getTimetableForm.attr("action");

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

                    $("#timetable-view-container").html(rData);
                }
            });
        };
    }

    /** -------------------------------------------------------------------
     * Get Timetable Update form submit
     * ----------------------------------------------------------------- */

    if (!window.get_timetableUpdate_Submit) {
        window.get_timetableUpdate_Submit = function() {

            var statusDiv = $("#timetableUpdate_form_status");

            var getTimetableForm = $("form#get_timetableUpdate_form");
            var FrmData = new FormData(getTimetableForm[0]);

            FrmData.append('requestName', 'get_timetableUpdateForm');

            var actionUrl = getTimetableForm.attr("action");

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

                    $("#timetable-form-container").html(rData);
                    window.load_timetableSubactivities();
                }
            });
        };
    }


    /** -------------------------------------------------------------------
     * Timetable Update form submit
     * ----------------------------------------------------------------- */
    if (!window.timetableUpdate_Submit) {
        window.timetableUpdate_Submit = function() {debugger;
            var statusDiv = $("#timetableUpdate_form_status");

            var getTimetableForm = $("form#timetableUpdate_form");

            getTimetableForm.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            getTimetableForm.find(".reqField").each(function () {
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

            var FrmData = new FormData(getTimetableForm[0]);

            FrmData.append('requestName', 'submit_timetableUpdateForm');

            var actionUrl = getTimetableForm.attr("action");
            if (isValid == true) {
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

                    // $("#timetableUpdate_form_status").html(rData);

                    let rObj = JSON.parse(rData);

                    if (rObj.status == "success") {
                        $("#successModalContent").html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");

                        window.get_timetableUpdate_Submit();
                    } else {
                        statusDiv.html(
                            '<div class="msg msg-danger msg-full">' + rObj.message + "</div>"
                        );
                    }
                }
            });
            }
            else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger msg-full">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Import Timetable Modal
     * ----------------------------------------------------------------- */
    // if (!window.importTimetable_Modal) {
    //     window.importTimetable_Modal = function() {
    //         $("#dataImportModalTitle").html("Import Timetable");
    //         $("#dataImportModalBtns .modal-btn-primary").html(
    //             `<button type="button" class="btn btn-primary"  onclick="window.importTimetable_Submit();">Submit</button>`
    //         );

    //         var importForm = `
    //             <form name="importTimetable_form" id="importTimetable_form" action="#" method="post" class="text-center" enctype="multipart/form-data" accept-charset="utf-8">
    //                 <input type="file" name="timetable_csv" accept=".csv" />
    //             </form>

    //             <div id="importTimetable_form_status"></div>
    //         `;

    //         $("#dataImportModalContent").html(importForm);
    //         $("#dataImportModal").modal("show");
    //     };
    // }

    /** -------------------------------------------------------------------
     * Get Import Data Modal
     * ----------------------------------------------------------------- */
     if (!window.get_timetableImport_modal) {
        window.get_timetableImport_modal = function () {

            $.ajax({
                url: appUrl +'/timetables/ajax',
                data: {
                    requestName: "get_timetableImport_modal"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#dataImportModalTitle").html("Import Timetable");
                    $("#dataImportModalContent").html(rData);
                    $("#dataImportModalBtns").hide();
                    $("#dataImportModal .modal-dialog").addClass("modal-lg");
                    $("#dataImportModal").modal("show");
                    let batchId    = $("#data_batch_id");
                    if(batchId.val().length) {
                        window.select_batchId_changed(batchId, 'data_squad_id');
                    }
                    window.getDatepicker();
                }
            });
        }
    };


    /** -------------------------------------------------------------------
     * Download Timetable Datasheet
     * ----------------------------------------------------------------- */
     if (!window.download_timetableDatasheet) {
        window.download_timetableDatasheet = function () {

            var formEl = $("form#download_timetableDatasheet_form");
            var statusDiv = $("#download_timetableDatasheet_status");

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
                FrmData.append('requestName', 'download_timetableDatasheet');

                $.ajax({
                    url: appUrl +'/timetables/ajax',
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
                        let rObj = JSON.parse(rData);

                        if (rObj.status == "success") {
                            // statusDiv.html(rObj.data);

                            let newTab = window.open(rObj.datasheet_url);
                            // newTab.location.href = rObj.datasheet_url;
                        } else {
                            statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                        }
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger msg-full">Fill all the required fields</div>');
            }
        }
    };

    /** -------------------------------------------------------------------
     * Import Timetable Form Submit
     * ----------------------------------------------------------------- */
    if (!window.importTimetable_submit) {
        window.importTimetable_submit = function() {
            var statusDiv = $("#importTimetable_status");

            var importTimetableForm = $("form#importTimetable_form");
            var FrmData = new FormData(importTimetableForm[0]);

            FrmData.append('requestName', 'import_Timetable');

            var actionUrl = appUrl + "/timetables/ajax";

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
                    importTimetableForm[0].reset();
                    // $("#timetableUpdate_form_status").html(rData);

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

})(jQuery);

$("#createtimetable .timetable tbody td").append(`
    <div class="form-group">
    <select class="form-control">
    <option>1</option>
    <option>2</option>
    <option>3</option>
    <option>4</option>
    </select>
    </div>
    <div class="timerange">
    <div class="form-group">
    <label>From :</label>
    <div class="input-group time" id="datetimepicker4" data-target-input="nearest">
        <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker4"/>
        <div class="input-group-append" data-target="#datetimepicker4" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-clock"></i></div>
        </div>
    </div>
</div>
<div class="form-group">
    <label>To :</label>
    <div class="input-group time" id="datetimepicker4" data-target-input="nearest">
        <input type="text" class="form-control datetimepicker-input" data-target="#datetimepicker4"/>
        <div class="input-group-append" data-target="#datetimepicker4" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-clock"></i></div>
        </div>
    </div>
</div></div>
`);

$(document).ready(function() {
    $(document).on(
        "click",
        "#viewtimetable .timetable tbody tr td a.editvalue",
        function() {
            //
            var dad = $(this).siblings("div");

            dad.siblings("span").hide();
            var placeholder = dad.siblings("span").text();

            dad.css("display", "flex");
            var input = dad.find('input[type="text"]');
            input.focus();
            input.attr("placeholder", placeholder);
            $(this).hide();
        }
    );

    // $("input[type=text]").blur(function() {
    //     var dad = $(this).parent();
    //     dad.hide();
    //     dad.siblings("span").show();
    //     dad.siblings("a.editvalue").show();
    // });

    $(document).on(
        "click",
        "#viewtimetable .timetable tbody tr td div a.update",
        function() {
            //
            var dad = $(this).parent();
            var input = dad.children("div input");

            var inputvalue = $(input).val();
            dad.siblings("span")
                .text(inputvalue)
                .show();
            dad.parents()
                .find("a.editvalue")
                .show();
            dad.hide();
        }
    );

});
