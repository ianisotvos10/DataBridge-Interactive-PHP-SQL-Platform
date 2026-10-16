-- 1. CREARE TABELE

-- Tabelul Aeronave
CREATE TABLE Aeronave (
    idav            INT          NOT NULL PRIMARY KEY,
    numeav          VARCHAR(50)  NOT NULL,
    gama_croaziera  INT          NOT NULL
);

-- Tabelul Angajati
CREATE TABLE Angajati (
    idan     INT           NOT NULL PRIMARY KEY,
    numean   VARCHAR(50)   NOT NULL,
    functie  VARCHAR(40)   NOT NULL,
    salariu  DECIMAL(10,2) NOT NULL
);

-- Tabelul Zboruri
CREATE TABLE Zboruri (
    nrz       INT          NOT NULL PRIMARY KEY,
    de_la     VARCHAR(40)  NOT NULL,
    la        VARCHAR(40)  NOT NULL,
    distanta  INT          NOT NULL,
    plecare   TIME         NOT NULL,
    sosire    TIME         NOT NULL,
    zi        VARCHAR(15)  NULL
);

-- Tabelul Certificare
CREATE TABLE Certificare (
    idan  INT NOT NULL,
    idav  INT NOT NULL,
    PRIMARY KEY (idan, idav),
    FOREIGN KEY (idan) REFERENCES Angajati(idan) ON DELETE CASCADE,
    FOREIGN KEY (idav) REFERENCES Aeronave(idav) ON DELETE CASCADE
);

-- Tabelul Exceptii (pentru procedura stocata)
CREATE TABLE Exceptii (
    idan         INT           NOT NULL,
    numean       VARCHAR(50)   NOT NULL,
    functie      VARCHAR(40)   NOT NULL,
    salariu      DECIMAL(10,2) NOT NULL,
    tip_exceptie VARCHAR(50),
    idav         INT
);

-- 2. CONSTRANGERI (CHECK)

-- a) Nume aeronava
ALTER TABLE Aeronave
    ADD CONSTRAINT chk_aeronave_numeav
    CHECK (numeav LIKE 'BOEING%' OR numeav LIKE 'AIRBUS%' OR numeav LIKE 'ATR%');

-- b) Constrangere timp si distanta
ALTER TABLE Zboruri
    ADD CONSTRAINT chk_zboruri_timp_distanta
    CHECK (
        (TIMESTAMPDIFF(HOUR, plecare, sosire) <= 1)
        OR
        (distanta > 800)
    );

-- 3. POPULARE CU DATE

-- Aeronave
INSERT INTO Aeronave VALUES (1, 'BOEING 737-800', 5420);
INSERT INTO Aeronave VALUES (2, 'BOEING 737-700', 6000);
INSERT INTO Aeronave VALUES (3, 'BOEING 737-300', 4600);
INSERT INTO Aeronave VALUES (4, 'BOEING 747-400', 13450);
INSERT INTO Aeronave VALUES (5, 'AIRBUS A320', 6150);
INSERT INTO Aeronave VALUES (6, 'AIRBUS A330', 11750);
INSERT INTO Aeronave VALUES (7, 'AIRBUS A350', 15000);
INSERT INTO Aeronave VALUES (8, 'ATR 42', 1500);
INSERT INTO Aeronave VALUES (9, 'ATR 72', 1800);
INSERT INTO Aeronave VALUES (10,'AIRBUS A380', 15200);

-- Angajati
INSERT INTO Angajati VALUES (101, 'George Iulian',   'pilot',              9000);
INSERT INTO Angajati VALUES (102, 'Popescu Mihai',   'pilot',             11000);
INSERT INTO Angajati VALUES (103, 'Ionescu Andrei',  'pilot',              7500);
INSERT INTO Angajati VALUES (104, 'Marin Elena',     'pilot',             12000);
INSERT INTO Angajati VALUES (201, 'Stan Maria',      'stewardesa',         4500);
INSERT INTO Angajati VALUES (202, 'Lupu Ana',        'stewardesa',         4700);
INSERT INTO Angajati VALUES (301, 'Georgescu Radu',  'director tehnic',   15000);
INSERT INTO Angajati VALUES (302, 'Barbu Stefan',    'director operational', 16000);

-- Certificari
INSERT INTO Certificare VALUES (102, 5);
INSERT INTO Certificare VALUES (102, 6);
INSERT INTO Certificare VALUES (103, 8);
INSERT INTO Certificare VALUES (103, 9);
INSERT INTO Certificare VALUES (104, 4);
INSERT INTO Certificare VALUES (104, 7);
INSERT INTO Certificare VALUES (101, 1);
INSERT INTO Certificare VALUES (101, 2);
INSERT INTO Certificare VALUES (101, 3);
-- Cele adaugate in cerinta 04.07
INSERT INTO Certificare VALUES (101, 5);
INSERT INTO Certificare VALUES (103, 5);

-- Zboruri
INSERT INTO Zboruri VALUES (1001, 'Cluj', 'Bucuresti', 320,  '10:00:00', '11:00:00', 'Luni');
INSERT INTO Zboruri VALUES (1002, 'Cluj', 'Paris',     1650, '07:00:00', '09:30:00', 'Marti');
INSERT INTO Zboruri VALUES (1003, 'Bucuresti', 'Londra', 2100, '08:00:00', '11:00:00', 'Miercuri');
INSERT INTO Zboruri VALUES (1004, 'Cluj', 'Frankfurt', 1200, '14:00:00', '16:00:00', 'Joi');
INSERT INTO Zboruri VALUES (1005, 'Timisoara', 'Roma', 950,  '06:00:00', '07:45:00', 'Vineri');
INSERT INTO Zboruri VALUES (1006, 'Bucuresti', 'Madrid', 2470, '12:00:00', '15:20:00', 'Sambata');
INSERT INTO Zboruri VALUES (1007, 'Cluj', 'Amsterdam', 1500, '09:00:00', '11:20:00', 'Luni');
INSERT INTO Zboruri VALUES (1008, 'Iasi', 'Paris',     1750, '13:00:00', '15:40:00', 'Marti');
INSERT INTO Zboruri VALUES (1009, 'Bucuresti', 'Munchen', 1190, '10:00:00', '11:50:00', 'Miercuri');
INSERT INTO Zboruri VALUES (1010, 'Cluj', 'Lisabona',  2980, '05:30:00', '09:10:00', 'Duminica');

-- 5. TRIGGER (Tradus din 04.09.a)
DELIMITER //

CREATE TRIGGER trg_certificare_insert
AFTER INSERT ON Certificare
FOR EACH ROW
BEGIN
    UPDATE Angajati
    SET salariu = salariu + 50
    WHERE idan = NEW.idan;
END //

CREATE TRIGGER trg_certificare_delete
AFTER DELETE ON Certificare
FOR EACH ROW
BEGIN
    UPDATE Angajati
    SET salariu = salariu - 50
    WHERE idan = OLD.idan;
END //

DELIMITER ;