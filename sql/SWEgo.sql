-- Project:	SWEgo
-- Author:	Bertolini Luca
--			Bonolo Marco
--			Carlin Mauro
--			Tintorri Nicola

-- Create database
create database swego;

-- Use clause
use swego;

-- Table: users
create table if not exists users (
	id int auto_increment primary key,
	groupname varchar(100) not null unique,
	projectname varchar(100) not null unique,
	password varchar(100) not null,
	type varchar(20) not null
)engine=INNODB;

-- Table: login
create table if not exists login (
	userid int not null,
	date_time datetime not null,
	primary key(userid, date_time),
	foreign key(userid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: usecase
create table if not exists usecase (
	id int auto_increment primary key,
	usecaseid varchar(50) not null,
	name varchar(200) not null,
	description varchar(1000) not null,
	precondition varchar(1000) not null,
	postcondition varchar(1000) not null,
	mainscenario varchar(1000) not null,
	alternativescenario varchar(1000),
	generalization boolean not null,
	parent int,
	projectid int,
	foreign key(parent) references usecase(id) on delete cascade on update cascade,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: sources
create table if not exists sources (
	id int auto_increment primary key,
	name varchar(100) not null,
	description varchar(1000) not null,
	projectid int,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: requirements
create table if not exists requirements (
	id int auto_increment primary key,
	requirementid varchar(50) not null,
	description varchar(1000) not null,
	type varchar(100) not null,
	importance varchar(100) not null,
	satisfied varchar(100) not null,
	parent int,
	source int,
	projectid int,
	foreign key(parent) references requirements(id) on delete cascade on update cascade,
	foreign key(source) references sources(id) on delete cascade on update cascade,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: actors
create table if not exists actors (
	id int auto_increment primary key,
	name varchar(100) not null,
	description varchar(1000) not null,
	projectid int,
	foreign key(projectid) references users(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: usecaseactors
create table if not exists usecaseactors (
	usecaseid int,
	actorsid int,
	primary key(usecaseid, actorsid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(actorsid) references actors(id) on delete cascade on update cascade
)engine=INNODB;

-- Table usecaseextensions
create table if not exists usecaseextensions (
	usecaseid int,
	extendedusecaseid int,
	primary key(usecaseid, extendedusecaseid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(extendedusecaseid) references usecase(id) on delete cascade on update cascade
)engine=INNODB;

-- Table usecaseinclusions
create table if not exists usecaseinclusions (
	usecaseid int,
	includedusecaseid int,
	primary key(usecaseid, includedusecaseid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(includedusecaseid) references usecase(id) on delete cascade on update cascade
)engine=INNODB;

-- Table: usecaserequirements
create table if not exists usecaserequirements (
	usecaseid int,
	requirementid int,
	primary key(usecaseid, requirementid),
	foreign key(usecaseid) references usecase(id) on delete cascade on update cascade,
	foreign key(requirementid) references requirements(id) on delete cascade on update cascade
)engine=INNODB;

-- Dump
--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `groupname`, `projectname`, `password`, `type`) VALUES
(1, 'admin', 'admin', 'bb017ba90f714a59da19ebcfe41ff68db41c5ab40e4eb0d17f40b2db3cd4cbbf', 'admin'),
(2, 'user', 'user', 'f2894c2d4e1b1ce6ccd9c7ff7111082f7204c3a64073f863f7a79954acfde891', 'user');

--
-- Dump dei dati per la tabella `sources`
--

INSERT INTO `sources` (`id`, `name`, `description`, `projectid`) VALUES
(1, 'Capitolato', 'Capitolato d''appalto C3', 2),
(2, 'Interno', 'Requisiti individuati dal gruppo', 2);

--
-- Dump dei dati per la tabella `actors`
--

INSERT INTO `actors` (`id`, `name`, `description`, `projectid`) VALUES
(1, 'Utente autenticato', 'Utente autenticato all''interno del sistema', 2),
(2, 'Amministratore', 'Amministratore del sistema', 2);

--
-- Dump dei dati per la tabella `usecase`
--

INSERT INTO `usecase` (`id`, `usecaseid`, `name`, `description`, `precondition`, `postcondition`, `mainscenario`, `alternativescenario`, `generalization`, `parent`, `projectid`) VALUES
(1, 'UC1', 'Benvenuto', 'Il sistema dÃ  il benvenuto all''utente', 'Il sistema Ã¨ accesso e funzionante', 'L''utente ha ricevuto il benvenuto dal sistema', 'Il sistema mostra all''utente un messaggio di benvenuto', '', 0, NULL, 2),
(2, 'UC2', 'Eliminazione utente', 'L''amministratore elimina un utente del sistema', 'L''amministratore si trova nella sezione dedicata all''eliminazione degli utenti', 'L''amministratore ha eliminato l''utente da lui deciso', 'L''amministratore elimina l''utente scelto dal sistema', '', 0, NULL, 2),
(3, 'UC2.1', 'Scelta utente da eliminare', 'L''amministratore seleziona l''utente da eliminare', 'L''amministratore si trova nella sezione dedicata all''eliminazione degli utenti', 'L''amministratore ha eliminato l''utente da lui deciso', 'L''amministratore sceglie quale utente eliminare', '', 0, 2, 2),
(4, 'UC3', 'Visualizzazione profilo', 'L''utente autenticato puÃ² visualizzare i dati del proprio profilo', 'L''utente si trova si trova in qualsiasi sezione del sistema, tranne la pagina del proprio profilo', 'L''utente ha visualizzato i dati del suo profilo', 'L''utente autenticato accede al proprio profilo e visualizza i dati', '', 0, NULL, 2);

--
-- Dump dei dati per la tabella `usecaseactors`
--

INSERT INTO `usecaseactors` (`usecaseid`, `actorsid`) VALUES
(1, 1),
(2, 2),
(3, 2),
(4, 1);

--
-- Dump dei dati per la tabella `requirements`
--
INSERT INTO `requirements` (`id`, `requirementid`, `description`, `type`, `importance`, `satisfied`, `parent`, `source`, `projectid`) VALUES
(1, 'R0F1', 'L''utente autenticato deve poter visualizzare il proprio profilo', 'Funzionale', 'Obbligatorio', 'Implementato', NULL, 2, 2),
(2, 'R0F2', 'L''amministratore deve poter eliminare gli utenti del sistema', 'Funzionale', 'Obbligatorio', 'Non implementato', NULL, 1, 2),
(3, 'R1F2.1', 'L''amministratore deve poter scegliere l''utente da eliminare', 'Funzionale', 'Desiderabile', 'Non implementato', 2, 1, 2),
(4, 'R0V1', 'Il sistema deve funzionare correttamente in tutti i dispositivi mobile.', 'Di Vincolo', 'Obbligatorio', 'Implementato', NULL, 1, 2);

--
-- Dump dei dati per la tabella `usecaserequirements`
--

INSERT INTO `usecaserequirements` (`usecaseid`, `requirementid`) VALUES
(2, 2),
(3, 2),
(3, 3),
(4, 1);