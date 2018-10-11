function loadTable(margins = true) {
    var loadObj;
    if (!margins) {
        loadObj =
            {
                'margins': margins,
                'sold': false,
                'realty-type': 'Квартира',
            };
    } else {
        loadObj =
            {
                'margins': margins,
                'sold': false,
                'realty-type': 'Квартира',
                'price-min': $('#price-slider').slider('value')[0],
                'price-max': $('#price-slider').slider('value')[1],
                'area-min': $('#area-slider').slider('value')[0],
                'area-max': $('#area-slider').slider('value')[1],
                'date-min': $('#date-slider').slider('value')[0],
                'date-max': $('#date-slider').slider('value')[1]
                // district
                // isNew
            };
    }
    $.post("php/loadtable.php",
        loadObj,
        function (tableJson, textStatus, jqXHR) {
            $("#content-table tbody").empty();

            var tableArr = JSON.parse(tableJson);

            if (tableArr.margins) {
                $('#price-slider').slider({
                    min: parseInt(tableArr.margins.min_money),
                    max: parseInt(tableArr.margins.max_money),
                    step: 1,
                    orientation: 'horizontal',
                    value: [parseInt(tableArr.margins.min_money), parseInt(tableArr.margins.max_money)],
                    tooltip: 'show',
                    handle: 'triangle',
                    formater: function(value) {
                        return value;
                    }
                });
                $('#area-slider').slider({
                    min: parseInt(tableArr.margins.min_area),
                    max: parseInt(tableArr.margins.max_area),
                    step: 1,
                    orientation: 'horizontal',
                    value: [parseInt(tableArr.margins.min_area), parseInt(tableArr.margins.max_area)],
                    tooltip: 'show',
                    handle: 'triangle',
                    formater: function(value) {
                        return value;
                    }
                });
                $('#date-slider').slider({
                    min: parseInt(tableArr.margins.min_age),
                    max: parseInt(tableArr.margins.max_age),
                    step: 1,
                    orientation: 'horizontal',
                    value: [parseInt(tableArr.margins.min_age), parseInt(tableArr.margins.max_age)],
                    tooltip: 'show',
                    handle: 'triangle',
                    formater: function(value) {
                        return value;
                    }
                });
            }
            tableArr.table.forEach(function (tableRow, rowId) {
                $("#content-table tbody").append($("<tr></tr>"));
                $("#content-table tbody:last-child")
                    .append($("<td></td>").text(tableRow.realty_type))
                    .append($("<td></td>").text(tableRow.number_of_floors))
                    .append($("<td></td>").text(tableRow.floor))
                    .append($("<td></td>").text(tableRow.number_of_rooms))
                    .append($("<td></td>").text(tableRow.subway))
                    ;
            }, this);
        });
}
$(document).ready(function () {
    loadTable(false);
});