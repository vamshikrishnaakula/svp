/* ---------------------- Submit Get Squad Form ----------------------- */
$("form#get_squads_form").on("submit", function (e) {
    e.preventDefault();

    var formObj = $(this);

    var FrmData = new FormData(formObj[0]);
    FrmData.append("requestName", "get_squads");

    $.ajax({
        url: appUrl + "/faculty-ajax",
        data: FrmData,
        type: "POST",
        processData: false,
        contentType: false,
        beforeSend: function (xhr) {
            window.loadingScreen("show");
        },
        success: function (rData) {
            window.loadingScreen("hide");

            $("#squad_list").html(rData);
        }
    });
});

/* ---------------------- Submit Get Probationers Form ----------------------- */
$("form#get_probationers_form").on("submit", function (e) {
    e.preventDefault();

    var formObj = $(this);

    var FrmData = new FormData(formObj[0]);
    FrmData.append("requestName", "get_probationers");

    $.ajax({
        url: appUrl + "/faculty-ajax",
        data: FrmData,
        type: "POST",
        processData: false,
        contentType: false,
        beforeSend: function (xhr) {
            window.loadingScreen("show");
        },
        success: function (rData) {
            window.loadingScreen("hide");

            $("#probationers_list").html(rData);

            $('#probationersTable').DataTable({
                "bLengthChange": false,
                language: { search: "", searchPlaceholder: "Search..." }
            });
        }
    });
});

if(!window.get_squad_probationers) {
    window.get_squad_probationers = function(squad_id) {
        var data    = {
            squad_id: squad_id,
            requestName: "get_squad_probationers"
        }

        $.ajax({
            url: appUrl + "/faculty-ajax",
            data: data,
            type: "POST",
            beforeSend: function (xhr) {
                window.loadingScreen("show");
                $("#probationersModalContent").html("");
            },
            success: function (rData) {
                window.loadingScreen("hide");

                $("#probationersModalContent").html(rData);
                $("#probationersModal").modal("show");
            }
        });
    }
}

/* ---------------------- Get List of Activities Table on Batch Select --------------- */
if (!window.get_activities_by_batch) {
    window.get_activities_by_batch = function (el) {
        var batchId = $(el).val();
        // console.log("Batch Id: "+ batchId);

        var data = {
            batch_id: batchId,
            requestName: "get_activities_by_batch"
        };

        $.ajax({
            url: appUrl + "/faculty-ajax",
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
