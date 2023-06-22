"use strict";

$("document").ready(function() {
    $("div.alert").fadeTo(2000, 500).slideUp(500, function() {
        $("div.alert").slideUp(500);
    });
});

$(function() {
    $(".date").datetimepicker();
    $(".time").datetimepicker({
        format: "LT"
    });

    $(".datePicker").datepicker({
        dateFormat: "yy-mm-dd"
    });


    // Get jquery time picker on input
    $(document).on("click", "input.jquery-timepicker", function() {
        window.getTimepicker($(this));
    });
    $(document).on("input", "input.jquery-timepicker", function() {
        window.getTimepicker($(this));
    });

    // Get jquery time input mask
    $(document).on("click", "input.jquery-timeinput-mask", function() {
        window.timeInputMask($(this));
    });
    $(document).on("input", "input.jquery-timeinput-mask", function() {
        window.timeInputMask($(this));
    });

    // Get jquery timerange input mask
    $(document).on("click", "input.jquery-timerange-mask", function() {
        window.timerangeMask($(this));
    });
    $(document).on("input", "input.jquery-timerange-mask", function() {
        window.timerangeMask($(this));
    });

    /** ----------------------------------------------------------------
     * jQuery Seahc in table
     * -------------------------------------------------------------- */
    $(document).on("keyup", "input.searchInTable", function() {
        var value = $(this).val().toLowerCase().trim();
        var table = $(this).attr("data-table");

        $("table#" + table + " tbody tr").each(function() {
            $(this).find("td").each(function() {
                var id = $(this).text().toLowerCase().trim();
                var not_found = (id.indexOf(value) == -1);
                $(this).closest('tr').toggle(!not_found);
                return not_found;
            });
        });
    });

    /** ----------------------------------------------------------------
     * jQuery sortable table row
     * -------------------------------------------------------------- */
    $(document).on("mouseover", "table.table-sortable tbody", function() {
        var tbody = $(this);
        tbody.addClass("cursor-move");

        // tbody.sortable({
        //     axis: 'y',
        //     stop: function (event, ui) {
        //         tbody.find('tr').each(function(index){
        //             $(this).find("input.pb_position_number").val(index+1);
        //         });
        //     }
        // });

        window.generatePositionNumber(tbody);
    });
});

/** ------------------------------------------------------------------------------
 * Jquery Time Mask Input
 * ---------------------------------------------------------------------------- */
if (!window.timeInputMask) {
    window.timeInputMask = function(el) {
        $(el).inputmask({
            mask: "99:99",
            insertMode: false,
            showMaskOnHover: false,
        });
    };
}

/** ------------------------------------------------------------------------------
 * Jquery Time Range Mask Input
 * ---------------------------------------------------------------------------- */
if (!window.timerangeMask) {
    window.timerangeMask = function(el) {
        $(el).inputmask({
            mask: "99:99 - 99:99",
            showMaskOnHover: false,
            showMaskOnFocus: false,
        });
    };
}

/** ------------------------------------------------------------------------------
 * Jquery Datepicker
 * ---------------------------------------------------------------------------- */
if (!window.getDatepicker) {
    window.getDatepicker = function() {
        $(".datePicker").datepicker({
            dateFormat: "yy-mm-dd"
        });
    };
}

/** ------------------------------------------------------------------------------
 * Jquery Timepicker
 * ---------------------------------------------------------------------------- */
if (!window.getTimepicker) {
    window.getTimepicker = function(el) {
        $(el).timepicker({
            scrollDefault: "08:30"
        });

        $(el).inputmask({
            mask: "99:99"
        });

        $(el).focus();
    };
}

/** ------------------------------------------------------------------------------
 * jQuery sortable table row
 * ---------------------------------------------------------------------------- */
if (!window.generatePositionNumber) {
    window.generatePositionNumber = function(tbody) {
        tbody.sortable({
            axis: 'y',
            stop: function(event, ui) {
                tbody.find('tr').each(function(index) {
                    $(this).find("input.pb_position_number").val(index + 1);
                });
            }
        });
    };
}

/** ------------------------------------------------------------------------------
 * Success Modal Reset on close
 * ---------------------------------------------------------------------------- */
$(function() {
    $("#successModal").on("hidden.bs.modal", function(e) {
        $(this).load(location.href + " #successModal>*");
    });
});

$("#sidebar-wrapper").on("mouseenter", function() {
    $("#wrapper").addClass("toggled");
    $("#subwrapper").addClass("toggled");
    $(this).css("border", "none");
});

