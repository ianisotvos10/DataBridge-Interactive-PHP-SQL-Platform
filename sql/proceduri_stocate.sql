DELIMITER //

-- PROCEDURA STOCATA (Tradusa din 04.08)
CREATE PROCEDURE Insereaza_Exceptii(IN p_idav INT)
BEGIN
    DECLARE v_min_sal DECIMAL(10,2);
    DECLARE v_max_sal DECIMAL(10,2);

    -- Determinam salariul minim si maxim pentru acea aeronava
    SELECT MIN(a.salariu), MAX(a.salariu)
    INTO   v_min_sal, v_max_sal
    FROM Angajati a
    JOIN Certificare c ON c.idan = a.idan
    WHERE a.functie = 'pilot'
      AND c.idav = p_idav;

    -- Inseram pilotul/pilotii cu salariul minim
    INSERT INTO Exceptii (idan, numean, functie, salariu, tip_exceptie, idav)
    SELECT a.idan, a.numean, a.functie, a.salariu, 'salariul cel mai mic', p_idav
    FROM Angajati a
    JOIN Certificare c ON c.idan = a.idan
    WHERE a.functie = 'pilot'
      AND c.idav = p_idav
      AND a.salariu = v_min_sal;

    -- Inseram pilotul/pilotii cu salariul maxim
    INSERT INTO Exceptii (idan, numean, functie, salariu, tip_exceptie, idav)
    SELECT a.idan, a.numean, a.functie, a.salariu, 'salariul cel mai mare', p_idav
    FROM Angajati a
    JOIN Certificare c ON c.idan = a.idan
    WHERE a.functie = 'pilot'
      AND c.idav = p_idav
      AND a.salariu = v_max_sal;
END //

DELIMITER ;