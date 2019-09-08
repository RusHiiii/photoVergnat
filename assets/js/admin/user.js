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
});

// Initialisation formualire d'ajout
function initCreateUser() {
    $('body').on('submit', '#create-user', function(e){
        e.preventDefault();

        $('.create-user').empty();
        $('.create-user').addClass('loading spinner');

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
            success : function(res) {
                console.log(res);
                $('.create-user').removeClass('loading spinner');
                $('.create-user').html('Valider');

                showErrors(res['errors'], 'alert-create');
            }
        });
    });
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
                success : function(res) {
                    var message = 'Suppression terminée !';
                    if(res.errors.length > 0) {
                        message = res.errors;
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