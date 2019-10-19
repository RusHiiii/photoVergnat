/****************** LISTENER **********************/

/** Fonction d'envoie du formulaire */
$('#update-user').on('submit', function (event) {
    event.preventDefault();

    $.addSpinner('.edit-user');

    $.ajax({
        url : '/xhr/app/user/edit-user',
        type : 'POST',
        data : {
            'user': $('#update-user').serializeObject()
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

    $.ajax({
        url : '/xhr/app/user/edit-password',
        type : 'POST',
        data : {
            'user': $('#update-password').serializeObject()
        },
        dataType:'json',
        success : function (res) {
            $.removeSpinner('.edit-password', 'Valider');
            $.showErrors(res['errors'], '#alert-password');
        }
    });
});