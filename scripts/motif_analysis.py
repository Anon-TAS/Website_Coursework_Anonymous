import os
import subprocess

# file paths
FASTA_FILE = "/home/s2694679/public_html/Website/user_sequences.fasta"
MOTIFS_FILE = "/home/s2694679/public_html/Website/motifs.txt"

def run_patmatmotifs():
    # remove old motifs to make sure results are for the most recent search
    if os.path.exists(MOTIFS_FILE):
        os.remove(MOTIFS_FILE)

    # read the fasta file and extract the individual sequences
    # This is based on what is returned from the extraction so formating based on that layout. Want to extract the ID and sequence of each result.
    sequences = []
    with open(FASTA_FILE, "r") as f:
        current_seq = []
        for line in f:
            if line.startswith(">"):
                if current_seq:
                    sequences.append(current_seq)
                current_seq = [line.strip()]  # Start a new sequence
            else:
                current_seq.append(line.strip())  # Append sequence data
        if current_seq:
            sequences.append(current_seq)  # Add last sequence

    # Process each sequence **individually**
    for i, seq_data in enumerate(sequences):
        temp_fasta = f"/home/s2694679/public_html/Website/temp_seq_{i}.fasta"
        temp_motif_file = f"/home/s2694679/public_html/Website/temp_motifs_{i}.txt"

        #Write sequence to a temporary FASTA file
        with open(temp_fasta, "w") as temp_f:
            temp_f.write("\n".join(seq_data))

        #Run `patmatmotifs` on this single sequence, using a separate output file
        command = f"patmatmotifs -sequence {temp_fasta} -outfile {temp_motif_file} -full Y -prune N"
        print(f"🔍 Running motif search on: {seq_data[0]}")
        subprocess.run(command, shell=True, check=True)

        #Append this sequence's results to the main `motifs.txt` file
        with open(temp_motif_file, "r") as temp_mf:
            motifs_content = temp_mf.read()
        with open(MOTIFS_FILE, "a") as main_mf:
            main_mf.write(motifs_content + "\n")  # Ensure separation between sequences

        #Remove temp files after processing
        os.remove(temp_fasta)
        os.remove(temp_motif_file)

    print("Motif analysis completed for all sequences.")

# Run the function
run_patmatmotifs()
