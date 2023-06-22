"use strict";

(function ($) {
    /* ---------------------- Get Activities on Batch Select --------------- */

    if (!window.get_activity_options) {
        window.get_activity_options = function (el, FieldId) {
            var batchId = $(el).val();
            if (batchId.length > 0) {
                // console.log("Batch Id: "+ batchId);

                var data = {
                    batch_id: batchId,
                    requestName: "activityDropdownOptions"
                };

                var actionUrl = appUrl + "/activities/ajax";

                $.ajax({
                    url: actionUrl,
                    data: data,
                    type: "POST",
                    // processData: false,
                    // contentType: false,
                    beforeSend: function (xhr) {
                        // window.loadingScreen("show");

                        $("#" + FieldId).html(
                            '<option value="">Please wait...</option>'
                        );
                    },
                    success: function (rData) {
                        // window.loadingScreen("hide");

                        $("#" + FieldId).html(rData);
                    }
                });
            }
        };
    }

    /* ---------------------- Get List of Activities Table on Batch Select --------------- */
    if (!window.get_list_of_activities_table) {
        window.get_list_of_activities_table = function (el) {
            var batchId = $(el).val();
            if(!batchId) {
                batchId = 0;
            }

            var search  = $("#activity-search-container input.search").val();

            var data = {
                search: search,
                batch_id: batchId,
                requestName: "getListOfActivitiesTable"
            };

            var actionUrl = appUrl + "/activities/ajax";

            $.ajax({
                url: actionUrl,
                data: data,
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#activity-list-tbody").html(rData);
                }
            });
        };
    }

    /* ---------------------- Add Sub Activities --------------- */
    $(document).on("keypress", "#activity-search-container input.search", function (e) {
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            var batchId = $("select#batch_id").val();
            if(!batchId) {
                batchId = 0;
            }

            var search  = $(this).val();

            var data = {
                search: search,
                batch_id: batchId,
                requestName: "getListOfActivitiesTable"
            };

            var actionUrl = appUrl + "/activities/ajax";

            $.ajax({
                url: actionUrl,
                data: data,
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#activity-list-tbody").html(rData);
                }
            });
        }
    });

    /* ---------------------- Add Sub Activities --------------- */
    $(document).on("click", "#AddSubActivity", function () {
        //

        var subActivities = $("#sub_activities");

        var subActivityCount = $(this).attr("data-subactivity-count");
        // var subActivityId = subActivityCount;
        var subActivityId = Number(subActivityCount) + 1;

        var subActivityHtml = `
            <div class="sub-activity-item" data-subactivity-sl="${subActivityId}">
                <div class="sub-activity-item-inner">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Sub Activity</label>
                            <span class="deleteSubactivityBtn delete small float-right">Delete</span>
                            <div>
                                <input type="text" name="sub_activity[${subActivityId}]" id="sub_activity_${subActivityId}" class="form-control sub-activity-name reqField" />
                                <div class="add addComponentBtn" data-subactivity-sl="${subActivityId}">
                                    <img src="${assetUrl}images/add.png" />
                                    <span>Add components</span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <label>Unit</label>
                            <input type="text" name="sub_activity_unit[${subActivityId}]" id="sub_activity_unit_${subActivityId}" class="form-control sub-activity-unit">
                            <div class="d-flex justify-content-start has_grading_wrapper">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="sub_activity_has_grading[${subActivityId}]" value="1" class="form-check-input sub_activity_has_grading-input" id="sub_activity_has_grading_${subActivityId}">
                                    <label class="form-check-label ml-0" for="sub_activity_has_grading_${subActivityId}">Use Grading</label>
                                </div>
                                <div class="form-group form-check ml-3">
                                    <input type="checkbox" name="sub_activity_has_qualify[${subActivityId}]" value="1" class="form-check-input sub_activity_has_qualify-input" id="sub_activity_has_qualify_${subActivityId}">
                                    <label class="form-check-label ml-0" for="sub_activity_has_qualify_${subActivityId}">Use Qualify</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="components-wrapper">
                        <div id="components_${subActivityId}" class="components"></div>
                    </div>
                </div>
            </div>
        `;

        subActivities.append(subActivityHtml);
        $(this).attr(
            "data-subactivity-count",
            Number(subActivityCount) + 1
        );

        activityUnitFix();
    });

    /*----------------Delete Sub Activity-----------*/
    $(function () {
        $(document).on("click", ".deleteSubactivityBtn", function () {
            $(this)
                .parents("div.sub-activity-item")
                .remove();

            activityUnitFix();
        });
    });

    function activityUnitFix() {
        var saCount = $("#sub_activities").children().length;
        if (saCount > 0) {
            $("#activity_unit").prop("readonly", true);
            $("#activity_unit").val("");
            $("#activity_unit").removeClass("reqField");
            $("#activity_unit").removeClass("input-error");

            $("#activity_has_grading").prop('checked', false);
            $("#activity_has_grading").attr("onclick", "return false");

            $("#activity_has_qualify").prop('checked', false);
            $("#activity_has_qualify").attr("onclick", "return false");
        } else {
            $("#activity_unit").prop("readonly", false);
            // $("#activity_unit").addClass("reqField");

            $("#activity_has_grading").attr("onclick", "");
            $("#activity_has_qualify").attr("onclick", "");
        }
    }

    /* ------------------ Add Component ----------------- */
    $(document).on("click", ".addComponentBtn", function () {
        var subActivityId = $(this).attr("data-subactivity-id");
        var subActivitySL = $(this).attr("data-subactivity-sl");
        //
        addComponent(subActivitySL, subActivityId);
    });

    function addComponent(sl, subActivity) {
        if( (subActivity !== "") && (subActivity !== null) && (subActivity !== undefined) ) {
            var inputNameKey   = sl+"-"+subActivity;
        } else {
            var inputNameKey   = sl;
        }
        var componentsWrapper = $("#components_" + sl);

        var randomNum   = Math.random();

        var componentHtml = `
            <div class="component-item" data-subactivity-sl="${sl}">
                <div class="row">
                    <div class="col">
                        <label class="float-left">Components</label>
                        <span class="deleteComponentBtn delete small float-right">Delete</span>

                        <input type="text" name="component[${inputNameKey}][]" class="form-control component-name reqField">
                    </div>
                    <div class="col">
                        <label>Unit</label>
                        <input type="text" name="component_unit[${inputNameKey}][]" class="form-control component-unit">

                        <div class="d-flex justify-content-start has_grading_wrapper">
                            <div class="form-group form-check">
                                <input type="checkbox" name="component_has_grading[${inputNameKey}][]" value="1" class="form-check-input component_has_grading-input" id="component_has_grading_${inputNameKey}_${randomNum}">
                                <label class="form-check-label ml-0" for="component_has_grading_${inputNameKey}_${randomNum}">Use Grading</label>
                            </div>
                            <div class="form-group form-check ml-3">
                                <input type="checkbox" name="component_has_qualify[${inputNameKey}][]" value="1" class="form-check-input component_has_qualify-input" id="component_has_qualify_${inputNameKey}_${randomNum}">
                                <label class="form-check-label ml-0" for="component_has_qualify_${inputNameKey}_${randomNum}">Use Qualify</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        componentsWrapper.append(componentHtml);

        subactivityUnitFix();
    }

    /* ----------------Delete Component----------- */
    $(function () {
        $(document).on("click", ".deleteComponentBtn", function () {
            $(this)
                .parents("div.component-item")
                .remove();

            var dataId = $(this).parents("div.component-item").attr("data-subactivity-sl");
            console.log("Subactivity removed: " + dataId);

            subactivityUnitFix();
        });
    });

    /* ------------------- Subactivity Unit Enable / Disable --------------------- */
    function subactivityUnitFix() {
        $("#sub_activities .sub-activity-item").each(function () {
            var subActivityInput = $(this);

            var subactivity_id = $(this).attr("data-subactivity-sl");

            var count = $(this).find("div.components").children().length;
            if (count > 0) {
                $("#sub_activity_unit_" + subactivity_id).prop("readonly", true);
                $("#sub_activity_unit_" + subactivity_id).val("");
                $("#sub_activity_unit_" + subactivity_id).removeClass("reqField");
                $("#sub_activity_unit_" + subactivity_id).removeClass("input-error");

                $("#sub_activity_has_grading_" + subactivity_id).prop('checked', false);
                $("#sub_activity_has_grading_" + subactivity_id).attr("onclick", "return false");

                $("#sub_activity_has_qualify_" + subactivity_id).prop('checked', false);
                $("#sub_activity_has_qualify_" + subactivity_id).attr("onclick", "return false");
            } else {
                if(
                    (subActivityInput.find("input.sub_activity_has_grading-input").prop('checked') != true) &&
                    (subActivityInput.find("input.sub_activity_has_qualify-input").prop('checked') != true)
                ) {
                    subActivityInput.find(".sub-activity-unit").prop("readonly", false);
                    // subactivityUnitFix();
                }

                // $("#sub_activity_unit_" + subactivity_id).prop("readonly", false);
                // $("#sub_activity_unit_" + subactivity_id).addClass("reqField");

                $("#sub_activity_has_grading_" + subactivity_id).attr("onclick", "");
                $("#sub_activity_has_qualify_" + subactivity_id).attr("onclick", "");
            }
        });
        // var componentsWrapper = $("#components_" + subactivity_id);
    }

    /* ------------------ Activity Unit fix on page load --------------------------- */
    $(function () {
        activityUnitFix();
        subactivityUnitFix();
    });

    /* ------------------ diable unit if has_grade or has_qualify selected ----------------- */
    $(document).on("change", "input.activity_has_grading-input, input.activity_has_qualify-input", function () {
        if(this.checked) {
            $("#activity_unit").val("");
            $("#activity_unit").prop("readonly", true);

             // Disable the other checkbox
             if($("input.activity_has_grading-input").prop('checked') == true) {
                $("input.activity_has_qualify-input").prop('checked', false)
                    .prop('readonly', true)
                    .attr("onclick", "return false");
            }
            if($("input.activity_has_qualify-input").prop('checked') == true) {
                $("input.activity_has_grading-input").prop('checked', false)
                    .prop('readonly', true)
                    .attr("onclick", "return false");
            }
        } else {
            if(
                ($("input.activity_has_grading-input").prop('checked') != true) &&
                ($("input.activity_has_qualify-input").prop('checked') != true)
            ) {
                $("#activity_unit").prop("readonly", false);
                activityUnitFix();
            }
        }
    });
    $(document).on("change", "input.sub_activity_has_grading-input, input.sub_activity_has_qualify-input", function () {
        var subActivityItem    = $(this).closest(".sub-activity-item");
        if(this.checked) {
            subActivityItem.find(".sub-activity-unit").val("");
            subActivityItem.find(".sub-activity-unit").prop("readonly", true);

            // Disable the other checkbox
            if(subActivityItem.find("input.sub_activity_has_grading-input").prop('checked') == true) {
                subActivityItem.find("input.sub_activity_has_qualify-input").prop('checked', false)
                    .prop('readonly', true)
                    .attr("onclick", "return false");
            }
            if(subActivityItem.find("input.sub_activity_has_qualify-input").prop('checked') == true) {
                subActivityItem.find("input.sub_activity_has_grading-input").prop('checked', false)
                    .prop('readonly', true)
                    .attr("onclick", "return false");
            }
        } else {
            if(
                (subActivityItem.find("input.sub_activity_has_grading-input").prop('checked') != true) &&
                (subActivityItem.find("input.sub_activity_has_qualify-input").prop('checked') != true)
            ) {
                subActivityItem.find(".sub-activity-unit").prop("readonly", false);
                subactivityUnitFix();
            }
        }
    });
    $(document).on("change", "input.component_has_grading-input, input.component_has_qualify-input", function () {
        var componentInput    = $(this).closest(".component-item");
        if(this.checked) {

            componentInput.find(".component-unit").val("");
            componentInput.find(".component-unit").removeClass("reqField");
            componentInput.find(".component-unit").prop("readonly", true);

            // Disable the other checkbox
            if(componentInput.find("input.component_has_grading-input").prop('checked') == true) {
                componentInput.find("input.component_has_qualify-input").prop('checked', false)
                    .prop('readonly', true)
                    .attr("onclick", "return false");
            }
            if(componentInput.find("input.component_has_qualify-input").prop('checked') == true) {
                componentInput.find("input.component_has_grading-input").prop('checked', false)
                    .prop('readonly', true)
                    .attr("onclick", "return false");
            }
        } else {
            // remove
            componentInput.find(".form-check-input").removeAttr("onclick");
            if(
                (componentInput.find("input.component_has_grading-input").prop('checked') != true) &&
                (componentInput.find("input.component_has_qualify-input").prop('checked') != true)
            ) {
                componentInput.find(".component-unit").addClass("reqField");
                componentInput.find(".component-unit").prop("readonly", false);
            }
        }
    });


    $(function() {
        /** ---------- Disable Grade if Subactivit has_grade / has_qualify in Edit Activity ON page load  ---------- */
        $(".sub-activities .sub-activity-item").each(function(){
            var subActivityItem   = $(this);
            if(
                (subActivityItem.find("input.sub_activity_has_grading-input").prop('checked') == true) ||
                (subActivityItem.find("input.sub_activity_has_qualify-input").prop('checked') == true)
            ) {
                subActivityItem.find(".sub-activity-unit").removeClass("reqField");
                subActivityItem.find(".sub-activity-unit").prop("readonly", true);
            }
        });

        /** ---------- Disable Grade if Component has_grade / has_qualify in Edit Activity ON page load  ---------- */
        $(".components-wrapper .component-item").each(function(){
            var componentItem   = $(this);
            if(
                (componentItem.find("input.component_has_grading-input").prop('checked') == true) ||
                (componentItem.find("input.component_has_qualify-input").prop('checked') == true)
            ) {
                componentItem.find(".component-unit").removeClass("reqField");
                componentItem.find(".component-unit").prop("readonly", true);
            }
        });
    });

    /* ---------------------- Submit Activity Form ----------------------- */
    $("#createactivity_form").on("submit", function (e) {
        e.preventDefault();

        var statusDiv = $("#createactivity_form_status");
        var activityForm = $("form#createactivity_form");

        activityForm.find(".form-control").each(function () {
            $(this).removeClass("input-error");
        });

        var isValid = true;
        var firstError = "";
        var ErrorCount = 0;

        activityForm.find(".reqField").each(function () {
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
            var FrmData = new FormData(activityForm[0]);

            var actionUrl = activityForm.attr("action");

            $.ajax({
                url: actionUrl,
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
                        // activityForm[0].reset();

                        setTimeout(function () {
                            $("#createactivity_form").load(
                                location.href + " #createactivity_form>*"
                            );
                        }, 1000);

                        $("#successModalContent").html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");
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
    });


    /* ---------------------- Edit Activity Form Submit ----------------------- */
    $("#editactivity_form").on("submit", function (e) {
        e.preventDefault();

        var statusDiv = $("#editactivity_form_status");
        var activityForm = $("form#editactivity_form");

        activityForm.find(".form-control").each(function () {
            $(this).removeClass("input-error");
        });

        var isValid = true;
        var firstError = "";
        var ErrorCount = 0;

        activityForm.find(".reqField").each(function () {
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

            var activity_id = $("#activity_id").val();

            var FrmData = new FormData(activityForm[0]);
            FrmData.append("activity_id", activity_id);

            var actionUrl = activityForm.attr("action");

            $.ajax({
                url: actionUrl,
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

                        $("#successModalContent").html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");

                        setTimeout(function () {
                            window.location.href    = appUrl + "/activities";
                        }, 1500);

                        statusDiv.html('');
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
    });

    /* ---------------------- Get Delete Activity Popup ----------------------- */
    if (!window.get_deleteActivity) {
        window.get_deleteActivity = function (activity_id, activity_type = "") {
            $("#successModalContent").html(
                `<p class="text-danger">Do you want to delete the ${activity_type}?</p>`
            );
            $("#successModalBtns .modal-btn-primary").html(
                `<button type="button" class="btn btn-primary" onclick="window.deleteActivity(${activity_id}, '${activity_type}');">Delete</button>`
            );
            $("#successModalBtns .modal-btn-secondary button").html('Cancel');
            $("#successModal").modal("show");
        }
    };

    /* ---------------------- Delete Activity ----------------------- */
    if (!window.deleteActivity) {
        window.deleteActivity = function (activity_id, activity_type = '') {

            $.ajax({
                url: appUrl + "/activities/ajax",
                data: {
                    activity_id: activity_id,
                    requestName: "delete_activity"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    $("#successModal").modal("hide");
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    let rObj = JSON.parse(rData);

                    if (rObj.status !== "success") {
                        window.loadingScreen("hide");

                        $("#successModalContent").html(
                            '<div class="msg msg-danger">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");
                    } else {
                        if(activity_type === 'Activity') {
                            window.location.replace(appUrl + "/activities");
                        } else {
                            location.reload();
                        }
                    }
                }
            });

        }
    };

    /* ---------------------- Delete Activity ----------------------- */
    if (!window.get_undeleteActivity) {
        window.get_undeleteActivity = function (activity_id, activity_type = "") {
            $("#successModalContent").html(
                `<p class="text-danger">Do you want to Restore the ${activity_type}?</p>`
            );
            $("#successModalBtns .modal-btn-primary").html(
                `<button type="button" class="btn btn-primary" onclick="window.undeleteActivity(${activity_id});">Restore</button>`
            );
            $("#successModalBtns .modal-btn-secondary button").html('Cancel');
            $("#successModal").modal("show");
        };
    }

    /* ---------------------- Delete Activity ----------------------- */
    if (!window.undeleteActivity) {
        window.undeleteActivity = function (activity_id) {

            $.ajax({
                url: appUrl + "/activities/ajax",
                data: {
                    activity_id: activity_id,
                    requestName: "undelete_activity"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    $("#successModal").modal("hide");
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    let rObj = JSON.parse(rData);

                    if (rObj.status !== "success") {
                        window.loadingScreen("hide");

                        $("#successModalContent").html(
                            '<div class="msg msg-danger">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");
                    } else {
                        location.reload();
                    }
                }
            });

        }
    };

    /** -------------------------------------------------------------------
     * Get Assign Activity form submit
     * ----------------------------------------------------------------- */
    if (!window.get_assignActivity_submit) {
        window.get_assignActivity_submit = function () {
            var statusDiv = $("#get_assignActivity_status");

            var assignActivityForm = $("form#get_assignActivity_form");
            var FrmData = new FormData(assignActivityForm[0]);

            FrmData.append('requestName', 'get_assignActivity_form');

            var actionUrl = assignActivityForm.attr("action");

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

                    $("#assignActivity-container").html(rData);
                }
            });
        };
    }

    $(document).on('change', '#assignactivity .activityStaffList', function () {
        var statusDiv = $("#assignActivity_status");

        var trainerId = $(this).val();
        var activityId = $("input[name=current_activity_id]").val();
        var squadId = $(this).closest('li.squad-list-item').attr('data-squad-id');

        //console.log("TrainerId: "+ trainerId+", ActivityId: "+activityId+", SquadId: "+squadId);

        var data = {
            squad_id: squadId,
            activity_id: activityId,
            trainer_id: trainerId,
            requestName: "submit_assignActivity"
        };

        $.ajax({
            url: appUrl + "/activities/ajax",
            data: data,
            type: "POST",
            beforeSend: function (xhr) {
                window.loadingScreen("show");

                statusDiv.html(
                    '<div class="text-info">Please wait while updating changes</div>'
                );
            },
            success: function (rData) {
                window.loadingScreen("hide");

                // statusDiv.html(rData);
                let rObj = JSON.parse(rData);

                if (rObj.status == "success") {
                    statusDiv.html(
                        '<div class="text-success">' + rObj.message + "</div>"
                    );
                } else {
                    statusDiv.html(
                        '<div class="text-error">' + rObj.message + "</div>"
                    );
                }

                setTimeout(function () {
                    statusDiv.html('');
                }, 3000);
            }
        });
    });

      /** -------------------------------------------------------------------
     * Get Import and download Data Modal
     * ----------------------------------------------------------------- */
       if (!window.get_activityImport_modal) {
        window.get_activityImport_modal = function () {

            $.ajax({
                url: appUrl +'/activities/ajax',
                data: {
                    requestName: "get_activityImport_modal"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $("#dataImportModalTitle").html("Import and export activities");
                    $("#dataImportModalContent").html(rData);
                    $("#dataImportModalBtns").hide();
                    $("#dataImportModal .modal-dialog").addClass("modal-lg");
                    $("#dataImportModal").modal("show");

                    window.getDatepicker();
                }
            });
        }
    };


    /** -------------------------------------------------------------------
     * Export Activites Datasheet
     * ----------------------------------------------------------------- */
         if (!window.export_activities_submit) {
            window.export_activities_submit = function () {

                var formEl = $("form#download_activityDatasheet_form");
                var statusDiv = $("#download_activityDatasheet_form_status");

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
                    FrmData.append('requestName', 'download_activityDatasheet');

                    $.ajax({
                        url: appUrl +'/activities/ajax',
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
                            console.log(rData);
                            let rObj = JSON.parse(rData);
                            if (rObj.status == "success") {
                                let newTab = window.open(rObj.datasheet_url);
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
     * Import Data Sheet Submit
     * ----------------------------------------------------------------- */
     if (!window.import_activityDataSheet_submit ) {
        window.import_activityDataSheet_submit = function () {
            var formEl = $("form#importActvity_form");
            var statusDiv = $("#download_importActvity_status");

            var FrmData = new FormData(formEl[0]);

            FrmData.append('requestName', 'import_Activity_DataSheet');

            $.ajax({
                url: appUrl + "/activities/ajax",
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function (xhr) {
                   // window.loadingScreen("show");

                    statusDiv.html("");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    //formEl[0].reset();
                    var sData = rData.replace( /[\r\n]+/gm, "" );

                    let rObj = JSON.parse(sData);

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
