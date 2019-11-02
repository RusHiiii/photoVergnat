/****************** LISTENER **********************/

/** Initialisation de la modal */
$('#seasons-table tbody').on('click', '.edit', function (e) {
    var id = $(this).data('id');
    $.ajax({
        url : `/xhr/admin/season/display/edit/${id}`,
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation de la modal */
$('.season .add').on('click', function (e) {
    $.ajax({
        url : '/xhr/admin/season/display/create/',
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initilisation des modals de suppression */
$('#seasons-table tbody').on('click', '.alert-ajax', function (e) {
    var table = $('#seasons-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « " + $(`#season_${id}`).children('.title').text() + " »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : `/xhr/admin/season/remove/${id}`,
            type : 'DELETE',
            statusCode: {
                403: function () {
                    swal('Action interdite !');
                },
            },
            success : function () {
                table
                    .row($(`#season_${id}`))
                    .remove()
                    .draw();
                swal('Suppression terminée !');
            }
        });
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#create-season', function (e) {
    e.preventDefault();

    $.addSpinner('.create-season');

    $.ajax({
        url : '/xhr/admin/season/create',
        type : 'POST',
        data : {
            'season': $('#create-season').serializeObject()
        },
        dataType:'json',
        statusCode: {
            403: function () {
                swal('Action interdite !');
            },
        },
        success : function (res) {
            addRow(JSON.parse(res));
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-create');
        },
        complete: function () {
            $.removeSpinner('.create-season', 'Valider');
        }
    });
});

/** Initialisation formualire de MàJ */
$('body').on('submit', '#update-season', function (e) {
    e.preventDefault();

    $.addSpinner('.update-season');

    var data = $('#update-season').serializeObject();

    $.ajax({
        url : `/xhr/admin/season/update/${data.id}`,
        type : 'POST',
        data : {
            'season': data
        },
        dataType:'json',
        success : function (res) {
            updateRow(JSON.parse(res));
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-update');
        },
        complete: function () {
            $.removeSpinner('.update-season', 'Valider');
        }
    });
});


/****************** FONCTION **********************/

/** Ajoute une ligne au tableau */
function addRow(season)
{
    let current_datetime = new Date(season.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#seasons-table').DataTable();
    var row = table.row.add([
        season.id,
        season.title,
        formatted_date,
        season.categories.length,
        $.getHtmlButton(season)
    ])
        .draw(false)
        .nodes()
        .to$()
        .attr('id', `season_${season.id}`);

    table.row(row).column(1).nodes().to$().addClass('title');
}

/** MàJ une ligne au tableau */
function updateRow(season)
{
    let current_datetime = new Date(season.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#seasons-table').DataTable();
    table.row(`#season_${season.id}`).data([
        season.id,
        season.title,
        formatted_date,
        season.categories.length,
        $.getHtmlButton(season)
    ]).draw(false);
}