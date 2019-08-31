$( document ).ready(function() {
    // Envoie du formulaire
    initUpdateEdit();

    // Validation du formulaire
    initFormValidation();
});

// Fonction d'envoie du formulaire
function initUpdateEdit() {
    $('#update-user').on('submit', function(event) {
        event.preventDefault();

        $('.edit-user').empty();
        $('.edit-user').addClass('loading spinner');

        var data = $('#update-user').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        $.ajax({
            url : '/xhr/front/edit-user',
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
function initFormValidation() {
    $.validate({
        lang: 'fr',
        borderColorOnError : '#ff001c',
        scrollToTopOnError: false
    });
}

// Fonction affichage des erreurs
function showErrors(data, element) {
    if(!$.isEmptyObject(data)){
        $("#" + element).empty();

        Object.keys(data).forEach(function(key) {
            $("#" + element).append(data[key]).append('<br>');
        });

        $("#" + element).show();
    }
}