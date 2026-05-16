CREATE DATABASE competition;
USE competition;

CREATE TABLE equipe (
    codee INT PRIMARY KEY,
    nome VARCHAR(255)
);

INSERT INTO equipe (codee, nome) VALUES
(0, 'liverpool'),
(1, 'ac milan'),
(2, 'real madrid');

CREATE TABLE pays (
    codep INT PRIMARY KEY,
    nomp VARCHAR(255)
);

INSERT INTO pays (codep, nomp) VALUES
(0, 'angleterre'),
(1, 'italie'),
(2, 'espagne');

CREATE TABLE etape (
    numet INT PRIMARY KEY,
    date DATE,
    villedep VARCHAR(255),
    villearr VARCHAR(255),
    nbkm INT
);

INSERT INTO etape (numet, date, villedep, villearr, nbkm) VALUES
(0, '2023-11-07', 'paris', 'marseille', 774),
(1, '2023-11-08', 'tunis', 'nabeul', 60),
(2, '2023-11-09', 'cotonu', 'ibadan', 253);

CREATE TABLE coureur (
    numc INT PRIMARY KEY,
    nomc VARCHAR(255),
    codee INT,
    codep INT,
    FOREIGN KEY (codee) REFERENCES equipe(codee),
    FOREIGN KEY (codep) REFERENCES pays(codep)
);

INSERT INTO coureur (numc, nomc, codee, codep) VALUES
(0, 'john', 0, 0),
(1, 'akrem', 0, 1),
(2, 'ali', 1, 2),
(3, 'brad', 1, 1),
(4, 'chris', 2, 2),
(5, 'james', 2, 0);

CREATE TABLE participation (
    numc INT,
    numet INT,
    tempsrealise INT,
    PRIMARY KEY (numc, numet),
    FOREIGN KEY (numc) REFERENCES coureur(numc),
    FOREIGN KEY (numet) REFERENCES etape(numet)
);


INSERT INTO participation (numc, numet, tempsrealise) VALUES
(0, 0, 10),
(0, 1, 20),
(1, 2, 30),
(1, 3, 40),
(2, 4, 50),
(2, 5, 60);

SELECT nomc FROM coureur WHERE numc IN (SELECT numc FROM participation WHERE tempsrealise > 30);

SELECT * FROM coureur WHERE nomc LIKE 'a%';

SELECT COUNT(*) FROM participation WHERE numet = 2;

ALTER TABLE equipe ADD COLUMN couleureq VARCHAR(20);
