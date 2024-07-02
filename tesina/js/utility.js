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
    id_risposta = id_domanda.replace('dom', 'risp');

    // Mostro il container
    container = document.getElementById(id_risposta);
    container.style.display = "block";
    
    // Cambio il contenuto del paragrafo contenente il simbolo della freccia
    simbolo = document.getElementById(id_domanda);
    simbolo.innerHTML = "&#x25B2";

    // Cambio l'evento di onclick
    nuovo_evento = "nascondiRispostaFaq('" + id_domanda + "')";
    simbolo.setAttribute('onclick', nuovo_evento);
}

function nascondiRispostaFaq(id_domanda)
{
    // Compongo l'id del container relativo alla risposta da nascondere
    id_risposta = id_domanda.replace('dom', 'risp');

    // Mostro il container
    container = document.getElementById(id_risposta);
    container.style.display = "none";

    // Cambio il contenuto del paragrafo contenente il simbolo della freccia
    simbolo = document.getElementById(id_domanda);
    simbolo.innerHTML = "&#x25BC;";

    // Cambio l'evento di onclick
    nuovo_evento = "mostraRispostaFaq('" + id_domanda + "')";
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

function coloraStellina(nome_stelle, stella)
{
    // Prendo il vettore di stelle su cui si e' verificato l'evento
    stelle = document.getElementsByClassName(nome_stelle);

    // Coloro le stelle finche' non raggiungo quella passata
    finito = false;
    for ( i=0; i<5 && !finito; i++ )
    {
        // Coloro la stella corrente
        stelle[i].innerHTML = '&#9733;';
        stelle[i].style.color = 'yellow';

        if ( stelle[i] == stella )
            finito = true;
    }
}

function decoloraStelline(nome_stelle, colore_reset)
{   
    // Prendo il vettore di stelle su cui si e' verificato l'evento
    stelle = document.getElementsByClassName(nome_stelle);

    // Coloro le stelle finche' non raggiungo quella passata
    for ( i=0; i<5; i++ )
    {
        // Coloro la stella corrente
        stelle[i].innerHTML = '&#9734;';
        stelle[i].style.color = colore_reset;
    }
}

function vaiDettaglioUtente(container)
{
    // Tramite il container accedo al form nascosto al suo interno
    // 3 figlio
    form = container.children[2];

    // Eseguo il submit del form
    form.submit();
}

function azzeraRicercaClienti()
{
    // Riferimento al form
    form = document.getElementById('ricercaClienti');

    // Azzero i campi
    checkboxAttivi = document.getElementsByName('attivi')[0];
    checkboxBannati = document.getElementsByName('bannati')[0];
    contenutoRic = document.getElementsByName('contenutoRicerca')[0];
    checkboxAttivi.checked = '';
    checkboxBannati.checked = '';
    contenutoRic.innerHTML = '';

    // Refresh della pagina
    form.submit();
}

function inserisciValutazione(id_intervento, stella_premuta)
{
    // Prelevo il riferimento all'intervento nella pagina per ottenere
    // l'id dell'intervento nei file XML e il tipo di intervento
    intervento = document.getElementById(id_intervento);
    id_intervento_xml = intervento.children[3].innerHTML;
    tipo_intervento = intervento.children[4].innerHTML;
    
    // Prelevo le informazioni dell'utente che effettua la valutazione nascoste nella pagina
    id_utente = document.getElementById('id_utente').innerHTML;
    reputazione_utente = document.getElementById('reputazione_utente').innerHTML;

    // Eseguo l'inserimento della valutazione in modalita' asincrona
    // Compongo la query string da passare allo script
    query_string = "id_utente=" + id_utente + "&reputazione_utente=" + reputazione_utente
                            + "&id_intervento_xml=" + id_intervento_xml + "&tipo_intervento=" + tipo_intervento
                            + "&stella_premuta=" + stella_premuta;
    
    // Oggetto per connessione mediante tecnologia AJAX
    xhr = new XMLHttpRequest();
    xhr.open("POST", "lib/inserisciValutazione.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function(){ callbackValutazione(xhr) }; // Definisco una funzione di callback implicita che chiama quella sotto
    xhr.send(query_string);
}

function callbackValutazione(xhr)
{
    // Ricevo vero o falso a seconda della riuscita dell'operazione
    // In caso di errore emetto un alert per notificarlo
    if ( !xhr.responseText )
        alert("Inserimento valutazione fallito");
    
    // A prescindere si effettua il refresh della pagina
    location.reload();
}