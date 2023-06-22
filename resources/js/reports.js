"use strict";

(function ($) {
    /** -------------------------------------------------------------------
     * Download Missed Classes Report
     * ----------------------------------------------------------------- */
     if (!window.download_missedClass_report) {
        window.download_missedClass_report = function () {
            var formEl = $("form#mised_class_report_form");
            var statusDiv = $("#mised_class_report_status");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".subactivity_id").removeClass("reqField");

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
                FrmData.append('requestName', 'download_missedClass_report');

                $.ajax({
                    url: appUrl +'/reports/ajax',
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
                            let newTab = window.open(rObj.report_url);
                        } else {
                            statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                        }
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger msg-full">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Download Extra Classes Report
     * ----------------------------------------------------------------- */
     if (!window.download_extraClass_report) {
        window.download_extraClass_report = function () {
            var formEl = $("form#extra_class_report_form");
            var statusDiv = $("#extra_class_report_status");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".subactivity_id").removeClass("reqField");

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
                FrmData.append('requestName', 'download_extraClass_report');

                $.ajax({
                    url: appUrl +'/reports/ajax',
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
                            let newTab = window.open(rObj.report_url);
                        } else {
                            statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                        }
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger msg-full">Fill all the required fields</div>');
            }
        };
    }

    /** -------------------------------------------------------------------
     * Download Pass Fail Report
     * ----------------------------------------------------------------- */
     if (!window.download_pass_fail_report) {
        window.download_pass_fail_report = function () {
            var formEl = $("form#pass_fail_report_form");
            var statusDiv = $("#pass_fail_report_status");

            formEl.find(".form-control").each(function () {
                $(this).removeClass("input-error");
            });

            var isValid = true;
            var firstError = "";
            var ErrorCount = 0;

            formEl.find(".squad_id").removeClass("reqField");

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
                FrmData.append('requestName', 'download_pass_fail_report');

                $.ajax({
                    url: appUrl +'/reports/ajax',
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
                            let newTab = window.open(rObj.report_url);
                        } else {
                            statusDiv.html('<div class="msg msg-danger msg-full">'+rObj.message+'</div>');
                        }
                    }
                });
            } else {
                firstError.focus();
                statusDiv.html('<div class="msg msg-danger msg-full">Fill all the required fields</div>');
            }
        };
    }
})(jQuery);
