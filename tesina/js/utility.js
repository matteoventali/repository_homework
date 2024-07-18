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
    xhr.open("POST", "inserisciValutazione.php");
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

function cambiaStatoAccount(operazione, id_utente)
{
    // Operazione 1=ban 2=id_utente
    // Compongo la query string da passare allo script
    query_string = "id_utente=" + id_utente + "&operazione=" + operazione;
                            
    // Oggetto per connessione mediante tecnologia AJAX
    xhr = new XMLHttpRequest();
    xhr.open("POST", "lib/cambiaStatoCliente.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function(){ callbackCambiaStatoAccount(xhr) }; // Definisco una funzione di callback implicita che chiama quella sotto
    xhr.send(query_string);
}

function callbackCambiaStatoAccount(xhr)
{
    // Ricevo vero o falso a seconda della riuscita dell'operazione
    // Notifica tramite alert in caso di errore
    if ( !xhr.responseText )
        alert("Cambio stato fallito");
    
    // Per effettuare il refresh della pagina effettuo il submit del form
    // che contiene il bottone per ban/riattivazione account.
    // Cambio l'azione e imposto lo script dettaglioCliente.php in modo da ricaricare
    // la pagina.
    // Meccanismo implementato cosi per evitare la richiesta di alcuni browser 
    // di reinviare la richiesta POST (es. Firefox)
    form = document.getElementById('formOpzioni');
    form.action = 'dettaglioCliente.php';
    form.submit();
}

function tornaIndietroDallaModificaCliente(ruolo)
{
    // A seconda del ruolo scelgo in modo opportuno l'azione del form
    // che corrisponde alla pagina su cui tornare dalla pagina di modifica
    // del cliente
    if ( ruolo == 'A' )
        pagina_precedente = 'dettaglioCliente.php';
    else if ( ruolo == 'C' )
        pagina_precedente = 'areaPersonale.php';

    // Prendo il riferimento al form
    form = document.getElementById('formModifica');
    form.action = pagina_precedente;

    // Eseguo il submit del form per tornare indietro
    form.submit();
}

function eliminaIntervento(form)
{
    // Id e tipo dell'intervento
    id_intervento_xml = form.children[0].children[0].value;
    tipo_intervento_xml = form.children[0].children[1].value;

    // Composizione della query string
    query_string = 'id_intervento=' + id_intervento_xml + "&tipo_intervento=" + tipo_intervento_xml;

    // Richiesta AJAX per eliminare l'intervento
    xhr = new XMLHttpRequest();
    xhr.open("POST", "eliminaIntervento.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function(){ callbackEliminaIntervento(xhr, tipo_intervento_xml) }; // Definisco una funzione di callback implicita che chiama quella sotto
    xhr.send(query_string);
}

function callbackEliminaIntervento(xhr, tipo)
{
    // Ricevo vero o falso a seconda della riuscita dell'operazione
    // Notifica tramite alert in caso di errore
    if ( !xhr.responseText )
        alert("Eliminazione intervento fallita");
    
    // Se e' stata eliminata una domanda ritorno alla pagina di resoconto domande
    // altrimenti refresh della pagina
    if (tipo != 'domanda')
        location.reload();
    else
        location.href = 'domande.php';
}

function vaiDettaglioAcquisto(container) 
{
    // Eseguo il submit del form
    // Il form e' il secondo figlio del container acquisto
    form = container.children[1];
    form.submit();
}

function svuotaTendinaTipologie()
{
    // Per ogni tipologia la elimino
    tendina_tipologie = document.getElementById('tendinaTipologia');
    while (tendina_tipologie.firstChild) 
        tendina_tipologie.removeChild(tendina_tipologie.firstChild);
    
    // Rimetto l'opzione di default
    opt_default = document.createElement('option');
    opt_default.setAttribute('value', '0');
    opt_default.innerHTML = 'Seleziona tipologia';
    tendina_tipologie.appendChild(opt_default);
}

function ottieniTipologie(tendina_categoria)
{
    // Id della categoria
    id_categoria = tendina_categoria.value;

    // Se la categoria e' 0 non devo ottenere tipologie
    // ma svuotare la tendina delle tipologie
    if ( id_categoria != 0 )
    {
        // Composizione della query string
        query_string = 'id_categoria=' + id_categoria;
        
        // Richiesta AJAX per ottenere le tipologie
        xhr = new XMLHttpRequest();
        xhr.open("POST", "ottieniTipologiePerCategoria.php");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function(){ callbackPopolaTipologie(xhr) }; // Definisco una funzione di callback implicita che chiama quella sotto
        xhr.send(query_string);
    }
    else
        svuotaTendinaTipologie();
}

function callbackPopolaTipologie()
{
    // Ricevo dallo script lato client una risposta in JSON
    // Effettuo il parsing della risposta per estrarre le tipologie
    tipologie = JSON.parse(xhr.responseText);

    // Per ogni tipologia creo una option all'interno della tendina
    svuotaTendinaTipologie();
    tendina_tipologie = document.getElementById('tendinaTipologia');
    for ( i=0; i < tipologie.length; i++ )
    {
        // Creo la nuova option
        nuova_option = document.createElement('option');
        nuova_option.setAttribute('value', tipologie[i].id_tipo);
        nuova_option.innerHTML = tipologie[i].nome_tipo;

        // Aggancio la nuova option
        tendina_tipologie.appendChild(nuova_option);
    }
}

function azzeraRicercaProdotti()
{
    // Riferimento al form
    form = document.getElementById('ricercaProdotti');

    // Azzero i campi
    contenutoRic = document.getElementsByName('contenutoRicerca')[0];
    contenutoRic.innerHTML = '';
    svuotaTendinaTipologie();
}

function applicaOrdinamento(form_ordinamento)
{
    // Prendo il riferimento al form
    form = document.getElementById(form_ordinamento);

    // Eseguo il submit del form per applicare l'ordinamento
    form.submit();
}

function azzeraFormInserimentoProdotto()
{
    // Azzeramento campi form
    document.getElementsByName('nome')[0].value   = "";
    document.getElementsByName('prezzoListino')[0].value   = "";
    document.getElementsByName('id_categoria')[0].value   = 0;
    document.getElementsByName('id_tipologia')[0].value   = 0;
    document.getElementsByName('specifiche')[0].value   = "";
    document.getElementsByName('immagine')[0].value   = null;
}

function azzeraFormOffertaSpeciale()
{
    // Azzeramento campi form
    document.getElementsByName('dataInizio')[0].value   = "";
    document.getElementsByName('dataFine')[0].value   = "";
    document.getElementsByName('percentuale')[0].value   = "";
    document.getElementsByName('crediti')[0].value   = "";
}