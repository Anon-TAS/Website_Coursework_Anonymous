<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$session_id = session_id();

require '/home/s2694679/public_html/Website/database/login.php';
if (!isset($pdo)) {
    die("Error: Database connection is missing in login.php.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fetched Sequences</title>
    <link rel="stylesheet" href="/~s2694679/Website/assets/style.css">
</head>
<body>
    <div class="container">

        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>

        <h1>Fetched Sequences</h1>
        <?php if (isset($_GET['example'])): ?>
            <div style="text-align: center; color: #444; font-size: 15px; margin-bottom: 20px;">
                <p>Example dataset of <strong>glucose-6-phosphatase proteins from Aves</strong>.</p>
                <p>Select proteins of interest using the checkboxes to process them through the analysis pipeline.</p>
                <p>You can store the proteins for inspection, then view their top motifs, and explore the functional regions.</p>
            </div>
        <?php endif; ?>

        <?php
        $output = $_SESSION['fasta_output'] ?? "";

        //Run script if POST request is met
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $protein_family = escapeshellarg($_POST['protein_family']);
            $taxonomic_group = escapeshellarg($_POST['taxonomic_group']);
            $retmax = isset($_POST['retmax']) ? (int)$_POST['retmax'] : 10;

            $script_path = "/home/s2694679/public_html/Website/scripts/fetch_sequences.py";
            if (!file_exists($script_path)) {
                die("Error: Script not found");
            }

            $command = "python3 $script_path $protein_family $taxonomic_group $retmax";
            $output = shell_exec($command);
            $_SESSION['fasta_output'] = $output;
        } elseif (isset($_GET['limit'])) {
            $output = $_SESSION['fasta_output'] ?? "";
        }

        $limit = isset($_GET['limit']) && $_GET['limit'] != 'all' ? (int)$_GET['limit'] : 100000;

        if (!empty($output)) {
            echo '<form action="store_selected.php" method="POST">';
            preg_match_all("/>([^ ]+) (.+?) \[(.+?)\]\n([A-Za-z\n]+)/", $output, $matches, PREG_SET_ORDER);
            echo '<div class="scroll-container">';
            
            echo '<div class="sequences-table-wrapper">';
            echo '<table>
                    <tr>
                        <th>Select</th>
                        <th>Protein Name</th>
                        <th>Accession</th>
                        <th>Taxon</th>
                        <th>Sequence</th>
                    </tr>';

            $matches = array_slice($matches, 0, $limit);

            foreach ($matches as $match) {
                $accession = trim($match[1]);
                $protein_name = trim($match[2]);
                $taxon = trim($match[3]);
                $sequence = str_replace("\n", "", $match[4]);

                $_SESSION['parsed_sequences'][$accession] = [
                    'protein_name' => $protein_name,
                    'taxon' => $taxon,
                    'sequence' => $sequence
                ];

                echo "<tr>
                        <td><input type='checkbox' name='selected_sequences[]' value='" . htmlspecialchars($accession) . "'></td>
                        <td>" . htmlspecialchars($protein_name) . "</td>
                        <td>" . htmlspecialchars($accession) . "</td>
                        <td>" . htmlspecialchars($taxon) . "</td>
                        <td><div class='sequence-container'>" . htmlspecialchars($sequence) . "</div></td>
                    </tr>";
            }

            echo '</table>';
            echo '</div>';
            echo '</div>';
            ?>
            <div style="text-align: center;">
                <p style="font-size: 15px; color: #333;">
                ← Scroll to see more of the sequence →
                </p>

                <form method="post">
                    <button type="submit" class="store-button">
                        Store Selected Sequences
                    </button>
                </form>
            </div>
            
            <?php
            echo '</form>';
        } else {
            echo "<p style='text-align:center;'>No sequences found. Please run a query first.</p>";
        }

        ?>


        <div style="text-align: center; margin-top: 10px;">
            <a href="store_selected.php">View Stored Sequences</a>
        </div>

    </div>
</body>
</html>