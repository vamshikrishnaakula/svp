/** -------------------------------------------------------------------
 * Get Import Fitness Data Modal
 * ----------------------------------------------------------------- */
if (!window.get_fitnessData_import_modal) {
    window.get_fitnessData_import_modal = function () {

        $.ajax({
            url: appUrl + '/fitness/ajax',
            data: {
                requestName: "get_fitnessData_import_modal"
            },
            type: "POST",
            beforeSend: function (xhr) {
                window.loadingScreen("show");
            },
            success: function (rData) {
                window.loadingScreen("hide");

                $("#dataImportModalTitle").html("Import Fitness Data");
                $("#dataImportModalContent").html(rData);
                $("#dataImportModalBtns").hide();
                // $("#dataImportModal .modal-dialog").addClass("modal-lg");
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
 * Download Fitness Datasheet
 * ----------------------------------------------------------------- */
if (!window.download_fitnessDatasheet) {
    window.download_fitnessDatasheet = function () {

        var formEl = $("form#download_fitnessDatasheet_form");
        var statusDiv = $("#download_fitnessDatasheet_status");

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
            FrmData.append('requestName', 'download_fitnessDatasheet');

            $.ajax({
                url: appUrl + '/fitness/ajax',
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
                    } else {
                        statusDiv.html('<div class="msg msg-danger msg-full">' + rObj.message + '</div>');
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
     * Import Fitness Data Form Submit
     * ----------------------------------------------------------------- */
 if (!window.importFitnessData_submit) {
    window.importFitnessData_submit = function() {
        var statusDiv = $("#importFitnessData_status");

        var importFitnessDataForm = $("form#importFitnessData_form");
        var FrmData = new FormData(importFitnessDataForm[0]);

        FrmData.append('requestName', 'import_FitnessData');

        var actionUrl = appUrl + "/fitness/ajax";

        $.ajax({
            url: actionUrl,
            data: FrmData,
            type: "POST",
            processData: false,
            contentType: false,
            beforeSend: function(xhr) {
                //window.loadingScreen("show");

                statusDiv.html("");
            },
            success: function(rData) {
                window.loadingScreen("hide");
                importFitnessDataForm[0].reset();
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
