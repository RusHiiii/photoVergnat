// any CSS you require will output into a single css file (app.css in this case)
require('../../css/admin/admin.scss');

require('./tools.js');
require('./user.js');
require('./tag.js');

$.addSpiner = function (data) {
    $(data).empty();
    $(data).addClass('loading spinner');
};