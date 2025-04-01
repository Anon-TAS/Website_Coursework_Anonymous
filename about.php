<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About This Website</title>
    <link rel="stylesheet" href="/~s2694679/Website/assets/style.css">
</head>
<body>
    <div class="page-wrapper">
        <div class="container">

            <div class="back-link">
                <a href="index.php">Go to Homepage</a>
            </div>

            <h1>About This Website</h1>

            <div class="main-results">
                <p>This web application allows users to analyse protein sequences and discover biologically significant motifs.</p>

                <h2>&#128269; What It Does</h2>
                <ul>
                    <li>Search protein sequences in FASTA format</li>
                    <li>Conduct conservation and alignment analysis across species</li>
                    <li>Automatically identify recurring motifs</li>
                    <li>View frequency charts and positional distribution of motifs</li>
                </ul>

                <h2>&#128202; Database and Schema Design</h2>
                <p>This website used a relational database (MySQL) to store the user submitted sequences, motif analusis results and session data.</p>
                <ul>
                    <li>Session isolation – each user has a unique session_id that ties their data together and means many users can use site at the same time</li>
                    <li>Sequences SQL Table - stores protein name, accession, taxon, query_id and session id.</li>
                    <li>Motif SQL Table - stores accession, motif name, start and end position and session id</li>
                </ul>

                <h2>&#129514; Demo Dataset</h2>
                <p>You can explore using an example dataset of <strong>glucose-6-phosphatase proteins</strong> from Aves to 'try before you buy'!</p>

                <h2>&#128101; Who It's For</h2>
                <p>This tool is aimed at bioinformatics/data science students, researchers, and educators who are looking to understand motif patterns and conservation across protein sequences.</p>

                <h2>&#128736; Technologies & Tools Used</h2>
                <ul>
                    <li>PHP & MySQL (for dynamic content and data storage)</li>
                    <li>Python scripts (for fetching sequences, concervation analysis, backend motif analysis and plotting)</li>
                    <li>AJAX to fetch and display sequences dynamically without reloading the page</li>
                    <li>Chart.js for cool interactive visualisations</li>
                    <li>NCBI Entrez API – Used to dynamically fetch protein sequences based on user-defined queries (protein family, taxonomic group, and limit)</li>
                    <li>EMBOSS patmatmotifs – Command-line tool used for identifying known protein motifs against PROSITE motif database</li>
                    <li>Biopython - Tool used for For FASTA parsing (SeqIO) and alignment processing (AlignIO)</li>
                    <li>Various responsive design via CSS stylesheet</li>
                </ul>

                <h2>&#127891;Acknowledgements</h2>
                <p>This website was developed as part of the <strong>Introduction to Website and Database Design</strong> coursework. Special thanks to the teaching staff for providing the lecture content, guidance, and support throughout the semester.</p>
            </div>

        </div>
    </div>
</body>
</html>
