/****************** LISTENER **********************/

/** Initialisation de la modal */
$('.comment .add').on('click', function (e) {
    $.ajax({
        url : '/xhr/admin/comment/display/create/',
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation de la modal */
$('#comments-table tbody').on('click', '.edit', function (e) {
    var id = $(this).data('id');
    $.ajax({
        url : `/xhr/admin/comment/display/edit/${id}`,
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal({'focus': false});
        }
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#create-comment', function (e) {
    e.preventDefault();

    $.addSpinner('.create-comment');

    $.ajax({
        url : '/xhr/app/comment/create',
        type : 'POST',
        data : {
            'comment': $('#create-comment').serializeObject()
        },
        dataType:'json',
        success : function (res) {
            addRow(JSON.parse(res));
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-create');
        },
        complete: function () {
            $.removeSpinner('.create-comment', 'Valider');
        }
    });
});

/** Initialisation formualire de MàJ */
$('body').on('submit', '#update-comment', function (e) {
    e.preventDefault();

    $.addSpinner('.update-comment');

    var data = $('#update-comment').serializeObject();

    $.ajax({
        url : `/xhr/admin/comment/update/${data.id}`,
        type : 'PATCH',
        data : {
            'comment': data
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
            $.removeSpinner('.update-comment', 'Valider');
        }
    });
});

/** Initilisation des modals de suppression */
$('#comments-table tbody').on('click', '.alert-ajax', function (e) {
    var table = $('#comments-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « " + $(`#comment_${id}`).children('.title').text() + " »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : `/xhr/admin/comment/remove/${id}`,
            type : 'DELETE',
            success : function () {
                table
                    .row($(`#comment_${id}`))
                    .remove()
                    .draw();
                swal('Suppression terminée !');
            }
        });
    });
});

/****************** FONCTION **********************/

/** Ajoute une ligne au tableau */
function addRow(comment)
{
    let current_datetime = new Date(comment.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#comments-table').DataTable();
    var row = table.row.add([
        comment.id,
        comment.category.title,
        comment.name,
        comment.email,
        comment.message,
        formatted_date,
        $.getHtmlButton(comment)
    ])
        .draw(false)
        .nodes()
        .to$()
        .attr('id', `comment_${comment.id}`);

    table.row(row).column(4).nodes().to$().addClass('title');
}

/** MàJ une ligne au tableau */
function updateRow(comment)
{
    let current_datetime = new Date(comment.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#comments-table').DataTable();
    table.row(`#comment_${comment.id}`).data([
        comment.id,
        comment.category.title,
        comment.name,
        comment.email,
        comment.message,
        formatted_date,
        $.getHtmlButton(comment)
    ]).draw(false);
}