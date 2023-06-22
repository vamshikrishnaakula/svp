if (!window.tab1_To_tab2) {
    window.tab1_To_tab2 = function() {
        var table1 = document.getElementById("prob_list"),
            table2 = document.getElementById("Addedprob"),
            checkboxes = document.getElementsByName("check-tab1");
        var rowCount1 = $('#Addedprob tbody tr').length;
        var rowCount = $('#Addedprob tbody tr').length;
        if (rowCount == '0') {
            $('#Addedprob tbody').append('<tr></tr>');
        }

        for (var i = 0; i < checkboxes.length; i++)
            if (checkboxes[i].checked) {
                var newRow = table2.insertRow(table2.length),
                    cell1 = newRow.insertCell(0),
                    cell2 = newRow.insertCell(1),
                    cell3 = newRow.insertCell(2),
                    cell4 = newRow.insertCell(3);
                cell5 = newRow.insertCell(4);
                cell6 = newRow.insertCell(5);
                // console.log(newRow);
                // add values to the cells
                cell1.innerHTML = "<input type='checkbox' class='form-control' name='check-tab2'>";
                cell2.innerHTML = table1.rows[i + 1].cells[1].innerHTML;
                cell3.innerHTML = table1.rows[i + 1].cells[2].innerHTML;
                cell4.innerHTML = table1.rows[i + 1].cells[3].innerHTML;
                cell5.innerHTML = table1.rows[i + 1].cells[4].innerHTML;
                cell6.innerHTML = '<input type= hidden value=' + table1.rows[i + 1].cells[4].innerHTML + ' name=pid[] />';

                // remove the transfered rows from the first table [table1]
                var index = table1.rows[i + 1].rowIndex;
                table1.deleteRow(index);
                // we have deleted some rows so the checkboxes.length have changed
                // so we have to decrement the value of i
                i--;
                //  console.log(checkboxes.length);
            }
        $("tr:empty").remove();
    }
}

$('#Batch_Id').change(function() {
    var batch = $('#Batch_Id').val();
    var token = $('#token').val();
    $.ajax({
        url: '/batchwiseprob',
        type: "POST",
        data: {
            "_token": token,
            "id": batch,
        },
        beforeSend: function (xhr) {
            window.loadingScreen("show");
        },
        success: function(data) {
            window.loadingScreen("hide");
            var j = 1;
            $('#Addedprob td').remove();
            $('#prob_list tbody tr').remove();
            $.each(data, function(i) {
                var tr_str = "<tr>" +
                    "<td> <input type='checkbox' class='form-control' name = 'check-tab1' /></td>" +
                    "<td align='center'>" + data[i].BatchName + "</td>" +
                    "<td align='center'>" + data[i].RollNumber + "</td>" +
                    "<td align='center'>" + data[i].Name + "</td>" +
                    "<td align='center'>" + data[i].id + "</td>" +
                    "</tr>";
                $("#prob_list tbody").append(tr_str);
            });
        }
    })
});

