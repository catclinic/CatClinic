use catclinic;

insert into chat(nom, age, tatouage) values ('Sylvestre', 7, 'ABCDEF');
insert into chat(nom, age, tatouage) values ('Ninja', 1, 'ABCDEG');
insert into chat(nom, age, tatouage) values ('Grosminet', 10, 'ABCDEH');

insert into praticien(nom, prenom) values ('Jean', 'Matou');
insert into praticien(nom, prenom) values ('Félix', 'Lechat');
insert into praticien(nom, prenom) values ('Sylvain', 'Minou');

insert into proprietaire(nom, prenom) values ('Ferrandez', 'Sébastien');
insert into proprietaire(nom, prenom) values ('Ferrandez', 'Sandrine');

insert into proprietaire_chat values (1,1), (1,2), (2,3);

-- le premier utilisateur a comme login invite et comme mot de passe 123cat5
-- admincat aura le même mot de passe
insert into utilisateur(login, motdepasse, id_proprietaire) values ('invite', SHA1('inviteinvite123cat5'), 1);
insert into utilisateur(login, motdepasse, admin) values ('admincat', SHA1('admincat123cat5'), 1);

insert into visite(id_praticien, id_chat, date, prix, observations) values (1,1,current_timestamp(), 79.90, 'Opération bien déroulée');
insert into visite(id_praticien, id_chat, date, prix, observations) values (2,3,current_timestamp(), 79.90, 'Opération bien déroulée');