/****************** FONCTION **********************/

/** Fonction affichage des erreurs */
$.showErrors = function(data, element) {
    $(element).empty();

    if(!$.isEmptyObject(data)){
        Object.keys(data).forEach(function(key) {
            $(element).append(data[key]).append('<br>');
        });

        $(element).show();
    }else{
        $(element).hide();
        $('#large-Modal').modal('hide');
    }
}

/** Ajout du spinner */
$.addSpinner = function(data) {
    $(data).empty();
    $(data).addClass('loading spinner');
}

/** Suppression du spinner */
$.removeSpinner = function(data, value) {
    $(data).removeClass('loading spinner');
    $(data).html(value);
}
