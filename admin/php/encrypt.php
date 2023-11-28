<?php

// fungsi untuk mendapatkan posisi (baris dan kolom) suatu karakter dalam matriks
function getPosition($mat, $c)
{
    for ($i = 0; $i < count($mat); $i++) {
        for ($j = 0; $j < count($mat[$i]); $j++) {
            if ($c == $mat[$i][$j]) {
                return ['row' => $i, 'col' => $j];
            }
        }
    }
}

// fungsi untuk menghasilkan matriks kunci Playfair
function generateMatrix($key)
{
    // inisialisasi array berukuran 128 dengan nilai 0
    $flag = array_fill(0, 128, 0); 
    // inisialisasi variabel x dan y dengan 0 utk melacak posisi matriks
    $x = 0;
    $y = 0;
    $mat = []; // array mat yg digunakan utk kunci

    // looping utk kunci
    // looping setiap karakter dalam kunci
    for ($i = 0; $i < strlen($key); $i++) {
        // jika karakter blm digunakan maka tambahkan ke matriks pada posisi x dan y
        if ($flag[ord($key[$i])] == 0) {
            $mat[$x][$y++] = $key[$i]; // menambahkan karakter ke matriks
            $flag[ord($key[$i])] = 1; // utk menandai karakter sebagai sudah digunakan
        }

        // jika posisi pd baris sudah mencapai maks (128) maka pindah pada baris selanjutnya
        if ($y == 128) {
            $x++;
            $y = 0;
        }
    }

    // looping utk karakter
    // mengisi matriks dengan karakter yang belum digunakan
    for ($ch = 0; $ch < 128; $ch++) {
        if ($flag[$ch] == 0) {
            $mat[$x][$y++] = chr($ch);
            $flag[$ch] = 1;
        }

        if ($y == 128) {
            $x++;
            $y = 0;
        }
    }

    return $mat;
}

// fungsi utk menformat pesan agar bisa digunakan utk menenkrip/dekrip
function formatMessage($msg)
{
    // looping utk setiap karakter dalam pesan
    for ($i = 0; $i < strlen($msg); $i++) {
        // jika karakter adalah j maka ganti dengan ~
        if ($msg[$i] == 'j') {
            $msg[$i] = '~';
        }
    }

    // jika ada karakter yg sama bersebelahan maka sisipkan karakter }
    for ($i = 1; $i < strlen($msg); $i += 2) {
        if ($msg[$i - 1] == $msg[$i]) {
            $msg = substr_replace($msg, '}', $i, 0);
        }
    }

    // jika pesan ganjil maka tambhakn karakter }
    if (strlen($msg) % 2 != 0) {
        $msg .= '}';
    }

    return $msg;
}

function encrypt($mat, $message)
{
    // var utk menyimpan hasil enkripsi
    $ctext = "";

    //looping tiap dua karakter krn tiap blok berisi dua karakter
    for ($i = 0; $i < strlen($message); $i += 2) {
        // utk mendapatkan posisi 2 karakter yg akan dienkripsi
        $p1 = getPosition($mat, $message[$i]);
        $p2 = getPosition($mat, $message[$i + 1]);

        // menuimpan hasil posisi (baris dan kolom) masing2 karakter
        $x1 = $p1['row'];
        $y1 = $p1['col'];
        $x2 = $p2['row'];
        $y2 = $p2['col'];

        // aturan playfair cipher
        // jika baris sama, maka ambil karakter yg ada di kanannya
        if ($x1 == $x2) {
            $ctext .= $mat[$x1][($y1 + 1) % 128];
            $ctext .= $mat[$x2][($y2 + 1) % 128];
        } 
        // jika kolom sama, maka ambil karakter yg ada di bawahnya
        elseif ($y1 == $y2) {
            $ctext .= $mat[($x1 + 1) % 128][$y1];
            $ctext .= $mat[($x2 + 1) % 128][$y2];
        } 
        // jika tdk berada pd baris dan kolom yg sama, maka ambil karakter yg membentuk persegi di antara keduannya
        else {
            $ctext .= $mat[$x1][$y2];
            $ctext .= $mat[$x2][$y1];
        }
    }

    return $ctext;
}


function decrypt($mat, $message)
{
    // var utk menyimpan hasil dekripsi
    $ptext = "";
    //looping tiap dua karakter krn tiap blok berisi dua karakter
    for ($i = 0; $i < strlen($message); $i += 2) {
        $p1 = getPosition($mat, $message[$i]);
        $p2 = getPosition($mat, $message[$i + 1]);
        $x1 = $p1['row'];
        $y1 = $p1['col'];
        $x2 = $p2['row'];
        $y2 = $p2['col'];


        //aturan playfair cipher

        // jika baris sama, maka ambil karakter yg ada di kirinya
        if ($x1 == $x2) {
            $ptext .= $mat[$x1][($y1 - 1 + 128) % 128];
            $ptext .= $mat[$x2][($y2 - 1 + 128) % 128];
        } 
        // jika kolom sama, maka ambil karakter yg ada di atasnya
        elseif ($y1 == $y2) {
            $ptext .= $mat[($x1 - 1 + 128) % 128][$y1];
            $ptext .= $mat[($x2 - 1 + 128) % 128][$y2];
        } 
        // jika tdk berada pd baris dan kolom yg sama, maka ambil karakter yg membentuk persegi di antara keduannya
        else {
            $ptext .= $mat[$x1][$y2];
            $ptext .= $mat[$x2][$y1];
        }
    }

    // menghilangkan karakter } dan ~ 
    $removeChar = ["}"];
    $result1 = str_replace($removeChar, "", $ptext);
    $result = str_replace("~",'j',$result1);

    return $result;

}

// $plaintext = 'lalalala';

// $keyMatrixArray = generateMatrix("akuma no ko");

// echo "Actual Message :<br> $plaintext\n";

// $formattedMessage = formatMessage($plaintext);
// echo "<br><br>Formatted Message : <br>$formattedMessage\n";

// $ciphertext = encrypt($keyMatrixArray, $formattedMessage);
// echo "<br><br>Encrypted Message :<br> $ciphertext\n";

// $decryptedMessage = decrypt($keyMatrixArray, $ciphertext);
// echo "<br><br>Decrypted Message :<br> $decryptedMessage\n";
?>