$("#sidebar-wrapper").on("mouseleave", function() {
    $("#wrapper").removeClass("toggled");
    $("#subwrapper").removeClass("toggled");
    $(this).css("border-right", "1px solid #fff");
    $("#sidebar-wrapper ul li ul").removeClass("show");
});

// $("#sidebar-wrapper ul .usermanagement").on("click", function() {
//     $("#sidebar-wrapper ul li ul").removeClass("active");
//     $("#sidebar-wrapper ul .usermanagement ul").toggleClass("active");
// });

// $("#sidebar-wrapper ul .activities").on("click", function() {
//     $("#sidebar-wrapper ul li ul").removeClass("active");
//     $("#sidebar-wrapper ul .activities ul").toggleClass("active");
// });

// $("#sidebar-wrapper ul .events").on("click", function() {
//     $("#sidebar-wrapper ul li ul").removeClass("active");
//     $("#sidebar-wrapper ul .events ul").toggleClass("active");
// });

// $("#sidebar-wrapper ul .attendance").on("click", function() {
//     $("#sidebar-wrapper ul li ul").removeClass("active");
//     $("#sidebar-wrapper ul .attendance ul").toggleClass("active");
// });

// $(document).ready(function() {
//     $("#sidebar-wrapper ul li:first").addClass("active");
//     $(".tab-content:not(:first)").hide();
//     $("#sidebar-wrapper ul li ul li a").click(function(event) {
//         $("#sidebar-wrapper ul li").removeClass("active");
//         $(this)
//             .parents()
//             .addClass("active");
//         $(this)
//             .parents()
//             .siblings()
//             .removeClass("active");
//     });
// });

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

/** ------------------------------------------------------------------------------
 * On Load squads dropdown option
 * ---------------------------------------------------------------------------- */
 $(document).ready(function () {
        var batchId = $('#batch_id').val();
        var activityId = $('#activity_id');

        if (activityId.length > 0) {
            window.get_activitiesDropdownOptions(batchId);
        }
        var data = {
            batch_id: batchId
        };
        var actionUrl = appUrl + "/squadDropdownOptions";
        $.ajax({
            url: actionUrl,
            data: data,
            type: "POST",
            beforeSend: function(xhr) {
                $("#squad_id").html(
                    '<option value="">Please wait...</option>'
                );
            },
            success: function(rData) {
                $("#squad_id").html(rData);
               // $("#data_squad_id").html(rData);
            }
        });
    });


/** ------------------------------------------------------------------------------
 * On Select Batch change
 * ---------------------------------------------------------------------------- */
if (!window.select_batchId_changed) {
    window.select_batchId_changed = function(el, squadFieldId) {
        var batchId = $(el).val();
        var activityId = $('#activity_id');

        if (activityId.length > 0) {
            window.get_activitiesDropdownOptions(batchId);
        }

        if (batchId.length > 0) {
            // console.log("Batch Id: "+ batchId);

            window.get_squadDropdownOptions(batchId, squadFieldId);
        }
    };
}

/** ------------------------------------------------------------------------------
 * Get Squad Dropdown Options
 * ---------------------------------------------------------------------------- */
if (!window.get_squadDropdownOptions) {
    window.get_squadDropdownOptions = function(batchId, fieldId) {
        var data = {
            batch_id: batchId
        };

        var actionUrl = appUrl + "/squadDropdownOptions";

        $.ajax({
            url: actionUrl,
            data: data,
            type: "POST",
            // processData: false,
            // contentType: false,
            beforeSend: function(xhr) {
                // window.loadingScreen("show");

                $("#" + fieldId).html(
                    '<option value="">Please wait...</option>'
                );
            },
            success: function(rData) {
                // window.loadingScreen("hide");

                $("#" + fieldId).html(rData);
            }
        });
    };
}

/** ------------------------------------------------------------------------------
 * Get Activities Dropdown Options
 * ---------------------------------------------------------------------------- */
if (!window.get_activitiesDropdownOptions) {
    window.get_activitiesDropdownOptions = function(batchId) {

        var data = {
            batch_id: batchId
        };

        var actionUrl = appUrl + "/activitiesDropdownOptions";

        $.ajax({
            url: actionUrl,
            data: data,
            type: "POST",
            // processData: false,
            // contentType: false,
            beforeSend: function(xhr) {
                // window.loadingScreen("show");

                $("#activity_id").html(
                    '<option value="">Please wait...</option>'
                );
            },
            success: function(rData) {
                // window.loadingScreen("hide");

                $("#activity_id").html(rData);
            }
        });
    };
}

