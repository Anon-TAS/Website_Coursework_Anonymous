<?php
session_start();
$session_id = session_id();

require '/home/s2694679/public_html/Website/database/login.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle clearing sequences BEFORE inserting new ones
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear_sequences'])) {
    $stmt = $pdo->prepare("DELETE FROM sequences WHERE session_id = :session_id");
    $stmt->execute([':session_id' => $session_id]);

    $stmt = $pdo->prepare("DELETE FROM motifs WHERE session_id = :session_id");
    $stmt->execute([':session_id' => $session_id]);

    echo "<p>All stored sequences have been cleared!</p>";
}

// Store sequences to SQL table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_sequences'])) {
    $stored_count = 0;

    foreach ($_POST['selected_sequences'] as $accession) {
        if (!isset($_SESSION['parsed_sequences'][$accession])) {
            echo "<p>Could not find data for accession: $accession</p>";
            continue;
        }

        $data = $_SESSION['parsed_sequences'][$accession];
        $protein_name = $data['protein_name'];
        $sequence = $data['sequence'];
        $taxon = $data['taxon'];

        $sql = "INSERT IGNORE INTO sequences (protein_name, sequence, accession, taxon, query_id, session_id)
                VALUES (:protein_name, :sequence, :accession, :taxon, LAST_INSERT_ID(), :session_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':protein_name' => $protein_name,
            ':sequence' => $sequence,
            ':accession' => $accession,
            ':taxon' => $taxon,
            ':session_id' => $session_id
        ]);

        $stored_count++;#counts the sequences stored into the databases.
    }

    echo "<p>$stored_count sequence(s) successfully stored!</p>";
}

// SQL Stuff
$sql = "SELECT * FROM sequences WHERE session_id = :session_id ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':session_id' => $session_id]);
$sequences = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stored Sequences</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h2>Stored Protein Sequences</h2>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>Protein Name</th>
                <th>Accession</th>
                <th>Taxon</th>
                <th>Sequence (first 150 chars)</th>
            </tr>
            <?php foreach ($sequences as $seq): ?>
                <tr>
                    <td><?= htmlspecialchars($seq['protein_name']) ?></td>
                    <td><?= htmlspecialchars($seq['accession']) ?></td>
                    <td><?= htmlspecialchars($seq['taxon']) ?></td>
                    <td><?= htmlspecialchars(substr($seq['sequence'], 0, 150)) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

<!-- Added in a conditional statement where if all sequences are cleared, then the analysis button disappears (rather than clearing everything - motif tables, text parsing - i felt this was a cleaner option)-->
    <?php if (count($sequences) > 0): ?>
        <form action="analysis.php" method="POST">
            <button type="submit">Analyse All Stored Motifs</button>
        </form>
        <form id="conservationForm" method="POST">
            <button type="submit">Run Conservation Analysis</button>
        </form>

        <div id="conservationResult"></div>
    <?php endif; ?>
    

<!--same condition here as no need to show clear button if no sequencesm just the back to query button-->
    <?php if (count($sequences) > 0): ?>
        <form method="POST" action="">
            <input type="hidden" name="clear_sequences" value="1">
            <button type="submit">Clear Stored Sequences</button>
        </form>
    <?php endif; ?>
    <a href="index.php"><- Back to Query</a>

    <script>
    document.getElementById('conservationForm').addEventListener('submit', function(e) {
        e.preventDefault(); //Stops the page from reloading

        const resultDiv = document.getElementById('conservationResult');
        resultDiv.innerHTML = '<p>Running analysis... Please bare with me!!</p>';

        fetch('conservation_analysis.php', {
            method: 'POST'
        })
        .then(response => response.text())
        .then(data => {
            resultDiv.innerHTML = data; //Inject the result HTML into the page
        })
        .catch(error => {
            console.error('Error:', error);
            resultDiv.innerHTML = '<p>An error occurred while running the analysis.</p>';
        });
    });
    </script>
</body>
</html>