<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$session_id = session_id();
echo "<p>Session ID being used to store motifs: <strong>$session_id</strong></p>";

require '/home/s2694679/public_html/Website/database/login.php';

//delete any duplicates that are from the same session, so when rerun doesnt add a copy everytime.
$delete = $pdo->prepare("DELETE FROM motifs WHERE session_id = :session_id");
$delete->execute([':session_id' => $session_id]);

// File containing parsed motifs
$motif_file = "/home/s2694679/public_html/Website/motifs_parsed.txt";

if (!file_exists($motif_file)) {
    die("Error: Motif results file not found.");
}

$motifs = file($motif_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($motifs)) {
    die("No motif results found in the file.");
}

// Prepare SQL insertion
$sql = "INSERT INTO motifs (accession, motif_name, start_pos, end_pos, session_id)
        VALUES (:accession, :motif_name, :start_pos, :end_pos, :session_id)";
$stmt = $pdo->prepare($sql);

foreach ($motifs as $line) {
    $parts = explode("|", $line);

    if (count($parts) !== 4) {
        echo "<p>Skipping malformed line: $line</p>";
        continue;
    }

    list($accession, $motif_name, $start_pos, $end_pos) = $parts;

    $stmt->execute([
        ':accession' => $accession,
        ':motif_name' => $motif_name,
        ':start_pos' => $start_pos,
        ':end_pos' => $end_pos,
        ':session_id' => $session_id
    ]);
}

echo "Motifs successfully stored in the database!";
?>
