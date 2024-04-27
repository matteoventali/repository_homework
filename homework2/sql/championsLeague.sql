drop database if exists champions_league;
create database champions_league;
use champions_league;

create table SQUADRE(
    id      integer auto_increment not null,
    nome    varchar(50) not null,
    unique (nome),
    primary key(id)
);

create table PARTITE(
    id              integer auto_increment not null,
    data            DATE not null,
    squadra_casa    integer not null,
    squadra_ospite  integer not null,
    goal_casa        integer unsigned not null,
    goal_ospite      integer unsigned not null,
    primary key (id),
    unique (squadra_casa, squadra_ospite, data),
    constraint fk_partite_squadra_casa foreign key (squadra_casa)
            references SQUADRE(id),
    constraint fk_partite_squadra_ospite foreign key (squadra_ospite)
            references SQUADRE(id)
);

create table UTENTI(
    id          integer auto_increment not null,
    nome        varchar(50) not null,
    cognome     varchar(50) not null,
    mail        varchar(50) not null,
    password    char(64) not null,
    tipologia   char(1) default 'U',
    primary key (id),
    unique (mail)
);

insert into SQUADRE(nome) values ('Real Madrid');
insert into SQUADRE(nome) values ('Inter');
insert into SQUADRE(nome) values ('Milan');
insert into SQUADRE(nome) values ('Manchester United');
insert into SQUADRE(nome) values ('Barcellona');
insert into SQUADRE(nome) values ('Juventus');
insert into SQUADRE(nome) values ('Bayer Monaco');
insert into SQUADRE(nome) values ('Liverpool');
insert into UTENTI(nome, cognome, mail, password, tipologia) values ('admin', 'admin', 'admin@admin.it', SHA2('admin', 256), 'A');

create view SQUADRA_PARTITE_VINTE as
    select s.id, s.nome, COUNT(*) as n_vinte
    from SQUADRE s, PARTITE p
    where (s.id = p.squadra_casa and p.goal_casa > p.goal_ospite)
        or ( s.id = p.squadra_ospite and p.goal_ospite > p.goal_casa)
    group by s.id;

create view SQUADRA_PARTITE_PERSE as
    select s.id, s.nome, COUNT(*) as n_perse
    from SQUADRE s, PARTITE p
    where (s.id = p.squadra_casa and p.goal_casa < p.goal_ospite)
        or ( s.id = p.squadra_ospite and p.goal_ospite < p.goal_casa)
    group by s.id;

create view SQUADRA_PARTITE_PAREGGIATE as
    select s.id, s.nome, COUNT(*) as n_pareggiate
    from SQUADRE s, PARTITE p
    where (s.id = p.squadra_casa or s.id = p.squadra_ospite) and p.goal_ospite = p.goal_casa
    group by s.id;

create view SQUADRA_GOL_CASA as
    select s.id, s.nome, sum(p.goal_casa) as gol_fatti, sum(p.goal_ospite) as gol_subiti
    from SQUADRE s, PARTITE p       
    where s.id = p.squadra_casa
    group by s.id;

create view SQUADRA_GOL_OSPITE as
    select s.id, s.nome, sum(p.goal_ospite) as gol_fatti, sum(p.goal_casa) as gol_subiti
    from SQUADRE s, PARTITE p       
    where s.id = p.squadra_ospite
    group by s.id;

create view RESOCONTO_COMPLETO as
    select s.id, s.nome, v.n_vinte, p.n_perse, par.n_pareggiate, 
                        gc.gol_fatti as gol_fatti_casa, go.gol_fatti as gol_fatti_ospite,
                        gc.gol_subiti as gol_subiti_casa, go.gol_subiti as gol_subiti_ospite
    from SQUADRE s left outer join SQUADRA_PARTITE_VINTE v  on s.id = v.id
            left outer join SQUADRA_PARTITE_PERSE p         on s.id = p.id
            left outer join SQUADRA_PARTITE_PAREGGIATE par  on s.id = par.id
            left outer join SQUADRA_GOL_CASA gc             on s.id = gc.id
            left outer join SQUADRA_GOL_OSPITE go           on s.id = go.id;