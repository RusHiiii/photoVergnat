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
        success : function () {
            $.showSuccess('Profil édité !', '#success-edit');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-edit');
        },
        complete: function () {
            $.removeSpinner('.edit-user', 'Valider');
        }
    });
});

/** Fonction d'envoie du formulaire */
$('#update-password').on('submit', function (event) {
    event.preventDefault();

    $.addSpinner('.edit-password');

    var data = $('#update-password').serializeObject();

    $.ajax({
        url : `/xhr/app/user/edit-password/${data.id}`,
        type : 'POST',
        data : {
            'user': data
        },
        dataType:'json',
        success : function () {
            $.showSuccess('Mot de passe édité !', '#success-password');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-password');
        },
        complete: function () {
            $.removeSpinner('.edit-password', 'Valider');
        }
    });
});