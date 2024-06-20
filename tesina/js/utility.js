function scegliSfondoCasuale()
{
    nome_file = ["smartphone.jpg", "console.jpg", "elettrodomestici.jpg", "laptop.jpg", "televisore.jpg"];
    scelta_casuale = Math.floor(Math.random() * 4);

    // Set del background tra quelli disponibili
    cat = document.getElementById("sezioneCatalogo");
    cat.style.backgroundImage="url('../img/background/" + nome_file[scelta_casuale] + "')";
}