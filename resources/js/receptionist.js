"use strict";
if(!window.getData)
{
    window.getData = function() {
        var id = $('#prob_id').val();
       // var token = $('#token').val();
        $.ajax({
            url: '/getprobationerdata',
            type: "POST",
            data: {
               // "_token": token,
                "id": id,
            },
            success: function (data) {
                if (jQuery.isEmptyObject(data)) {
                    $('#Pname').val('');
                    $('#gender').val('');
                    $('#error').empty();
                    var e = $('<div class="alert alert-danger"><p>Please Enter valid Roll Number</p></div>');
                    $('#error').append(e);
                    $("div.alert").fadeTo(2000, 500).slideUp(500, function(){
                    $("div.alert").slideUp(500);
                });
                } else {
                    $('#Pname').text(data.Name);
                    $('#gender').text(data.gender);
                    $('#ap').val(data.id);
                }

            }
        })
    }
}

// Disable past dates

$(".reception_date").datetimepicker({minDate: new Date()});
