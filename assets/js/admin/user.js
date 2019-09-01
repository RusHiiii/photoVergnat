$( document ).ready(function() {
    // Initialisation de la table
    initDatatable();
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
