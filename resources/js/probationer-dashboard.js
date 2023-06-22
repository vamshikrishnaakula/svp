"use strict";

/** -------------------------------------------------------------------
 * Get User Attendance
 * ----------------------------------------------------------------- */

if (!window.get_user_attendance) {
    window.get_user_attendance = function() {
        var FormEl = $("form#user_attendance_form");

        FormEl.find(".form-control").each(function () {
            $(this).removeClass("input-error");
        });

        var isValid = true;

        FormEl.find(".reqField").each(function () {
            if ($(this).val().trim() == "") {
                $(this).addClass("input-error");

                isValid = false;
            } else {
                $(this).removeClass("input-error");
            }
        });

        if (isValid == true) {

            var FrmData = new FormData(FormEl[0]);

            FrmData.append("requestName", "get_attendance_table");

            $.ajax({
                url: FormEl.attr("action"),
                data: FrmData,
                type: "POST",
                processData: false,
                contentType: false,
                beforeSend: function(xhr) {
                    window.loadingScreen("show");
                },
                success: function(rData) {
                    $("#user_attendance-container").html(rData);
                    window.loadingScreen("hide");
                }
            });
        }
    };
}

/** -------------------------------------------------------------------
 * Get User Timetable
 * ----------------------------------------------------------------- */

if (!window.get_user_timetable) {
    window.get_user_timetable = function() {
        var timetableSelector = $("#timetable_date").val();
        var data = {
            timetableSelector: timetableSelector,
            requestName: "get_timetable",
        };

        $.ajax({
            url: appUrl + "/user-ajax",
            data: data,
            type: "POST",
            // processData: false,
            // contentType: false,
            beforeSend: function(xhr) {
                window.loadingScreen("show");
            },
            success: function(rData) {
                window.loadingScreen("hide");
                $("#user_timetable-container").html(rData);
                window.loadingScreen("hide");
            }
        });
    };
}


/** -------------------------------------------------------------------
 * Get Monthly Session Table Data
* ----------------------------------------------------------------- */

if(!window.get_monthlysessions_table) {
    window.get_monthlysessions_table = function() {
        var monthYear = $("#monthlyreport_month");

        monthYear.removeClass("input-error");

        var isValid = true;

        if (monthYear.val().trim() == "") {
            monthYear.addClass("input-error");

            isValid = false;
        } else {
            monthYear.removeClass("input-error");
        }

        if (isValid == true) {

            var data = {
                month_Year: monthYear.val(),
                requestName: "get_monthlysession_data",
            };

            $.ajax({
                url: appUrl + "/user-ajax",
                data: data,
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $("#user_monthly_session_table").html(rData);
                    window.loadingScreen("hide");
                }
            });
        }
    };
}

/** -------------------------------------------------------------------
 * Get Missed Session Table Data
* ----------------------------------------------------------------- */

if(!window.get_missedsessions_table) {
    window.get_missedsessions_table = function() {
        var monthYear = $("#missedsession_month");

        monthYear.removeClass("input-error");

        var isValid = true;

        if (monthYear.val().trim() == "") {
            monthYear.addClass("input-error");

            isValid = false;
        } else {
            monthYear.removeClass("input-error");
        }

        if (isValid == true) {

            var data = {
                month_Year: monthYear.val(),
                requestName: "get_missedsession_data",
            };

            $.ajax({
                url: appUrl + "/user-ajax",
                data: data,
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $("#user_missed_session_table").html(rData);
                    window.loadingScreen("hide");
                }
            });
        }
    };
}


/** -------------------------------------------------------------------
 * Get Extra Session Table Data
 * ----------------------------------------------------------------- */

if (!window.get_extrasession_data) {
    window.get_extrasession_data = function() {
        var date = $("#extrasession_date").val();

        var data = {
            day: date,
            requestName: "get_extrasession_data",
        };

        $.ajax({
            url: appUrl + "/user-ajax",
            data: data,
            type: "POST",
            beforeSend: function (xhr) {
                window.loadingScreen("show");
            },
            success: function (rData) {
                window.loadingScreen("hide");
                $("#extrasession-table tbody").html(rData);
            }
        });
    };
}


