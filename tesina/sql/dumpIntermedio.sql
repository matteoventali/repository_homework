use unitecno;

-- Inserimento di due clienti per la popolazione intermedia dell'applicazione
insert into UTENTI(nome, cognome, indirizzo, citta, cap, reputazione, username, mail, password, ruolo, data_registrazione) values
                ('Lionel', 'Messi', 'Via Roma 11', 'Vibo Valentia', '89900', 1, 'lionel.messi', 'messi@unitecno.it', 
                    SHA2('unitecno', 256), 'C', DATE(NOW()));

insert into UTENTI(nome, cognome, indirizzo, citta, cap, reputazione, username, mail, password, ruolo, data_registrazione) values
                ('Cristiano', 'Ronaldo', 'Via Milano 1', 'Pordenone', '33170', 1, 'cristiano.ronaldo', 'ronaldo@unitecno.it', 
                    SHA2('unitecno', 256), 'C', DATE(NOW()));