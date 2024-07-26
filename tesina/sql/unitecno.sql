drop database if exists unitecno;
create database unitecno;
use unitecno;

create table UTENTI(
    id          integer auto_increment not null,
    nome        varchar(50) not null,
    cognome     varchar(50) not null,
    indirizzo   varchar(70) not null,
    citta       varchar(50) not null,
    cap         varchar(5) not null,
    reputazione integer default 1,
    data_registrazione date,
    stato       char(1) default 'A',
    username    varchar(50) not null,
    mail        varchar(50) not null,
    password    char(64) not null,
    ruolo   char(1) default 'C',
    saldo_standard integer default 0,
    primary key (id),
    unique (mail),
    unique (username)
);

-- Inserimento di un account admin e di un account gestore
insert into UTENTI(nome, cognome, indirizzo, citta, cap, reputazione, username, mail, password, ruolo) values
                ('Matteo', 'Ventali', 'Via A. Caldara 8', 'Latina', '04100', 200, 'matteo.ventali', 'ventali@unitecno.it', 
                    SHA2('unitecno', 256), 'A');

insert into UTENTI(nome, cognome, indirizzo, citta, cap, reputazione, username, mail, password, ruolo) values
                ('Stefano', 'Rosso', 'Via Santa Fecitola 2', 'Latina', '04100', 200, 'stefano.rosso', 'rosso@unitecno.it', 
                    SHA2('unitecno', 256), 'G');