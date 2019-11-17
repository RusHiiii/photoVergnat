/*************** INITIALISATION ***************/

$( document ).ready(function() {
    initRgpd();
});

/** Fonction du RGPD */
function initRgpd(){
    window.cookieconsent.initialise({
        "palette": {
            "popup": {
                "background": "#fb9d79",
                "text": "#ffffff"
            },
            "button": {
                "background": "#FBC279",
                "text": "#ffffff"
            }
        },
        "theme": "edgeless",
        "position": "bottom-right",
        "content": {
            "message": "Photo'Vergnat utilise des cookies pour le fonctionnement du site.",
            "dismiss": "Accepter",
            "link": "Plus d'infos",
            "href": "/contact"
        }
    });
}