
const handleMessage = function(message){
    let mess = message;
    if (message == 'Request failed with status code 401') {
        window.location.href = window.location.href
        mess = 'Votre compte n\'est peut-être pas autorisé à effectuer cette action. Veuillez actualiser la page et réessayer.';
    }
    if (message == 'Request failed with status code 403') {
        mess = 'Votre compte n\'est peut-être pas autorisé à effectuer cette action.';
    }
    if (message == 'Request failed with status code 404') {
        mess = 'Ressource non trouvée.';
    }
    if (message == 'Request failed with status code 419') {
        window.location.href = window.location.href
        mess = 'Désolé, votre session a expiré. Veuillez actualiser et réessayer.';
    }
    if (message == 'Request failed with status code 422') {
        mess = '';
    }
    if (message == 'Request failed with status code 500') {
        mess = 'Oups, quelque chose s\'est mal passé sur nos serveurs..';
    }
    if (message == 'Request failed with status code 503') {
        mess = 'Service temporairement indisponible ou en maintenance.';
    }
    if (message == 'Request failed with status code 504') {
        mess = 'Temps d’attente écoulé.';
    }
    if (message == 'Request failed with status code 526') {
        mess = 'Certificat SSL invalide.';
    }

    return mess;
}

export default handleMessage;
