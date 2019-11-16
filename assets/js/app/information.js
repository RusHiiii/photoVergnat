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
        success : function () {
            $.showSuccess('Email envoyé !', '#alert-success');
        },
        error: function (res) {
            $.showErrors(JSON.parse(res.responseJSON).context, '#alert-send');
        },
        complete: function () {
            $.removeSpinner('.send-mail', 'Valider');
        }
    });
});
