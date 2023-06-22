var addUserCardHtml = `
    <div class="card">
        <div class="plus-container">
            <span class="plus user_card_add_btn">+</span>
            <span class="text">Add</span>
        </div>
    </div>
`;

/** -----------------------------------------------------------------------
 * Add user card
 */
$(document).on("click", "tr.user_card_row > td .user_card_add_btn", function() {
    var cTd = $(this).closest("td");
    var cTr = cTd.closest("tr");
    var tdIndex = cTd.index();

    var cUserCount = cTr.attr("data-user-count");
    var squadOptions = cTr.find("td:nth-child(2) select.squad_id").html();


    var userCardHtml = `
        <div class="card-body p-3">
            <div class="text-center">
                <img src="${appUrl+'/images/user_icon.png'}" alt="user icon" class="rounded-circle" width="100">
            </div>
            <div class="mt-5">
                <div class="form-group">
                    <select class="form-control squad_id">
                        ${squadOptions}
                    </select>
                </div>
                <div class="form-group mb-0">
                    <select class="form-control probationer_id">
                        <option value="">Select Probationer</option>
                    </select>
                </div>
            </div>
        </div>
        <span class="user_card_remove_btn">x</span>
    `;

    $(".user_card_remove_btn").addClass("hidden");
    $(this).closest("div.card").html(userCardHtml).parent().removeClass("user_card_add");
    cTr.find("td:nth-child(" + (parseInt(tdIndex) + 2) + ") .user_card_container").html(addUserCardHtml).addClass("user_card_add");

    cTr.attr("data-user-count", parseInt(cUserCount) + 1);
});

/** -----------------------------------------------------------------------
 * Remove user card
 */
$(document).on("click", "tr.user_card_row > td .user_card_remove_btn", function() {
    var cTd = $(this).closest("td");
    var cTr = cTd.closest("tr");
    var tdIndex = cTd.index();

    var cUserCount = cTr.attr("data-user-count");

    $(this).closest(".user_card_container").html(addUserCardHtml).addClass("user_card_add"); // Add user button to current cell
    cTr.find("td:nth-child(" + tdIndex + ") .user_card_remove_btn").removeClass("hidden"); // Display user_card_remove_btn to previous cell
    cTr.find("td:nth-child(" + (parseInt(tdIndex) + 2) + ") .user_card_container").html("").removeClass("user_card_add"); // remove add user button from next cell

    cTr.attr("data-user-count", parseInt(cUserCount) - 1);
});

// Get probationer compare data
window.get_probationer_compare_data = function() {
    // Probationer IDs
    var probationer_ids = new Array();
    var pb_count = 0;
    $("#pb_compare_table select.probationer_id").each(function() {
        probationer_ids.push($(this).val());

        if ($(this).val()) {
            pb_count++;
        }
    });
    if (pb_count == 0) {
        alert("Please select at least 2 probationers to compare.");
        return false;
    }

    // Activity IDs
    var activity_ids = new Array();
    var activity_count = 0;
    $("#fliter_section .activity-filter-btns .filter-btn.filter-btn-active").each(function() {
        activity_ids.push($(this).attr('data-activity-id'));

        activity_count++;
    });

    if (activity_count == 0) {
        $("#data_result_row td").html("");
    }

    $.ajax({
        url: appUrl + "/statistics/ajax",
        data: {
            probationer_ids: probationer_ids,
            activity_ids: activity_ids,
            requestName: "get_probationer_compare_data"
        },
        type: "POST",
        beforeSend: function(xhr) {
           // window.loadingScreen("show");
        },
        success: function(rData) {
            window.loadingScreen("hide");

            let rObj = JSON.parse(rData);
            if (rObj.status == "success") {
                $("#data_result_row td").html(rObj.data);
            } else {
                $("#data_result_row td").html("");
                alert(rObj.message);
            }
        }
    });
};

// Get compare data
$(document).on("click", "#compare_btn", function() {
    // Batch ID
    var batch_id = $("select#batch_id").val();

    // check if squad is selected
    $("#pb_compare_table select.squad_id").each(function() {
        if ($(this).val()) {
            $(this).removeClass("input-error");
        } else {
            $(this).addClass("input-error");
        }
    });

    // Probationer IDs
    var pb_count = 0;
    var inputError = 0;
    $("#pb_compare_table select.probationer_id").each(function() {
        if ($(this).val()) {
            $(this).removeClass("input-error");
            pb_count++;
        } else {
            $(this).addClass("input-error");
            inputError++;
        }
    });

    if (pb_count > 1 && inputError == 0) {

        // Get compare buttons
        $.ajax({
            url: appUrl + "/statistics/ajax",
            data: {
                batch_id: batch_id,
                requestName: "get_compare_filter_buttons"
            },
            type: "POST",
            beforeSend: function(xhr) {
                //
            },
            success: function(rData) {
                let rObj = JSON.parse(rData);
                if (rObj.status == "success") {
                    $("#fliter_section .activity-filter-btns").html(rObj.data);
                    $("#fliter_section .activity-filter-btns > div").append(`
                        <button type="button" data-toggle="collapse" class="filter-selectAll active col-sm-2 mb-2">Select all
                            <span class="close_icon close_icon_all" aria-hidden="true"><i class="fas fa-times"></i></span>
                        </button>
                    `);
                    // $("#fliter_section .other-filter-btns").show();

                    $(".filter_btns_row").show();

                    window.get_probationer_compare_data();
                } else {
                    alert(rObj.message);
                }
            }
        });
    } else {
        if (pb_count < 2) {
            alert("Please select at least 2 probationers to compare.");
        } else if (inputError > 0) {
            alert("Please select probationers.");
        }
    }
});


$(document).on("click", "#fliter_section .filter-btn.inactive", function() {
    $(this).addClass("filter-btn-active").removeClass("inactive");

    // Activate / Inactivate Select All button
    if ($("#fliter_section .filter-btn").length == $("#fliter_section .filter-btn.filter-btn-active").length) {
        $("#fliter_section .filter-selectAll").addClass('active').removeClass("inactive");
    } else {
        $("#fliter_section .filter-selectAll").addClass('inactive').removeClass("active");
    }

    window.get_probationer_compare_data();
});

$(document).on("click", "#fliter_section .filter-btn.filter-btn-active .close_icon", function() {
    $(this).closest(".filter-btn").removeClass("filter-btn-active").addClass("inactive");

    // Activate / Inactivate Select All button
    if ($("#fliter_section .filter-btn").length == $("#fliter_section .filter-btn.filter-btn-active").length) {
        $("#fliter_section .filter-selectAll").addClass('active').removeClass("inactive");
    } else {
        $("#fliter_section .filter-selectAll").addClass('inactive').removeClass("active");
    }

    window.get_probationer_compare_data();
});

/** Select All**/
$(document).on("click", "#fliter_section .filter-selectAll.inactive", function() {
    $("#fliter_section .filter-btn").addClass("filter-btn-active").removeClass("inactive");
    $("#fliter_section .filter-selectAll").addClass('active').removeClass("inactive");

    window.get_probationer_compare_data();
});

$(document).on("click", "#fliter_section .close_icon_all", function() {
    $("#fliter_section .filter-btn").removeClass("filter-btn-active").addClass("inactive");
    $("#fliter_section .filter-selectAll").addClass('inactive').removeClass("active");

    window.get_probationer_compare_data();
});
