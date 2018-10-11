function loadFlat() {
    $.get("php/loadflat.php",
        function (flatJson, textStatus, jqXHR) {
            $("#flat-info").empty();
            $("#flat-description").empty();
            $("#realtor-info").empty();

            var flatArr = JSON.parse(flatJson);

            tableArr.forEach(function(tableRow, rowId) {
                $("#content-table tbody").append($("<tr></tr>").attr("id", rowId));
            
                tableRow.forEach(function(tableCell){
                    $("#content-table tbody #" + rowId.toString()).append($("<td></td>").text(tableCell));
                }, this);
            }, this);
        });
} 