/** ------------------------------------------------------------------------------
 * Get Probationers Dropdown Options
 * ---------------------------------------------------------------------------- */
if (!window.get_probationerDropdownOptions) {
    window.get_probationerDropdownOptions = function(el, targetFieldId) {

        var SquadId = $(el).val();
        var data = {
            SquadId: SquadId
        };

        var actionUrl = appUrl + "/probationerDropdownOptions";

        $.ajax({
            url: actionUrl,
            data: data,
            type: "POST",
            // processData: false,
            // contentType: false,
            beforeSend: function(xhr) {
                // window.loadingScreen("show");

                $("#" + targetFieldId).html(
                    '<option value="">Please wait...</option>'
                );
            },
            success: function(rData) {
                // window.loadingScreen("hide");

                $("#" + targetFieldId).html(rData);
            }
        });
    };
}

$(function() {
    $("#familyhistory  input[type=checkbox]").click(function() {
        $(this).toggleClass("active");
        $(this)
            .siblings()
            .removeClass("active");
    });
});

// $(".addDependents").click(function() {
//     //
//     $(".dependents").append(
//         "<form><div class='row'><div class='col-md-2'><label>Name :</label><input type='text' class='form-control' /></div><div class='col-md-3'><label>Age :</label><input type='text' class='form-control' /></div><div class='col-md-2'><label>Gender :</label><input type='text' class='form-control' /></div><div class='col-md-3'><label>Relationship :</label><input type='text' class='form-control' /></div><a class='deleteDependents col-md-1'>Delete</a><a class='col-md-1 submitDependent'>Submit</a></div></form>"
//     );
// });

$(function() {
    $(".dependents").on("click", "  a.deleteDependents", function() {
        //
        $(this)
            .parent()
            .remove();
    });
});

$(document).ready(function() {
    $(document).on(
        "click",
        ".evaluvation .table tbody tr td a.editvalue",
        function() {
            //
            var dad = $(this)
                .parent("td")
                .siblings();
            dad.children("label").hide();
            var placeholder = dad.children("label").text();
            var div = dad.children("div");
            div.show();
            var input = dad.find('input[type="text"]');
            input.focus();
            input.attr("placeholder", placeholder);
            $(this).hide();
        }
    );

    // $("input[type=text]").focusout(function() {
    //     var dad = $(this).parent();
    //     dad.hide();
    //     dad.siblings("label").show();
    // });

    $(document).on(
        "click",
        ".evaluvation .table tbody tr td div a.update",
        function() {
            //
            var dad = $(this).parent();
            var input = dad.children("div input");

            var inputvalue = $(input).val();
            dad.siblings("label")
                .text(inputvalue)
                .show();
            dad.parents()
                .find("a")
                .show();
            dad.hide();
        }
    );
});

// $(document).ready(function() {
//     var InputsWrapper = $("#InputsWrapper"); //Input boxes wrapper ID
//     var InputsWrapperComponent = $("#InputsWrapperComponent");
//     var AddButton = $("#AddMoreFileBox"); //Add button ID
//     var AddComponent = $("#Addcomponent");
//     var x = InputsWrapper.length; //initlal text box count
//     var FieldCount = 1; //to keep track of text box added
//     var ComponentFieldCount = 1;

//     //on add input button click
//     $(AddButton).click(function(e) {
//         //max input box allowed

//         FieldCount++; //text box added ncrement
//         //add input box
//         $(InputsWrapper).append(
//             '<div class="addinput"><input type="text" class="form-control" id="sub_activity_' +
//                 FieldCount +
//                 '"/> <a href="#" class="removeclass">Remove</a></div>'
//         );
//         x++; //text box increment

//         $("#Addcomponent").show();

//         return false;
//     });

//     $(AddComponent).click(function(e) {
//         //max input box allowed

//         ComponentFieldCount++; //text box added ncrement
//         //add input box
//         $(InputsWrapperComponent).append(
//             '<div class="addinput"><input type="text" class="form-control" id="component_field_' +
//                 ComponentFieldCount +
//                 '"/> <a href="#" class="removeclass">Remove</a></div>'
//         );
//         x++; //text box increment

//         $("#AddMoreFileId").show();

//         return false;
//     });

//     $("body").on("click", ".removeclass", function(e) {
//         //user click on remove text
//         if (x > 1) {
//             $(this)
//                 .parent("div")
//                 .remove(); //remove text box
//             x--; //decrement textbox

