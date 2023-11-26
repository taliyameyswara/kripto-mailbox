<?php

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

function generateMatrix($key)
{
    $flag = array_fill(0, 128, 0);
    $x = 0;
    $y = 0;
    $mat = [];

    for ($i = 0; $i < strlen($key); $i++) {
        if ($flag[ord($key[$i])] == 0) {
            $mat[$x][$y++] = $key[$i];
            $flag[ord($key[$i])] = 1;
        }

        if ($y == 128) {
            $x++;
            $y = 0;
        }
    }

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

function formatMessage($msg)
{
    for ($i = 0; $i < strlen($msg); $i++) {
        if ($msg[$i] == 'j') {
            $msg[$i] = '~';
        }
    }

    for ($i = 1; $i < strlen($msg); $i += 2) {
        if ($msg[$i - 1] == $msg[$i]) {
            $msg = substr_replace($msg, '}', $i, 0);
        }
    }

    if (strlen($msg) % 2 != 0) {
        $msg .= '}';
    }

    return $msg;
}

function encrypt($mat, $message)
{
    $ctext = "";
    for ($i = 0; $i < strlen($message); $i += 2) {
        $p1 = getPosition($mat, $message[$i]);
        $p2 = getPosition($mat, $message[$i + 1]);
        $x1 = $p1['row'];
        $y1 = $p1['col'];
        $x2 = $p2['row'];
        $y2 = $p2['col'];

        if ($x1 == $x2) {
            $ctext .= $mat[$x1][($y1 + 1) % 128];
            $ctext .= $mat[$x2][($y2 + 1) % 128];
        } elseif ($y1 == $y2) {
            $ctext .= $mat[($x1 + 1) % 128][$y1];
            $ctext .= $mat[($x2 + 1) % 128][$y2];
        } else {
            $ctext .= $mat[$x1][$y2];
            $ctext .= $mat[$x2][$y1];
        }
    }

    return $ctext;
}


function decrypt($mat, $message)
{
    $ptext = "";
    for ($i = 0; $i < strlen($message); $i += 2) {
        $p1 = getPosition($mat, $message[$i]);
        $p2 = getPosition($mat, $message[$i + 1]);
        $x1 = $p1['row'];
        $y1 = $p1['col'];
        $x2 = $p2['row'];
        $y2 = $p2['col'];

        if ($x1 == $x2) {
            $ptext .= $mat[$x1][($y1 - 1 + 128) % 128];
            $ptext .= $mat[$x2][($y2 - 1 + 128) % 128];
        } elseif ($y1 == $y2) {
            $ptext .= $mat[($x1 - 1 + 128) % 128][$y1];
            $ptext .= $mat[($x2 - 1 + 128) % 128][$y2];
        } else {
            $ptext .= $mat[$x1][$y2];
            $ptext .= $mat[$x2][$y1];
        }
    }

    $removeChar = ["}"];

    // Remove multiple characters
        $result1 = str_replace($removeChar, "", $ptext);
        $result = str_replace("~",'j',$result1);
    
        return $result;

    // return $ptext;
}

// $plaintext = 'haohaoZHANGhao_250700';

// $keyMatrixArray = generateMatrix("akuma no ko");

// echo "Actual Message :<br> $plaintext\n";

// $formattedMessage = formatMessage($plaintext);
// echo "<br><br>Formatted Message : <br>$formattedMessage\n";

// $ciphertext = encrypt($keyMatrixArray, $formattedMessage);
// echo "<br><br>Encrypted Message :<br> $ciphertext\n";

// $decryptedMessage = decrypt($keyMatrixArray, $ciphertext);
// echo "<br><br>Decrypted Message :<br> $decryptedMessage\n";
?>