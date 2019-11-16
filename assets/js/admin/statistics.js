/****************** LISTENER **********************/

$(document).ready(function () {
    initGraphPhotoNumber();
});

/****************** FONCTION **********************/

/** Initialise le graph */
function initGraphPhotoNumber()
{
    $.ajax({
        url : '/xhr/admin/statistics/photos',
        type : 'GET',
        success : function (res) {
            $('.current-month').text(res[4].count);
            makeBarChart(res);













        }
    });
}

function makeBarChart(data)
{
    var chart = AmCharts.makeChart("cpt-photo", {
        "type": "serial",
        "hideCredits": true,
        "theme": "light",
        "dataProvider": data,
        "valueAxes": [{
            "gridAlpha": 0.3,
            "gridColor": "#fff",
            "axisColor": "transparent",
            "color": '#fff',
            "dashLength": 0
        }],
        "gridAboveGraphs": true,
        "startDuration": 1,
        "graphs": [{
            "balloonText": "Nombre de photo: <b>[[value]]</b>",
            "fillAlphas": 1,
            "lineAlpha": 1,
            "lineColor": "#fff",
            "type": "column",
            "valueField": "count",
            "columnWidth": 0.5
        }],
        "chartCursor": {
            "categoryBalloonEnabled": false,
            "cursorAlpha": 0,
            "zoomable": false
        },
        "categoryField": "month",
        "categoryAxis": {
            "gridPosition": "start",
            "gridAlpha": 0,
            "axesAlpha": 0,
            "lineAlpha": 0,
            "fontSize": 12,
            "color": '#fff',
            "tickLength": 0
        }
    });
}