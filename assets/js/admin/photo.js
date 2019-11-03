/****************** LISTENER **********************/

/** Initialisation de la modal */
$('.photo .add').on('click', function (e) {
    $.ajax({
        url : '/xhr/admin/photo/display/create/',
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation de la modal */
$('#photos-table tbody').on('click', '.edit', function (e) {
    var id = $(this).data('id');
    $.ajax({
        url : `/xhr/admin/photo/display/edit/${id}`,
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#create-photo', function (e) {
    e.preventDefault();

    $.addSpinner('.create-photo');

    $.ajax({
        url : '/xhr/admin/photo/create',
        type : 'POST',
        data : new FormData(this),
        dataType:'json',
        contentType: false,
        cache: false,
        processData:false,
        success : function (res) {
            addRow(JSON.parse(res));
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-create');
        },
        complete: function () {
            $.removeSpinner('.create-photo', 'Valider');
        }
    });
});

/** Initialisation formualire de MàJ */
$('body').on('submit', '#update-photo', function (e) {
    e.preventDefault();

    $.addSpinner('.update-photo');

    var data = new FormData(this);

    $.ajax({
        url : `/xhr/admin/photo/update/${data.get('id')}`,
        type : 'PATCH',
        data : data,
        dataType:'json',
        contentType: false,
        cache: false,
        processData:false,
        success : function (res) {
            updateRow(JSON.parse(res));
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-update');
        },
        complete: function () {
            $.removeSpinner('.update-photo', 'Valider');
        }
    });
});

/** Initilisation des modals de suppression */
$('#photos-table tbody').on('click', '.alert-ajax', function (e) {
    var table = $('#photos-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « " + $(`#photo_${id}`).children('.title').text() + " »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : `/xhr/admin/photo/remove/${id}`,
            type : 'DELETE',
            success : function (res) {
                table
                    .row($("#photo_" + id))
                    .remove()
                    .draw();
                swal('Suppression terminée !');
            }
        });
    });
});

/****************** FONCTION **********************/

/** Ajoute une ligne au tableau */
function addRow(photo)
{
    let current_datetime = new Date(photo.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#photos-table').DataTable();
    var row = table.row.add([
        photo.id,
        "<img src='/images/uploads/"+photo.file+"'>",
        photo.title,
        photo.type.title,
        photo.tags.map(a => a.title).join('|'),
        formatted_date,
        $.getHtmlButton(photo)
    ])
        .draw(false)
        .nodes()
        .to$()
        .attr('id', `photo_${photo.id}`);

    table.row(row).column(1).nodes().to$().addClass('photo-screen');
    table.row(row).column(2).nodes().to$().addClass('title');
}

/** Ajoute une ligne au tableau */
function updateRow(photo)
{
    let current_datetime = new Date(photo.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#photos-table').DataTable();
    table.row(`#photo_${photo.id}`).data([
        photo.id,
        "<img src='/images/uploads/"+photo.file+"'>",
        photo.title,
        photo.type.title,
        photo.tags.map(a => a.title).join('|'),
        formatted_date,
        $.getHtmlButton(photo)
    ]).draw(false);
}