//             $("#AddMoreFileId").show();
//         }
//         return false;
//     });
// });

/** ------------------------------------------------------------------------------
 * Jquery Mask Input
 * ---------------------------------------------------------------------------- */
if (!window.loadingScreen) {
    window.loadingScreen = function(Action) {
        if (Action == "show") {
            $("#wrapper").addClass("ajax-processing");
        }
        if (Action == "hide") {
            $("#wrapper").removeClass("ajax-processing");
        }
    };
}


/** ------------------------------------------------------------------------------
 * On Select Activity change
 * ---------------------------------------------------------------------------- */
if (!window.select_activityId_changed) {
    window.select_activityId_changed = function(el, SubActivityFieldId) {
        var ActivityId = $(el).val();
        if (ActivityId.length > 0) {
            // console.log("Batch Id: "+ batchId);
            window.get_subactivityDropdownOptions(ActivityId, SubActivityFieldId);
        }
    };
}

/** ------------------------------------------------------------------------------
 * Get Sub Activities Dropdown Options
 * ---------------------------------------------------------------------------- */
if (!window.get_subactivityDropdownOptions) {
    window.get_subactivityDropdownOptions = function(ActivityId, SubActivityFieldId) {

        var data = {
            Activity_Id: ActivityId
        };

        var actionUrl = appUrl + "/subactivitiesDropdownOptions";

        $.ajax({
            url: actionUrl,
            data: data,
            type: "POST",
            // processData: false,
            // contentType: false,
            beforeSend: function(xhr) {
                 window.loadingScreen("show");

                $("#" + SubActivityFieldId).html(
                    '<option value="">Please wait...</option>'
                );
            },
            success: function(rData) {
                 window.loadingScreen("hide");

                $("#" + SubActivityFieldId).html(rData);
            }
        });
    };
}

/** ------------------------------------------------------------------------------
 * On Select Activity change
 * ---------------------------------------------------------------------------- */
if (!window.select_sub_activityId_changed) {
    window.select_sub_activityId_changed = function(el, ComponentFieldId) {
        var SubActivityId = $(el).val();
        if (SubActivityId.length > 0) {
            // console.log("Batch Id: "+ batchId);
            window.get_componentDropdownOptions(SubActivityId, ComponentFieldId);
        }
    };
}

/** ------------------------------------------------------------------------------
 * Get Compnents Dropdown Options
 * ---------------------------------------------------------------------------- */
if (!window.get_componentDropdownOptions) {
    window.get_componentDropdownOptions = function(SubActivityId, ComponentFieldId) {

        var data = {
            SubActivityId: SubActivityId
        };

        var actionUrl = appUrl + "/componentsDropdownOptions";

        $.ajax({
            url: actionUrl,
            data: data,
            type: "POST",
            // processData: false,
            // contentType: false,
            beforeSend: function(xhr) {
                 window.loadingScreen("show");

                $("#" + ComponentFieldId).html(
                    '<option value="">Please wait...</option>'
                );
            },
            success: function(rData) {
                 window.loadingScreen("hide");

                $("#" + ComponentFieldId).html(rData);
            }
        });
    };
}


/** ------------------------------------------------------------------------------
 * Get Probationer drop down Options
 * ---------------------------------------------------------------------------- */

if (!window.select_squad_id_changed) {
    window.select_squad_id_changed = function(el, ProbationerFieldId) {
        var SquadId = $(el).val();
        if (SquadId.length > 0) {
            window.get_probationerDropdownOptions(el, ProbationerFieldId);
        }
    };
}

/** ------------------------------------------------------------------------------
 * Convert Json Object to CSV
 * ---------------------------------------------------------------------------- */
if (!window.jsonObjectToCSV) {
    window.jsonObjectToCSV = function(objArray) {
        var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
        var str = '';

        for (var i = 0; i < array.length; i++) {
            var line = '';
            for (var index in array[i]) {
                if (line != '') line += ','

                line += array[i][index];
            }
            str += line + '\r\n';
        }

        return str;
    }
};

/** ------------------------------------------------------------------------------
 * Convert Json Object to CSV
 * ---------------------------------------------------------------------------- */
if (!window.saveCsvData) {
    window.saveCsvData = function(data, fileName) {
        const a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";

        // return function (data, fileName) {
        const blob = new Blob([data], { type: "octet/stream" }),
            url = window.URL.createObjectURL(blob);
        a.href = url;
        a.download = fileName;
        a.click();
        window.URL.revokeObjectURL(url);
        // };
    }
};




