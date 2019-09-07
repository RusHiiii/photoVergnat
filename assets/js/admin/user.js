$( document ).ready(function() {
    // Initialisation de la table
    initDatatable();

    // Initilisation du bouton de suppression
    initDeleteButton();

    // Initialisation de la modal d'édition
    initModalEdit();

    // Initialisation de la modal de mot de passe
    initModalPassword();
});

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