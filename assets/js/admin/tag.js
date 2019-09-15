/****************** LISTENER **********************/

/** Initialisation de la modal */
$('#tags-table tbody').on('click', '.edit', function(e){
    var id = $(this).data('id');
    $.ajax({
        url : '/xhr/admin/tag/display/edit/' + id,
        type : 'GET',
        success : function(res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initialisation de la modal */
$('.tag .add').on('click', function(e){
    $.ajax({
        url : '/xhr/admin/tag/display/create/',
        type : 'GET',
        success : function(res) {
            $('#large-Modal').html(res);
            $('#large-Modal').modal();
        }
    });
});

/** Initilisation des modals de suppression */
$('#tags-table tbody').on('click', '.alert-ajax', function(e){
    var table = $('#tags-table').DataTable();
    var id = $(this).data('id');

    swal({
        title: "Suppression",
        text: "Suppression de « "+ $("#tag_" + id).children('.title').text() +" »",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        showLoaderOnConfirm: true
    }, function () {
        $.ajax({
            url : '/xhr/admin/tag/remove',
            type : 'POST',
            data : {
                'tag': id
            },
            dataType:'json',
            success : function(res) {
                var message = 'Suppression terminée !';
                if(res.errors.length > 0) {
                    message = res.errors[0];
                }else{
                    table
                        .row($("#tag_" + id))
                        .remove()
                        .draw();
                }
                swal(message);
            }
        });
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#create-tag', function(e){
    e.preventDefault();

    $.addSpinner('.create-tag');

    $.ajax({
        url : '/xhr/admin/tag/create',
        type : 'POST',
        data : {
            'tag': $('#create-tag').serializeObject()
        },
        dataType:'json',
        success : function(res) {
            $.removeSpinner('.create-tag', 'Valider');
            $.showErrors(res['errors'], '#alert-create');

            if(res['errors'].length === 0){
                addRow(JSON.parse(res['tag']));
            }
        }
    });
});

/** Initialisation formualire d'ajout */
$('body').on('submit', '#update-tag', function(e){
    e.preventDefault();

    $.addSpinner('.update-tag');

    $.ajax({
        url : '/xhr/admin/tag/update',
        type : 'POST',
        data : {
            'tag': $('#update-tag').serializeObject()
        },
        dataType:'json',
        success : function(res) {
            $.removeSpinner('.update-tag', 'Valider');
            $.showErrors(res['errors'], '#alert-update');

            if(res['errors'].length === 0){
                updateRow(JSON.parse(res['tag']));
            }
        }
    });
});

/****************** FONCTION **********************/

/** Génération du bouton */
function getHtmlButton(data) {
    var html =
        '<button type="button" class="btn btn-warning waves-effect edit" data-id="'+data.id+'" data-toggle="modal"><i class="icofont icofont-edit-alt"></i></button>\n' +
        '<button type="button" class="btn btn-danger alert-ajax m-b-10 delete" data-id="'+data.id+'"><i class="icofont icofont-bin"></i></button>\n';

    return html;
}

/** Ajoute une ligne au tableau */
function addRow(tag) {
    let current_datetime = new Date(tag.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#tags-table').DataTable();
    var row = table.row.add([
        tag.id,
        tag.title,
        formatted_date,
        tag.categories.length,
        tag.type,
        getHtmlButton(tag)
    ])
        .draw(false)
        .nodes()
        .to$()
        .attr('id', 'tag_' + tag.id);

    table.row(row).column(1).nodes().to$().addClass('title');
}

/** Ajoute une ligne au tableau */
function updateRow(tag) {
    let current_datetime = new Date(tag.created);
    let formatted_date = current_datetime.getFullYear() + "-" + (("0" + (current_datetime.getMonth() + 1)).slice(-2)) + "-" + ("0" + current_datetime.getDate()).slice(-2) + " " + ("0" + current_datetime.getHours()).slice(-2) + ":" + ("0" + current_datetime.getMinutes()).slice(-2) + ":" + ("0" + current_datetime.getSeconds()).slice(-2);

    var table = $('#tags-table').DataTable();
    table.row('#tag_' + tag.id).data([
        tag.id,
        tag.title,
        formatted_date,
        tag.categories.length,
        tag.type,
        getHtmlButton(tag)
    ]).draw(false);
}
