"use strict";


(function ($) {
    /** -------------------------------------------------------------------
     * Get Import Data Modal
     * ----------------------------------------------------------------- */
    if (!window.getImportDataBtn) {
        window.getImportDataBtn = function () {

            console.log("get_import_data_form");

            $.ajax({
                url: appUrl +'/statistics/ajax',
                data: {
                    requestName: "get_import_data_form"
                },
                type: "POST",
                beforeSend: function (xhr) {
                    window.loadingScreen("show");
                },
                success: function (rData) {
                    window.loadingScreen("hide");

                    $("#dataImportModalTitle").html("Import Data");
                    $("#dataImportModalContent").html(rData);
                    $("#dataImportModalBtns").hide();
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
     * Download Data Sheet
     * ----------------------------------------------------------------- */
    if (!window.downloadDataSheet_Submit) {
        window.downloadDataSheet_Submit = function () {
            var formEl = $("form#downloadDataSheet_form");
            var statusDiv = $("#downloadDataSheet_status");

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
                FrmData.append('requestName', 'download_statistics_datasheet');

                $.ajax({
                    url: appUrl +'/statistics/ajax',
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

                            // window.saveCsvData(window.jsonObjectToCSV(rObj.data), rObj.file_name);
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
        };
    }

    /** -------------------------------------------------------------------
     * Import Data Sheet Submit
     * ----------------------------------------------------------------- */
     if (!window.importDataSheet_Submit) {
        window.importDataSheet_Submit = function () {
            var formEl = $("form#importDataSheet_form");
            var statusDiv = $("#importDataSheet_form_status");

            var FrmData = new FormData(formEl[0]);

            FrmData.append('requestName', 'import_DataSheet');

            $.ajax({
                url: appUrl + "/statistics/ajax",
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

})(jQuery);
