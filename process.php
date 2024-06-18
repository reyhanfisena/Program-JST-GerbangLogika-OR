<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JST OR Implementation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
session_start();

class JST {
    public $w1;
    public $w2;
    public $w3;
    public $miu;

    public function __construct($learningRate) {
        $this->w1 = round((float)rand() / (float)getrandmax(), 3);
        $this->w2 = round((float)rand() / (float)getrandmax(), 3);
        $this->w3 = round((float)rand() / (float)getrandmax(), 3);
        $this->miu = $learningRate;
    }

    public function fungsiAktivasi($x) {
        return ($x >= 0) ? 1 : 0;
    }

    public function hitungOutput($x1, $x2) {
        $aktivasi = ($this->w1 * 1) + ($this->w2 * $x1) + ($this->w3 * $x2);
        return $this->fungsiAktivasi($aktivasi);
    }

    public function latih($x1, $x2, $target) {
        $output = $this->hitungOutput($x1, $x2);
        $error = $target - $output;

        if ($error != 0) {
            $this->w1 += $this->miu * $error * 1;
            $this->w2 += $this->miu * $error * $x1;
            $this->w3 += $this->miu * $error * $x2;
        }

        return array(
            'x1' => $x1,
            'x2' => $x2,
            'w1' => $this->w1,
            'w2' => $this->w2,
            'w3' => $this->w3,
            'output' => $output,
            'error' => $error
        );
    }
}

$input = array(
    array(0, 0), array(0, 1), array(1, 0), array(1, 1)
);
$targetOutput = array(0, 1, 1, 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["learningRate"])) {
        $learningRate = $_POST["learningRate"];
        $_SESSION['jst'] = serialize(new JST($learningRate));
        $_SESSION['iterations'] = 0;
        $_SESSION['totalError'] = 0;
        $_SESSION['results'] = array();
        $_SESSION['continue'] = true;
    }

    if (!isset($_SESSION['continue']) || !$_SESSION['continue']) {
        session_destroy();
        echo "<p>Pembelajaran dihentikan oleh pengguna.</p>";
        exit;
    }

    $OR = unserialize($_SESSION['jst']);
    $iterations = $_SESSION['iterations'];
    $totalError = $_SESSION['totalError'];
    $result = $_SESSION['results'];

    while ($totalError < 4) {
        foreach ($input as $index => $data) {
            $x1 = $data[0];
            $x2 = $data[1];
            $target = $targetOutput[$index];

            $trainingResult = $OR->latih($x1, $x2, $target);
            array_push($result, $trainingResult);

            if ($trainingResult['error'] == 0) {
                $totalError++;
            } else {
                $totalError = 0;
            }
        }
        $iterations++;
        break;
    }

    $_SESSION['jst'] = serialize($OR);
    $_SESSION['iterations'] = $iterations;
    $_SESSION['totalError'] = $totalError;
    $_SESSION['results'] = $result;

    echo "<h2>Hasil Pembelajaran</h2>";
    echo "<table border='1'>";
    echo "<tr><th>NO</th><th>I1</th><th>I2</th><th>W1</th><th>W2</th><th>W3</th><th>Output</th><th>Error</th></tr>";

    foreach ($result as $index => $data) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td>" . $data['x1'] . "</td>";
        echo "<td>" . $data['x2'] . "</td>";
        echo "<td>" . $data['w1'] . "</td>";
        echo "<td>" . $data['w2'] . "</td>";
        echo "<td>" . $data['w3'] . "</td>";
        echo "<td>" . $data['output'] . "</td>";
        echo "<td>" . $data['error'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    if ($totalError >= 4) {
        echo "<p>Pembelajaran selesai pada iterasi ke-" . $iterations . ".</p>";
        echo "<p>w1 = " . $OR->w1 . ", w2 = " . $OR->w2 . ", w3 = " . $OR->w3 . ".</p>";
        session_destroy();
    } else {
        echo "<form action='process.php' method='POST'>";
        echo "<p>Lanjutkan iterasi? (0=Berhenti 1=Lanjutkan):</p>";
        echo "<input type='number' name='option' min='0' max='1' required>";
        echo "<button type='submit'>Kirim</button>";
        echo "</form>";

        if (isset($_POST['option']) && $_POST['option'] == 0) {
            $_SESSION['continue'] = false;
        }
    }
}
?>
</body>
</html>
