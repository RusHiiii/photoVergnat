/****************** LISTENER **********************/

/** Initialisation de la modal */
$('#types-table tbody').on('click', '.edit', function (e) {
    var id = $(this).data('id');

    $.ajax({
        url : `/xhr/admin/type/display/edit/${id}`,
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation de la modal */
$('.type .add').on('click', function (e) {
    $.ajax({
        url : '/xhr/admin/type/display/create/',
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initilisation des modals de suppression */
$('#types-table tbody').on('click', '.alert-ajax', function (e) {
    var table = $('#types-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « " + $(`#type_${id}`).children('.title').text() + " »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : `/xhr/admin/type/remove/${id}`,
            type : 'DELETE',
            success : function () {
                table
                    .row($(`#type_${id}`))
                    .remove()
                    .draw();
                swal('Suppression terminée !');
            }
        });
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#create-type', function (e) {
    e.preventDefault();

    $.addSpinner('.create-type');

    $.ajax({
        url : '/xhr/admin/type/create',
        type : 'POST',
        data : {
            'type': $('#create-type').serializeObject()
        },
        dataType:'json',
        success : function (res) {
            addRow(res);
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(res.responseJSON.context, '#alert-create');
        },
        complete: function () {
            $.removeSpinner('.create-type', 'Valider');
        }
    });
});

/** Initialisation formualire de MàJ */
$('body').on('submit', '#update-type', function (e) {
    e.preventDefault();

    $.addSpinner('.update-type');

    var data = $('#update-type').serializeObject();

    $.ajax({
        url : `/xhr/admin/type/update/${data.id}`,
        type : 'PATCH',
        data : {
            'type': data
        },
        dataType:'json',
        success : function (res) {
            updateRow(res);
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(res.responseJSON.context, '#alert-update');
        },
        complete: function () {
            $.removeSpinner('.update-type', 'Valider');
        }
    });
});

/****************** FONCTION **********************/

/** Ajoute une ligne au tableau */
function addRow(type)
{
    let current_datetime = new Date(type.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#types-table').DataTable();
    var row = table.row.add([
        type.id,
        type.title,
        formatted_date,
        type.photos.length,
        $.getHtmlButton(type)
    ])
        .draw(false)
        .nodes()
        .to$()
        .attr('id', `type_${type.id}`);

    table.row(row).column(1).nodes().to$().addClass('title');
}

/** MàJ une ligne au tableau */
function updateRow(type)
{
    let current_datetime = new Date(type.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#types-table').DataTable();
    table.row('#type_' + type.id).data([
        type.id,
        type.title,
        formatted_date,
        type.photos.length,
        $.getHtmlButton(type)
    ]).draw(false);
}