/****************** LISTENER **********************/

/** Fonction d'envoie du formulaire */
$('#create-comment').on('submit', function (event) {
    event.preventDefault();

    $.addSpinner('.create-comment');

    $.ajax({
        url : '/xhr/app/comment/create',
        type : 'POST',
        data : {
            'comment': $('#create-comment').serializeObject()
        },
        dataType:'json',
        success : function (res) {
            $.removeSpinner('.create-comment', 'Envoyer');
            $.showErrors(res['errors'], '#alert-create');

            if (res['errors'].length === 0) {
                addComment(JSON.parse(res['comment']));
            }
        }
    });
});

/****************** FONCTION **********************/

/** Ajoute le commentaire */
function addComment(comment)
{
    let current_datetime = new Date(comment.created);

    const monthNames = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

    $('#comment-section').append(
        '<li class="single_comment_area">\n' +
        '  <div class="comment-content d-flex">\n' +
        '    <div class="comment-author">\n' +
        '       <img src="/theme/img/bg-img/Person.jpg">\n' +
        '    </div>\n' +
        '    <div class="comment-meta">\n' +
        '       <a href="#" class="post-date">'+ monthNames[current_datetime.getMonth()] + ' ' + current_datetime.getFullYear() +'</a>\n' +
        '       <h5>'+ comment.name +'</h5>\n' +
        '       <p>'+ comment.message +'</p>\n' +
        '    </div>\n' +
        '  </div>\n' +
        '</li>'
    );

    var number = parseInt($('#number-comment').text(), 10) + 1;
    $('#number-comment').text(number);

    $('.no-message').hide();
}