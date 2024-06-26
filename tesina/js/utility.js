function gestisciSidebar(param)
{
    // Prendo il riferimento alla sidebar
    sidebar = document.getElementById("sidebar");

    // Prendo il riferimento ai bottoni
    btn1 = document.getElementById("btnSidebar1");
    btn2 = document.getElementById("btnSidebar2");

    // Verifico se la sidebar e' mostrata o meno tramite il testo 
    if ( param == 0 ) // Mostro la sidebar
    {
        sidebar.style.left = "0%";
        btn1.style.display = "none";
        btn2.style.display = "block";
    }
    else if( param == 1 ) // Nascondo la sidebar
    {
        sidebar.style.left = "-25%";
        btn1.style.display = "block";
        btn2.style.display = "none";
    }
}

function nascondiPopup()
{
    // Prendo il riferimento alla finestra popup
    popup = document.getElementById("sezioneErrore");

    // Nascondo il popup
    popup.firstElementChild.style.marginRight = "-100%";
    
    // Tolgo la sezione errore al termine dell'animazione
    setTimeout(function(){popup.style.display = "none";}, 300);
}

function mostraRispostaFaq(id_domanda)
{
    // Compongo l'id del container relativo alla risposta da mostrare
    id_risposta = "risp_" + id_domanda;

    // Mostro il container
    container = document.getElementById(id_risposta);
    container.style.display = "block";
    
    // Cambio il contenuto del paragrafo contenente il simbolo della freccia
    simbolo = document.getElementById(id_domanda);
    simbolo.innerHTML = "&#x25B2";

    // Cambio l'evento di onclick
    nuovo_evento = "nascondiRispostaFaq(" + id_domanda + ")";
    simbolo.setAttribute('onclick', nuovo_evento);
}

function nascondiRispostaFaq(id_domanda)
{
    // Compongo l'id del container relativo alla risposta da nascondere
    id_risposta = "risp_" + id_domanda;

    // Mostro il container
    container = document.getElementById(id_risposta);
    container.style.display = "none";

    // Cambio il contenuto del paragrafo contenente il simbolo della freccia
    simbolo = document.getElementById(id_domanda);
    simbolo.innerHTML = "&#x25BC;";

    // Cambio l'evento di onclick
    nuovo_evento = "mostraRispostaFaq(" + id_domanda + ")";
    simbolo.setAttribute('onclick', nuovo_evento);
}

function azzeraFormRegistrazione()
{
    // Azzeramento campi form
    document.getElementsByName('nome')[0].value   = "";
    document.getElementsByName('cognome')[0].value   = "";
    document.getElementsByName('citta')[0].value   = "";
    document.getElementsByName('cap')[0].value   = "";
    document.getElementsByName('indirizzo')[0].value   = "";
    document.getElementsByName('username')[0].value   = "";
    document.getElementsByName('mail')[0].value   = "";
    document.getElementsByName('password')[0].value   = "";
}

function vaiDettaglioDomanda(id_domanda)
{
    // Costruisco l'url per ridirezionare l'utente
    // alla pagina dove visualizzare in dettaglio la domanda
    url = 'dettaglioDomanda.php?id_domanda=' + id_domanda;
    window.location.href = url;
}