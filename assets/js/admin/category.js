/****************** LISTENER **********************/

/** Initialisation de la modal */
$('.category .add').on('click', function(e) {
    $.ajax({
        url : '/xhr/admin/category/display/create/',
        type : 'GET',
        success : function(res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

