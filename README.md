# DataBridge: Interactive PHP & SQL Platform 
*(Alte variante de titlu: "Dynamic Web-to-SQL Architecture" / "Smart Database Manager")*

Acest proiect reprezintă o platformă web interactivă construită pentru a gestiona și interoga o bază de date relațională. Arhitectura combină un frontend dinamic și stilizat (PHP, CSS) cu un backend SQL robust, demonstrând integrarea completă de la nivelul interfeței utilizatorului până la structurile de date.

## 🛠️ Tech Stack
*   **Baze de Date:** SQL (Creare scheme, populare date, integritate referențială)
*   **Logică Backend (DB):** Proceduri Stocate (Stored Procedures) pentru operațiuni complexe și optimizarea interogărilor direct pe serverul de baze de date.
*   **Frontend & Web Logic:** PHP, HTML5, CSS3.

## 🗂️ Structura Arhitecturii
Sistemul este împărțit clar în două straturi:
1.  **Stratul de Date (Data Layer - `/sql`):** Conține scripturile de inițializare a bazei de date (`creare_si_populare.sql`) care definesc tabelele, relațiile și datele de test, alături de procedurile stocate (`proceduri_stocate.sql`) care încapsulează logica de business.
2.  **Stratul de Aplicație (App Layer - `/app`):** Interfața web. Conține conexiunea la baza de date (`db.php`), logica de afișare și interacțiune (`index.php`) și design-ul vizual (`style.css`).

## 🚀 Funcționalități Cheie
*   **Conexiune securizată:** Integrare PHP-SQL prin fișier de configurare dedicat.
*   **Rulare procedurală:** Utilizarea procedurilor stocate pentru a muta logica complexă în motorul bazei de date, crescând performanța.
*   **Interfață user-friendly:** Design curat (CSS custom) pentru vizualizarea și manipularea eficientă a seturilor de date.

*(Aici poți adăuga 1-2 propoziții despre domeniul bazei de date: ex. "Baza de date modelează un sistem de gestiune pentru o bibliotecă / magazin / spital etc.")*
