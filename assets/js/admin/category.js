/****************** LISTENER **********************/

/** Initialisation de la modal */
$('.category .add').on('click', function (e) {
    $.ajax({
        url : '/xhr/admin/category/display/create/',
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal({'focus': false});
        }
    });
});

/** Initialisation de la modal */
$('#categories-table tbody').on('click', '.edit', function (e) {
    var id = $(this).data('id');
    $.ajax({
        url : `/xhr/admin/category/display/edit/${id}`,
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal({'focus': false});
        }
    });
});


/** Initilisation des modals de suppression */
$('#categories-table tbody').on('click', '.alert-ajax', function (e) {
    var table = $('#categories-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « " + $(`#category_${id}`).children('.title').text() + " »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : `/xhr/admin/category/remove/${id}`,
            type : 'DELETE',
            success : function () {
                table
                    .row($("#category_" + id))
                    .remove()
                    .draw();
                swal('Suppression terminée !');
            }
        });
    });
});

/** Initialisation formualire de MàJ */
$('body').on('submit', '#update-category', function (e) {
    e.preventDefault();

    $.addSpinner('.update-category');

    var data = $('#update-category').serializeObject();
    if (!$.isArray(data.tags)) {
        data.tags = [data.tags];
    }

    if (!$.isArray(data.photos)) {
        data.photos = [data.photos];
    }

    $.ajax({
        url : `/xhr/admin/category/update/${data.id}`,
        type : 'POST',
        data : {
            'category': data
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
            $.removeSpinner('.update-category', 'Valider');
        }
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#create-category', function (e) {
    e.preventDefault();

    $.addSpinner('.create-category');

    var data = $('#create-category').serializeObject();
    if (!$.isArray(data.tags)) {
        data.tags = [data.tags];
    }

    if (!$.isArray(data.photos)) {
        data.photos = [data.photos];
    }

    $.ajax({
        url : '/xhr/admin/category/create',
        type : 'POST',
        data : {
            'category': data
        },
        success : function (res) {
            addRow(JSON.parse(res));
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-create');
        },
        complete: function () {
            $.removeSpinner('.update-category', 'Valider');
        }
    });
});

/****************** FONCTION **********************/

/** Ajoute une ligne au tableau */
function addRow(category)
{
    let current_datetime = new Date(category.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#categories-table').DataTable();
    var row = table.row.add([
        category.id,
        category.title,
        formatted_date,
        category.tags.map(a => a.title).join('|'),
        category.city,
        category.season.title,
        category.photos.length,
        (category.active) ? 'Oui' : 'Non',
        $.getHtmlButton(category)
    ])
        .draw(false)
        .nodes()
        .to$()
        .attr('id', `category_${category.id}`);

    table.row(row).column(1).nodes().to$().addClass('title');
}

/** MàJ une ligne au tableau */
function updateRow(category)
{
    let current_datetime = new Date(category.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#categories-table').DataTable();
    table.row(`#category_${category.id}`).data([
        category.id,
        category.title,
        formatted_date,
        category.tags.map(a => a.title).join('|'),
        category.city,
        category.season.title,
        category.photos.length,
        (category.active) ? 'Oui' : 'Non',
        $.getHtmlButton(category)
    ]).draw(false);
}