/** -------------------------------------------------------------------
 * Get Sub-activity dropdown
 * ----------------------------------------------------------------- */

if (!window.get_pbSubactivityOptions) {
    window.get_pbSubactivityOptions = function (el, subActivityFieldId) {
        var ActivityId  = $(el).val();
        var data = {
        Activity_Id: ActivityId,
        requestName: "get_subactivities_options"
        };
        var actionUrl = appUrl + "/user-ajax";
        $.ajax({
        url: actionUrl,
        data: data,
        type: "POST",
        // processData: false,
        // contentType: false,
        beforeSend: function beforeSend(xhr) {
             window.loadingScreen("show");
            $("#" + subActivityFieldId).html('<option value="">Please wait...</option>');
        },
        success: function success(rData) {
             window.loadingScreen("hide");
            $("#" + subActivityFieldId).html(rData);
        }
        });
    };
}

/** -------------------------------------------------------------------
 * Get Set My Target Data View
 * ----------------------------------------------------------------- */

if (!window.get_mytarget_data) {
    window.get_mytarget_data = function() {

        var FormEl = $("#get_mytarget_form");

        FormEl.find(".form-control").each(function () {
            $(this).removeClass("input-error");
        });

        var isValid = true;

        FormEl.find(".reqField").each(function () {
            if ($(this).val().trim() == "") {
                $(this).addClass("input-error");

                isValid = false;
            } else {
                $(this).removeClass("input-error");
            }
        });

        if (isValid == true) {
            var activity = $("#pb_activity_id").val();
            var data = {
                activity_id: activity,
                requestName: "view_mytarget_data",
            };

            $.ajax({
                url: appUrl + "/user-ajax",
                data: data,
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");
                    $("#mytarget_table tbody").html(rData);
                }
            });
        }
    };
}



/** -------------------------------------------------------------------
 * Get Fitness Evaluation Data
 * ----------------------------------------------------------------- */

if (!window.get_fitness_data) {
    window.get_fitness_data = function() {
        var monthYear = $("#fitness_month").val();

        var data = {
            month_year: monthYear,
            requestName: "get_fitness_data",
        };

        $.ajax({
            url: appUrl + "/user-ajax",
            data: data,
            type: "POST",
            beforeSend: function (xhr) {
                window.loadingScreen("show");
            },
            success: function (rData) {
                window.loadingScreen("hide");
                $("#fitnessanalytics-data").html(rData);
            }
        });
    };
}

(function($) {
    $(function() {
        // Adjust goal on change
        $("#mytargetAccordion .targetsetInput").on("input", function(){
            var targetInputRow  = $(this).closest(".targetInputRow");
            var value = $(this).val();

            targetInputRow.find(".targetInput").val(value);

            var grade   = "";
            switch(parseInt(value)) {
                case 1:
                    grade = "E";
                    break;
                case 2:
                    grade = "D";
                    break;
                case 3:
                    grade = "C";
                    break;
                case 4:
                    grade = "B";
                    break;
                case 5:
                    grade = "A";
                    break;
            }

            targetInputRow.find("span.targetGrade").html(grade);
        });

        // Submit set goal form
        $("#mytargetAccordion .myTargetSubmit").on("click", function(){
            var mytargetForm = $(this).closest("form.mytargetForm");

            var FrmData = new FormData(mytargetForm[0]);
            FrmData.append("requestName", "myTargetSubmit");

            $.ajax({
                url: appUrl + "/user-ajax",
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
                    console.log(rObj);

                    if (rObj.status == "success") {
                        // activityForm[0].reset();

                        $("#successModalContent").html(
                            '<div class="msg msg-success msg-full">' + rObj.message + "</div>"
                        );
                        $("#successModal").modal("show");
                    } else {
                        console.log(rObj.message);
                    }
                }
            });
        });
    });

})(jQuery);
