$( document ).ready(function() {
    // Envoie du formulaire
    initUpdateUser();

    // MàJ du mot de passe
    initUpdatePassword();

    // Validation du formulaire
    initFormValidation();
});

// Fonction d'envoie du formulaire
function initUpdateUser() {
    $('#update-user').on('submit', function(event) {
        event.preventDefault();

        $('.edit-user').empty();
        $('.edit-user').addClass('loading spinner');

        var data = $('#update-user').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        $.ajax({
            url : '/xhr/app/edit-user',
            type : 'POST',
            data : {
                'user': data
            },
            dataType:'json',
            success : function(res) {
                $('.edit-user').removeClass('loading spinner');
                $('.edit-user').html('Mettre à jours');

                showErrors(res['errors'], 'alert-edit');
            }
        });
    });
}

// Fonction d'envoie du formulaire
function initUpdatePassword() {
    $('#update-password').on('submit', function(event) {
        event.preventDefault();

        $('.edit-password').empty();
        $('.edit-password').addClass('loading spinner');

        var data = $('#update-password').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        $.ajax({
            url : '/xhr/app/edit-password',
            type : 'POST',
            data : {
                'user': data
            },
            dataType:'json',
            success : function(res) {
                $('.edit-password').removeClass('loading spinner');
                $('.edit-password').html('Valider');

                showErrors(res['errors'], 'alert-password');
            }
        });
    });
}

// Fonction d'envoie du formulaire
function initFormValidation() {
    $.validate({
        lang: 'fr',
        borderColorOnError : '#ff001c',
        scrollToTopOnError: false,
        modules : 'security',
        onModulesLoaded : function() {
            var optionalConfig = {
                fontSize: '8pt',
                padding: '4px',
                bad : 'Trop faible',
                weak : 'Faible',
                good : 'Bon',
                strong : 'Très bon'
            };

            $('input[name="password_first"]').displayPasswordStrength(optionalConfig);
        }
    });
}

// Fonction affichage des erreurs
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