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
            'aktivasi' => $this->hitungOutput($x1, $x2),
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
    $learningRate = $_POST["learningRate"];
    $OR = new JST($learningRate);

    $totalError = 0;
    $no = 1;
    $iterations = 0;
    $result = array();

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
    }

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

    echo "<p>Pembelajaran selesai pada iterasi ke-" . $iterations . ".</p>";
    echo "<p>w1 = " . $OR->w1 . ", w2 = " . $OR->w2 . ", w3 = " . $OR->w3 . ".</p>";
}
?>
</body>
</html>