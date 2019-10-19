/****************** LISTENER **********************/

/** Fonction d'envoie du formulaire */
$('#send-mail').on('submit', function (event) {
    event.preventDefault();

    $.addSpinner('.send-mail');

    $.ajax({
        url : '/xhr/app/information/contact/send',
        type : 'POST',
        data : {
            'mail': $('#send-mail').serializeObject()
        },
        dataType:'json',
        success : function (res) {
            $.removeSpinner('.send-mail', 'Envoyer');
            $.showErrors(res['errors'], '#alert-send');

            if (res['errors'].length === 0) {
                $.showSuccess('Email envoyé !', '#alert-success');
            }
        }
    });
});
