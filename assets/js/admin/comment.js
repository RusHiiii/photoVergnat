/****************** LISTENER **********************/

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
            statusCode: {
                403: function () {
                    swal('Action interdite !');
                },
            },
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
