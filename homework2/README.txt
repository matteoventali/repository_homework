Studenti: Matteo Ventali (1985026), Stefano Rosso (2001015)

I file dell'homework sono organizzati secondo la seguente struttura:
- php/ ----> directory che contiene gli script php del sito web. Tra questi vi e' anche lo script
             install.php per la creazione automatica del database;
- css/ ----> directory che contiene i file css del sito web;
- img/ ----> directory che contiene le immagini sfruttate per la realizzazione del sito web;
- sql/ ----> directory che contiene il file sql sfruttato per la creazione del database.
Inoltre e' presente lo script index.php utile all'avvio automatico dell'applicazione
quando si richiede la risorsa http://...qualche_dominio.../homework2

L'obiettivo dell'homework e' la realizzazione di un sito web riguardo un torneo calcistico ad 8 squadre
denominato Champions League. Il torneo e' un campionato che prevede girone d'andata e ritorno.
Esistono due tipologie di account:
1) classico;
2) admin;
Le funzionalita' offerte dal sito web sono:
1) visualizzazione della classifica del torneo, in accordo con le partite memorizzate;
2) visualizzazione del resoconto partite disputate;
3) inserimento di una nuova partita (solo per utente di tipo admin);
4) visualizzazione delle squadre partecipanti e riferimento al loro sito web ufficiale.

Per i nuovi utenti è possibile registrare un nuovo account di tipo classico.
Gli account di tipo admin devono essere registrati nel database manualmente, dall'amministratore di sistema.
Lo script di installazione del database prevede la creazione di un utente admin con le seguenti credenziali:
    email: admin@admin.it   password: admin
Tutte le funzionalita' dell'applicazioni sono fornite previa autenticazione dell'utente.

La repository GITHUB per i vari homework e' disponibile al seguente indirizzo:
https://github.com/matteoventali/repository_homework.git
