/****************** LISTENER **********************/

/** Initialisation de la modal */
$('.photo .add').on('click', function(e){
    $.ajax({
        url : '/xhr/admin/photo/display/create/',
        type : 'GET',
        success : function(res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});
