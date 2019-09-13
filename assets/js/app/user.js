/****************** LISTENER **********************/

/** Fonction d'envoie du formulaire */
$('#update-user').on('submit', function(event) {
    event.preventDefault();

    addSpiner('.edit-user');

    $.ajax({
        url : '/xhr/app/user/edit-user',
        type : 'POST',
        data : {
            'user': $('#update-user').serializeObject()
        },
        dataType:'json',
        success : function(res) {
            removeSpinner('.edit-user', 'Mettre a jours');
            showErrors(res['errors'], 'alert-edit');
        }
    });
});

/** Fonction d'envoie du formulaire */
$('#update-password').on('submit', function(event) {
    event.preventDefault();

    addSpiner('.edit-password');

    $.ajax({
        url : '/xhr/app/user/edit-password',
        type : 'POST',
        data : {
            'user': $('#update-password').serializeObject()
        },
        dataType:'json',
        success : function(res) {
            removeSpinner('.edit-password', 'Valider');
            showErrors(res['errors'], 'alert-password');
        }
    });
});

/****************** FONCTION **********************/

/** Fonction affichage des erreurs */
function showErrors(data, element) {
    $("#" + element).empty();

    if(!$.isEmptyObject(data)){
        Object.keys(data).forEach(function(key) {
            $("#" + element).append(data[key]).append('<br>');
        });

        $("#" + element).show();
    }else{
        $("#" + element).hide();
    }
}

/** Ajout du spinner */
function addSpiner(data) {
    $(data).empty();
    $(data).addClass('loading spinner');
}

/** Suppression du spinner */
function removeSpinner(data, value) {
    $(data).removeClass('loading spinner');
    $(data).html(value);
}