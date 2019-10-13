/****************** LISTENER **********************/

/** Initialisation formualire d'ajout */
$('body').on('submit', '#update-password', function (e) {
    e.preventDefault();

    $.addSpinner('.edit-password');

    $.ajax({
        url : '/xhr/app/user/edit-password',
        type : 'POST',
        data : {
            'user': $('#update-password').serializeObject()
        },
        dataType:'json',
        statusCode: {
            403: function (response) {
                swal('Action interdite !');
            },
        },
        success : function (res) {
            $.removeSpinner('.edit-password', 'Valider');
            $.showErrors(res['errors'], '#alert-password');
        },
        error: function (res) {
            $.removeSpinner('.edit-password', 'Valider');
            $.showErrors(['Oops an errors occured :('], '#alert-password');
        }
    });
});

/** Initialisation formualire de MàJ */
$('body').on('submit', '#update-user', function (e) {
    e.preventDefault();

    $.addSpinner('.update-user');

    $.ajax({
        url : '/xhr/admin/user/update',
        type : 'POST',
        data : {
            'user': $('#update-user').serializeObject()
        },
        dataType:'json',
        statusCode: {
            403: function (response) {
                swal('Action interdite !');
            },
        },
        success : function (res) {
            $.removeSpinner('.update-user', 'Valider');
            $.showErrors(res['errors'], '#alert-update');

            if (res['errors'].length === 0) {
                updateRow(JSON.parse(res['user']));
            }
        },
        error: function (res) {
            $.removeSpinner('.edit-user', 'Valider');
            $.showErrors(['Oops an errors occured :('], '#alert-update');
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
        statusCode: {
            403: function (response) {
                swal('Action interdite !');
                $('#large-Modal').modal('hide');
            },
        },
        success : function (res) {
            $.removeSpinner('.create-user', 'Valider');
            $.showErrors(res['errors'], '#alert-create');

            if (res['errors'].length === 0) {
                addRow(JSON.parse(res['user']));
            }
        },
        error: function (res) {
            $.removeSpinner('.create-user', 'Valider');
            $.showErrors(['Oops an errors occured :('], '#alert-create');
        }
    });
});

/** Initilisation des modals de suppression */
$('#users-table tbody').on('click', '.alert-ajax', function (e) {
    var table = $('#users-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « "+ $("#user_" + id).children('.lastname').text() +" »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : '/xhr/admin/user/remove',
            type : 'DELETE',
            data : {
                'user': id
            },
            dataType:'json',
            statusCode: {
                403: function (res) {
                    swal('Action interdite !');
                },
            },
            success : function (res) {
                var message = 'Suppression terminée !';
                if (res.errors.length > 0) {
                    message = res.errors[0];
                } else {
                    table
                        .row($("#user_" + id))
                        .remove()
                        .draw();
                }
                swal(message);
            }
        });
    });
});

/** Initialisation de la modal */
$('#users-table tbody').on('click', '.edit', function (e) {
    var id = $(this).data('id');
    $.ajax({
        url : '/xhr/admin/user/display/edit/' + id,
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
        url : '/xhr/admin/user/display/password/' + id,
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
        .attr('id', 'user_' + user.id);

    table.row(row).column(1).nodes().to$().addClass('lastname');
}

/** MàJ une ligne au tableau */
function updateRow(user)
{
    let current_datetime = new Date(user.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#users-table').DataTable();
    table.row('#user_' + user.id).data([
        user.id,
        user.lastname,
        user.firstname,
        user.email,
        formatted_date,
        user.roles.map(e => e.replace('ROLE_', '')).join(" | "),
        getHtmlButton(user)
    ]).draw(false);
}
