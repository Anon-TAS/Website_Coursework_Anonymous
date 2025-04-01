<?php
session_start();
$session_id = session_id();

require '/home/s2694679/public_html/Website/database/login.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Fetch stored sequences for this session
$sql = "SELECT sequence, accession FROM sequences WHERE session_id = :session_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':session_id' => $session_id]);
$sequences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. If no sequences, show a message and exit
if (count($sequences) == 0) {
    echo "<p>No sequences stored for conservation analysis.</p>";
    exit;
}

// 3. Write sequences to input.fasta
$fasta_file = 'input.fasta';
file_put_contents($fasta_file, ""); // Clear old file

foreach ($sequences as $seq) {
    $header = ">" . $seq['accession'];
    $sequence = strtoupper(trim($seq['sequence']));
    file_put_contents($fasta_file, "$header\n$sequence\n", FILE_APPEND);
}

// 4. Run Clustal Omega to align sequences
$aligned_file = 'aligned.fasta';
$clustal_command = "clustalo -i $fasta_file -o $aligned_file --outfmt=fasta --force";
exec($clustal_command, $output, $return_var);
if ($return_var !== 0) {
    echo "<p>Error running Clustal Omega!</p>";
    exit;
}

// 5. Run Python script to generate conservation plot
$python_command = "python3 /home/s2694679/public_html/Website/scripts/conservation_plot.py";
exec($python_command, $py_output, $py_return);
if ($py_return !== 0) {
    echo "<p>Error generating conservation plot!</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Conservation Analysis</title>
    <link rel="stylesheet" href="/~s2694679/Website/assets/style.css">
</head>
<body>
    <div class="conservation-box">
        <h2>Protein Conservation Analysis</h2>
        <p>Conservation across your aligned protein sequences:</p>
        <img src="/~s2694679/Website/assets/conservation_plot.png" alt="Conservation Plot" style="max-width: 100%; border: 1px solid #ccc;">
        <br><br>
    </div>
</body>
</html>