$( document ).ready(function() {

    // Initialisation de la table
    initDatatable();

    // Initilisation du bouton de suppression
    initDeleteButton();

    // Initialisation de la modal d'édition
    initModalEdit();

    // Initialisation de la modal de mot de passe
    initModalPassword();

    // Initialisation de la modal de création
    initModalCreate();

    // Initialisation submit ajout
    initCreateUser();

    // Initialisation du submit
    initUpdatePassword();

    // Initialisation de la MàJ
    initUpdateUser();
});

// Initialisation formualire d'ajout
function initUpdatePassword() {
    $('body').on('submit', '#update-password', function(e){
        e.preventDefault();

        addSpiner('.edit-password');

        var data = $('#update-password').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        $.ajax({
            url : '/xhr/app/user/edit-password',
            type : 'POST',
            data : {
                'user': data
            },
            dataType:'json',
            statusCode: {
                403: function (response) {
                    swal('Action interdite !');
                },
            },
            success : function(res) {
                removeSpinner('.edit-password', 'Valider');

                showErrors(res['errors'], 'alert-password');
                if(res['errors'].length === 0){
                    $('#large-Modal').modal('hide');
                }
            }
        });
    });
}

// Initialisation formualire d'ajout
function initUpdateUser() {
    $('body').on('submit', '#update-user', function(e){
        e.preventDefault();

        addSpiner('.update-user');

        var data = $('#update-user').serializeArray().reduce(function(obj, item) {
            if(item.name === 'roles'){
                if(obj[item.name] === undefined){
                    obj[item.name] = [];
                }
                obj[item.name].push(item.value);
            }else{
                obj[item.name] = item.value;
            }
            return obj;
        }, {});

        $.ajax({
            url : '/xhr/admin/user/update',
            type : 'POST',
            data : {
                'user': data
            },
            dataType:'json',
            statusCode: {
                403: function (response) {
                    swal('Action interdite !');
                },
            },
            success : function(res) {
                removeSpinner('.update-user', 'Valider');

                showErrors(res['errors'], 'alert-update');
                if(res['errors'].length === 0){
                    updateRow(JSON.parse(res['user']));
                    $('#large-Modal').modal('hide');
                }
            }
        });
    });
}

// Initialisation formualire d'ajout
function initCreateUser() {
    $('body').on('submit', '#create-user', function(e){
        e.preventDefault();

        addSpiner('.create-user');

        var data = $('#create-user').serializeArray().reduce(function(obj, item) {
            if(item.name === 'roles'){
                if(obj[item.name] === undefined){
                    obj[item.name] = [];
                }
                obj[item.name].push(item.value);
            }else{
                obj[item.name] = item.value;
            }
            return obj;
        }, {});

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
            success : function(res) {
                removeSpinner('.create-user', 'Valider');

                showErrors(res['errors'], 'alert-create');
                if(res['errors'].length === 0){
                    addRow(JSON.parse(res['user']));
                    $('#large-Modal').modal('hide');
                }
            }
        });
    });
}

// Ajoute une ligne au tableau
function addRow(user) {
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

// Ajoute une ligne au tableau
function updateRow(user) {
    let current_datetime = new Date(user.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#users-table').DataTable();
    var row = table.row('#user_' + user.id).data([
        user.id,
        user.lastname,
        user.firstname,
        user.email,
        formatted_date,
        user.roles.map(e => e.replace('ROLE_', '')).join(" | "),
        getHtmlButton(user)
    ])
        .draw(false);
}

// Génération du bouton
function getHtmlButton(data) {
    return '<button type="button" class="btn btn-warning waves-effect edit" data-id="'+data.id+'" data-toggle="modal"><i class="icofont icofont-edit-alt"></i></button>\n' +
        '<button type="button" class="btn btn-danger alert-ajax m-b-10 delete" data-id="'+data.id+'"><i class="icofont icofont-bin"></i></button>\n' +
        '<button type="button" class="btn btn-info m-b-10 pswd" data-id="'+data.id+'"><i class="icofont icofont-lock"></i></button>\n';
}

// Fonction d'initialisation de la table
function initDatatable() {
    $('#users-table').DataTable({
        "searching": true,
        "lengthChange": true,
        "language": {
            "lengthMenu": "Afficher _MENU_ lignes par page",
            "info": "Page _PAGE_ sur _PAGES_",
            "zeroRecords": "Aucun message !",
            "infoEmpty": "",
            "paginate": {
                "previous": "«",
                "next": "»"
            }
        },
        "ordering": false,
        "pageLength": 10,
        "pagingType": "simple"
    });
}

// Initilisation des modals de suppression
function initDeleteButton() {
    $('#users-table tbody').on('click', '.alert-ajax', function(e){
        var table = $('#users-table').DataTable();
        var id = $(this).data('id');
        var name = $("#user_" + id).children('.lastname').text();

        swal({
            title: "Suppression",
            text: "Suppression de « "+ name +" »",
            type: "warning",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            $.ajax({
                url : '/xhr/admin/user/remove',
                type : 'POST',
                data : {
                    'user': id
                },
                dataType:'json',
                statusCode: {
                    403: function (response) {
                        swal('Action interdite !');
                    },
                },
                success : function(res) {
                    var message = 'Suppression terminée !';
                    if(res.errors.length > 0) {
                        message = res.errors[0];
                    }else{
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
}

// Initialisation de la modal
function initModalEdit() {
    $('#users-table tbody').on('click', '.edit', function(e){
        var id = $(this).data('id');

        $.ajax({
            url : '/xhr/admin/user/display/edit/' + id,
            type : 'GET',
            success : function(res) {
                $('#large-Modal').html(res);
                $('#large-Modal').modal();
            }
        });
    });
}

// Initialisation de la modal
function initModalPassword() {
    $('#users-table tbody').on('click', '.pswd', function(e){
        var id = $(this).data('id');

        $.ajax({
            url : '/xhr/admin/user/display/password/' + id,
            type : 'GET',
            success : function(res) {
                $('#large-Modal').html(res);
                $('#large-Modal').modal();
            }
        });
    });
}

// Initialisation de la modal
function initModalCreate() {
    $('.add').on('click', function(e){

        $.ajax({
            url : '/xhr/admin/user/display/create/',
            type : 'GET',
            success : function(res) {
                $('#large-Modal').html(res);
                $('#large-Modal').modal();
            }
        });
    });
}

// Fonction affichage des erreurs
function showErrors(data, element) {
    $("#" + element).empty();

    if(!$.isEmptyObject(data)){
        Object.keys(data).forEach(function(key) {
            $("#" + element).append(data[key]).append('<br>');
        });

        $("#" + element).show();
    }else{
        $("#" + element).hide();
    }
}

// Ajout du spinner
function addSpiner(data) {
    $(data).empty();
    $(data).addClass('loading spinner');
}

// Suppression du spinner
function removeSpinner(data, value) {
    $(data).removeClass('loading spinner');
    $(data).html(value);
}