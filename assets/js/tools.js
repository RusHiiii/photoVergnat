/****************** FONCTION **********************/

/** Fonction affichage des erreurs */
$.showErrors = function (data, element) {
    $(element).empty();

    if (!$.isEmptyObject(data)) {
        Object.keys(data).forEach(function (key) {
            $(element).append(data[key]).append('<br>');
        });

        $(element).show();
    } else {
        $(element).hide();
        $('#large-Modal').modal('hide');
    }
};

/** Affichage du succée */
$.showSuccess = function (data, element) {
    $(element).text(data);
    $(element).show();
};

/** Ajout du spinner */
$.addSpinner = function (data) {
    $(data).empty();
    $(data).addClass('loading spinner');
};

/** Suppression du spinner */
$.removeSpinner = function (data, value) {
    $(data).removeClass('loading spinner');
    $(data).html(value);
};

/** Génération du bouton */
$.getHtmlButton = function (data) {
    var html =
        '<button type="button" class="btn btn-warning waves-effect edit" data-id="'+data.id+'" data-toggle="modal"><i class="icofont icofont-edit-alt"></i></button>\n' +
        '<button type="button" class="btn btn-danger alert-ajax m-b-10 delete" data-id="'+data.id+'"><i class="icofont icofont-bin"></i></button>\n';

    return html;
};