<?php
require 'db.php';
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroManager Pro</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

    <?php if ($page == 'home'): ?>
        <div id="radar-overlay">
            <div class="radar-spinner"></div>
            <div class="loading-text">SYSTEM INITIALIZING... by IANIS OTVOS</div>
        </div>
    <?php else: ?>
        <div id="cloud-overlay">
            <div class="cloud-cluster cluster-left">
                <div class="graphic-cloud c1"></div>
                <div class="graphic-cloud c2"></div>
                <div class="graphic-cloud c4"></div>
            </div>
            <div class="cloud-cluster cluster-right">
                <div class="graphic-cloud c3"></div>
                <div class="graphic-cloud c1"></div>
                <div class="graphic-cloud c2"></div>
            </div>
        </div>
    <?php endif; ?>

    <div id="flying-plane">✈️</div>

    <div class="sidebar">
        <div class="brand-box">
            <div class="mini-radar"></div>
            <h3>AeroManager</h3>
        </div>
        
        <div class="nav-links">
            <a href="?page=home" class="<?= $page=='home'?'active':'' ?>">🏠 Acasă</a>
            <a href="?page=q3a" class="<?= $page=='q3a'?'active':'' ?>">📏 3.a Distanță</a>
            <a href="?page=q3b" class="<?= $page=='q3b'?'active':'' ?>">👔 3.b Directori</a>
            <a href="?page=q4a" class="<?= $page=='q4a'?'active':'' ?>">🛫 4.a Airbus</a>
            <a href="?page=q4b" class="<?= $page=='q4b'?'active':'' ?>">👥 4.b Perechi</a>
            <a href="?page=q5a" class="<?= $page=='q5a'?'active':'' ?>">💰 5.a Min Boeing</a>
            <a href="?page=q5b" class="<?= $page=='q5b'?'active':'' ?>">🔍 5.b Căutare Pilot</a>
            <a href="?page=q6a" class="<?= $page=='q6a'?'active':'' ?>">📜 6.a Certificări</a>
            <a href="?page=q6b" class="<?= $page=='q6b'?'active':'' ?>">📊 6.b Statistici</a>
            <a href="?page=proc" class="proc-btn <?= $page=='proc'?'active':'' ?>">🚀 Execută Procedura</a>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <?php
            switch($page) {
                case 'home':
                    echo "<h1>Panou de Control</h1>";
                    echo "<p>Sistem conectat. Parametrii nominali.</p>";
                    echo "<p>Bine ai venit în interfața de administrare a flotei. Selectează o operațiune din stânga.</p>";
                    break;
                case 'q3a':
                    echo "<h2>Zboruri (700km - 1500km)</h2>";
                    $stmt = $pdo->query("SELECT nrz, de_la, la, distanta, plecare, sosire, zi FROM Zboruri WHERE distanta BETWEEN 700 AND 1500 ORDER BY plecare ASC, la DESC");
                    renderTable($stmt);
                    break;
                case 'q3b':
                    echo "<h2>Directori</h2>";
                    $stmt = $pdo->query("SELECT idan, numean, functie, salariu FROM Angajati WHERE LOWER(functie) LIKE '%director%' ORDER BY functie, numean");
                    renderTable($stmt);
                    break;
                case 'q4a':
                    echo "<h2>Zboruri Airbus</h2>";
                    $stmt = $pdo->query("SELECT DISTINCT z.nrz, z.de_la, z.la, z.distanta FROM Zboruri z JOIN Aeronave a ON a.gama_croaziera > z.distanta WHERE a.numeav LIKE 'AIRBUS%'");
                    renderTable($stmt);
                    break;
                case 'q4b':
                    echo "<h2>Perechi Angajați (Aceeași navă)</h2>";
                    $stmt = $pdo->query("SELECT c1.idan AS 'Pilot 1', c2.idan AS 'Pilot 2', c1.idav AS 'ID Aeronava' FROM Certificare c1 JOIN Certificare c2 ON c1.idav = c2.idav AND c1.idan < c2.idan");
                    renderTable($stmt);
                    break;
                case 'q5a':
                    echo "<h2>Salariu Minim (Boeing)</h2>";
                    $stmt = $pdo->query("SELECT a.numean, a.salariu FROM Angajati a WHERE a.functie = 'pilot' AND EXISTS (SELECT 1 FROM Certificare c JOIN Aeronave ae ON ae.idav = c.idav WHERE c.idan = a.idan AND ae.numeav LIKE 'BOEING%') AND a.salariu <= ALL (SELECT a2.salariu FROM Angajati a2 WHERE a2.functie = 'pilot' AND EXISTS (SELECT 1 FROM Certificare c2 JOIN Aeronave ae2 ON ae2.idav = c2.idav WHERE c2.idan = a2.idan AND ae2.numeav LIKE 'BOEING%'))");
                    renderTable($stmt);
                    break;
                case 'q5b':
                    echo "<h2>Căutare Zboruri Pilot</h2>";
                    echo '<div class="form-box">
                            <form id="searchForm" method="GET" action="index.php">
                                <input type="hidden" name="page" value="q5b">
                                <label style="display:block; margin-bottom:10px; color:#fff;">Employee ID:</label>
                                <input type="text" name="pilot_id" placeholder="Ex: 101" required>
                                <button type="submit" onclick="flyPlane(event, this, \'searchForm\')">CAUTĂ</button>
                            </form>
                          </div>';
                    if (isset($_GET['pilot_id'])) {
                        $id = intval($_GET['pilot_id']);
                        echo "<br><br><h3>Rezultate pentru ID: <span style='color:var(--neon-blue)'>$id</span></h3>";
                        $stmt = $pdo->prepare("SELECT z.nrz, z.de_la, z.la, z.distanta FROM Zboruri z WHERE EXISTS (SELECT 1 FROM Certificare c JOIN Aeronave ae ON ae.idav = c.idav WHERE c.idan = :id AND ae.gama_croaziera > z.distanta)");
                        $stmt->execute(['id' => $id]);
                        renderTable($stmt);
                        echo '<a href="?page=q5b" style="color:#fff; display:block; margin-top:20px;">↺ Resetare</a>';
                    }
                    break;
                case 'q6a':
                    echo "<h2>Certificări per Pilot</h2>";
                    $stmt = $pdo->query("SELECT a.idan, a.numean, COUNT(c.idav) AS 'Numar Aeronave' FROM Angajati a JOIN Certificare c ON c.idan = a.idan WHERE a.functie = 'pilot' GROUP BY a.idan, a.numean");
                    renderTable($stmt);
                    break;
                case 'q6b':
                    echo "<h2>Statistici Salarii</h2>";
                    $stmt = $pdo->query("SELECT functie, MIN(salariu) AS min, AVG(salariu) AS mediu, MAX(salariu) AS max FROM Angajati GROUP BY functie");
                    renderTable($stmt);
                    break;
                case 'proc':
                    echo "<h2>Execuție Procedură Stocată</h2>";
                    echo "<p>Această acțiune va insera piloții în tabela Excepții.</p>";
                    echo '<form id="procForm" method="POST" style="margin: 20px 0;">
                            <input type="hidden" name="run_proc" value="1">
                            <button type="submit" onclick="flyPlane(event, this, \'procForm\')">EXECUTĂ PROCEDURA</button>
                          </form>';
                    if (isset($_POST['run_proc'])) {
                        try {
                            $stmt = $pdo->prepare("CALL Insereaza_Exceptii(5)");
                            $stmt->execute();
                            echo "<div class='success-msg'>✓ Procedura executată! Datele au fost inserate mai jos.</div>";
                            echo "<h3>Conținut Tabela Excepții:</h3>";
                            $stmtCheck = $pdo->query("SELECT * FROM Exceptii");
                            renderTable($stmtCheck);
                        } catch (PDOException $e) {
                            echo "<div style='color:#ef4444;'>Eroare: " . $e->getMessage() . "</div>";
                        }
                    }
                    break;
                default: echo "<p>Selectați o opțiune.</p>";
            }
            ?>
        </div>
    </div>

    <script>
    window.addEventListener('load', function() {
        
        const radar = document.getElementById('radar-overlay');
        if (radar) {
            setTimeout(() => {
                radar.classList.add('hidden');
            }, 1800); 
        }

        const clouds = document.getElementById('cloud-overlay');
        if (clouds) {
            setTimeout(() => {
                clouds.classList.add('open');
            }, 100); 
        }
    });

    function flyPlane(e, btn, formId) {
        e.preventDefault();
        const plane = document.getElementById('flying-plane');
        const rect = btn.getBoundingClientRect();
        plane.style.left = rect.left + (rect.width / 2) - 20 + 'px';
        plane.style.top = rect.top + 'px';
        plane.style.opacity = '1';
        plane.classList.add('takeoff');
        setTimeout(() => { document.getElementById(formId).submit(); }, 1200);
    }
    </script>

</body>
</html>

<?php
function renderTable($stmt) {
    $rows = $stmt->fetchAll();
    if (!$rows) { echo "<div style='padding:20px; color:#aaa; font-style:italic;'>Fără rezultate.</div>"; return; }
    echo "<table><thead><tr>";
    foreach (array_keys($rows[0]) as $col) { echo "<th>" . htmlspecialchars(ucwords(str_replace('_', ' ', $col))) . "</th>"; }
    echo "</tr></thead><tbody>";
    foreach ($rows as $row) {
        echo "<tr>";
        foreach ($row as $k => $v) {
            if (strpos($k, 'salariu') !== false || strpos($k, 'mediu') !== false || strpos($k, 'min') !== false || strpos($k, 'max') !== false) 
                echo "<td class='number'>" . number_format($v, 2) . "</td>";
            else 
                echo "<td>" . htmlspecialchars($v) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}
?>