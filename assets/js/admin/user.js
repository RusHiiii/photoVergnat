/****************** LISTENER **********************/

/** Initialisation de la modal */
$('#users-table tbody').on('click', '.edit', function (e) {
    var id = $(this).data('id');

    $.ajax({
        url : `/xhr/admin/user/display/edit/${id}`,
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation de la modal */
$('#users-table tbody').on('click', '.pswd', function (e) {
    var id = $(this).data('id');

    $.ajax({
        url : `/xhr/admin/user/display/password/${id}`,
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation de la modal */
$('.user .add').on('click', function (e) {
    $.ajax({
        url : '/xhr/admin/user/display/create/',
        type : 'GET',
        success : function (res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#update-password', function (e) {
    e.preventDefault();

    $.addSpinner('.edit-password');

    var data = $('#update-password').serializeObject();

    $.ajax({
        url : `/xhr/app/user/edit-password/${data.id}`,
        type : 'POST',
        data : {
            'user': data
        },
        dataType:'json',
        success : function () {
            $('#large-Modal').modal('hide');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-password');
        },
        complete: function () {
            $.removeSpinner('.edit-password', 'Valider');
        }
    });
});

/** Initialisation formualire de MàJ */
$('body').on('submit', '#update-user', function (e) {
    e.preventDefault();

    $.addSpinner('.update-user');

    var data = $('#update-user').serializeObject();
    if (!$.isArray(data['roles'])) {
        data['roles'] = [data['roles']];
    }

    $.ajax({
        url : `/xhr/admin/user/update/${data.id}`,
        type : 'PATCH',
        data : {
            'user': data
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
            $.removeSpinner('.edit-user', 'Valider');
        }
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#create-user', function (e) {
    e.preventDefault();

    $.addSpinner('.create-user');

    var data = $('#create-user').serializeObject();
    if (!$.isArray(data['roles'])) {
        data['roles'] = [data['roles']];
    }

    $.ajax({
        url : '/xhr/admin/user/create',
        type : 'POST',
        data : {
            'user': data
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
            $.removeSpinner('.create-user', 'Valider');
        }
    });
});

/** Initilisation des modals de suppression */
$('#users-table tbody').on('click', '.alert-ajax', function (e) {
    var table = $('#users-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « " + $(`#user_${id}`).children('.lastname').text() +" »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : `/xhr/admin/user/remove/${id}`,
            type : 'DELETE',
            dataType:'json',
            success : function () {
                table
                    .row($(`#user_${id}`))
                    .remove()
                    .draw();
                swal('Suppression terminée !');
            }
        });
    });
});

/****************** FONCTION **********************/

/** Génération du bouton */
function getHtmlButton(data)
{
    var html =
        '<button type="button" class="btn btn-warning waves-effect edit" data-id="'+data.id+'" data-toggle="modal"><i class="icofont icofont-edit-alt"></i></button>\n' +
        '<button type="button" class="btn btn-danger alert-ajax m-b-10 delete" data-id="'+data.id+'"><i class="icofont icofont-bin"></i></button>\n' +
        '<button type="button" class="btn btn-info m-b-10 pswd" data-id="'+data.id+'"><i class="icofont icofont-lock"></i></button>\n';

    return html;
}

/** Ajoute une ligne au tableau */
function addRow(user)
{
    let current_datetime = new Date(user.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#users-table').DataTable();
    var row = table.row.add([
        user.id,
        user.lastname,
        user.firstname,
        user.email,
        formatted_date,
        user.roles.map(e => e.replace('ROLE_', '')).join(" | "),
        getHtmlButton(user)
    ])
        .draw(false)
        .nodes()
        .to$()
        .attr('id', `user_${user.id}`);

    table.row(row).column(1).nodes().to$().addClass('lastname');
}

/** MàJ une ligne au tableau */
function updateRow(user)
{
    let current_datetime = new Date(user.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#users-table').DataTable();
    table.row(`#user_${user.id}`).data([
        user.id,
        user.lastname,
        user.firstname,
        user.email,
        formatted_date,
        user.roles.map(e => e.replace('ROLE_', '')).join(" | "),
        getHtmlButton(user)
    ]).draw(false);
}
