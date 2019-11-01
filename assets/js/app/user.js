/****************** LISTENER **********************/

/** Fonction d'envoie du formulaire */
$('#update-user').on('submit', function (event) {
    event.preventDefault();

    $.addSpinner('.edit-user');

    var data = $('#update-user').serializeObject();

    $.ajax({
        url : '/xhr/app/user/edit-user/',
        type : 'POST',
        data : {
            'user': data
        },
        dataType:'json',
        success : function (res) {
            $.removeSpinner('.edit-user', 'Mettre a jours');
            $.showErrors(res['errors'], '#alert-edit');
        }
    });
});

/** Fonction d'envoie du formulaire */
$('#update-password').on('submit', function (event) {
    event.preventDefault();

    $.addSpinner('.edit-password');

    var data = $('#update-password').serializeObject();

    $.ajax({
        url : '/xhr/app/user/edit-password/' + data['id'],
        type : 'POST',
        data : {
            'user': data
        },
        dataType:'json',
        success : function (res) {
            $.removeSpinner('.edit-password', 'Valider');
            $.showErrors(res['errors'], '#alert-password');
        }
    });
});