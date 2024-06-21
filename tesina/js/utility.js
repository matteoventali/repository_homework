function scegliSfondoCasuale()
{
    nome_file = ["smartphone.jpg", "console.jpg", "elettrodomestici.jpg", "laptop.png", "televisore.jpg"];
    scelta_casuale = Math.floor(Math.random() * 5);

    // Set del background tra quelli disponibili
    cat = document.getElementById("sezioneCatalogo");
    cat.style.backgroundImage="url('../img/background/" + nome_file[scelta_casuale] + "')";
}

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