/****************** LISTENER **********************/

$(document).ready(function () {
    initGraphPhotoNumber();
});

/****************** FONCTION **********************/

/** Initialise le graph */
function initGraphPhotoNumber()
{
    $.ajax({
        url : '/xhr/admin/statistics',
        type : 'GET',
        success : function (res) {
            makePhotoChart(res.photo);
            makeCategoryChart(res.category);
        }
    });
}

function makePhotoChart(data)
{
    $('.current-month').text(data[4].count);

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

function makeCategoryChart(data)
{
    $('.category-offline').text(data[0]);
    $('.category-online').text(data[1]);

    var ctx = document.getElementById("article-online").getContext("2d");
    window.myDoughnut = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: data,
                backgroundColor: ["#fe5d70", "#01a9ac"],
                label: 'Dataset 1'
            }],
            labels: ["Hors ligne", "En ligne"]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            legend: {
                position: 'bottom',
            },
            title: {
                display: true,
                text: "",
            },
            animation: {
                animateScale: true,
                animateRotate: true
            }
        }
    });
}