if (!window.tab1_To_tab2) {
    window.tab1_To_tab2 = function() {

        var table1 = document.getElementById("prob_list"),
            table2 = document.getElementById("Addedprob"),
            checkboxes = document.getElementsByName("check-tab1");
        var rowCount1 = $('#Addedprob tbody tr').length;
        var rowCount = $('#Addedprob tbody tr').length;
        if (rowCount == '0') {
            $('#Addedprob tbody').append('<tr></tr>');
        }

        for (var i = 0; i < checkboxes.length; i++)
            if (checkboxes[i].checked) {
                var newRow = table2.insertRow(table2.length),
                    cell1 = newRow.insertCell(0),
                    cell2 = newRow.insertCell(1),
                    cell3 = newRow.insertCell(2),
                    cell4 = newRow.insertCell(3);
                cell5 = newRow.insertCell(4);
                cell6 = newRow.insertCell(5);
                // console.log(newRow);
                // add values to the cells
                cell1.innerHTML = "<input type='checkbox' class='form-control' name='check-tab2'>";
                cell2.innerHTML = table1.rows[i + 1].cells[1].innerHTML;
                cell3.innerHTML = table1.rows[i + 1].cells[2].innerHTML;
                cell4.innerHTML = table1.rows[i + 1].cells[3].innerHTML;
                cell5.innerHTML = table1.rows[i + 1].cells[4].innerHTML;
                cell6.innerHTML = '<input type= hidden value=' + table1.rows[i + 1].cells[4].innerHTML + ' name=pid[] />';

                // remove the transfered rows from the first table [table1]
                var index = table1.rows[i + 1].rowIndex;
                table1.deleteRow(index);
                // we have deleted some rows so the checkboxes.length have changed
                // so we have to decrement the value of i
                i--;
                //  console.log(checkboxes.length);
            }
        $("tr:empty").remove();
    };

}

if (!window.tab2_To_tab1) {
    window.tab2_To_tab1 = function() {

        var table1 = document.getElementById("prob_list"),
            table2 = document.getElementById("Addedprob"),
            checkboxes = document.getElementsByName("check-tab2");

        var rowCount = $('#prob_list tbody tr').length;
        if (rowCount == '0') {
            $('#prob_list tbody').append('<tr></tr>');
        }
        for (var i = 0; i < checkboxes.length; i++)
            if (checkboxes[i].checked) {
                var newRow = table1.insertRow(table1.length),
                    cell1 = newRow.insertCell(0),
                    cell2 = newRow.insertCell(1),
                    cell3 = newRow.insertCell(2),
                    cell4 = newRow.insertCell(3);
                cell5 = newRow.insertCell(4);

                // add values to the cells
                cell1.innerHTML = "<input type='checkbox' class='form-control' name='check-tab1'>";
                cell2.innerHTML = table2.rows[i + 1].cells[1].innerHTML;
                cell3.innerHTML = table2.rows[i + 1].cells[2].innerHTML;
                cell4.innerHTML = table2.rows[i + 1].cells[3].innerHTML;
                cell5.innerHTML = table2.rows[i + 1].cells[4].innerHTML;


                // remove the transfered rows from the second table [table2]
                var index = table2.rows[i + 1].rowIndex;
                table2.deleteRow(index);
                // we have deleted some rows so the checkboxes.length have changed
                // so we have to decrement the value of i
                i--;
            }
        $("tr:empty").remove();
    };
}

$("#search").keyup(function() {
    var value = this.value.toLowerCase().trim();

    $("#prob_list tr").each(function(index) {
        if (!index) return;
        $(this).find("td").each(function() {
            var id = $(this).text().toLowerCase().trim();
            var not_found = (id.indexOf(value) == -1);
            $(this).closest('tr').toggle(!not_found);
            return not_found;
        });
    });
});

// $('#Batch_Id').change(function() {
//     var batch = $('#Batch_Id').val();
//     var token = $('#token').val();
//     $.ajax({
//         url: '/batchwiseprob',
//         type: "POST",
//         data: {
//             "_token": token,
//             "id": batch,
//         },
//         success: function(data) {
//             var j = 1;
//             $('#Addedprob td').remove();
//             $('#prob_list tbody tr').remove();
//             $.each(data, function(i) {
//                 var tr_str = "<tr>" +
//                     "<td> <input type='checkbox' class='form-control' name = 'check-tab1' /></td>" +
//                     "<td align='center'>" + data[i].BatchName + "</td>" +
//                     "<td align='center'>" + data[i].RollNumber + "</td>" +
//                     "<td align='center'>" + data[i].Name + "</td>" +
//                     "<td align='center'>" + data[i].id + "</td>" +
//                     "</tr>";
//                 $("#prob_list tbody").append(tr_str);
//             });
//         }
//     });
// });
