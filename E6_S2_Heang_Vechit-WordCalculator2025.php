<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Riel to Words (Khmer & English) + USD Conversion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 650px;
            margin-top: 60px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            color: #343a40;
        }

        .form-label {
            font-weight: 500;
        }

        .btn {
            min-width: 120px;
        }

        .result {
            margin-top: 15px;
            padding: 15px 20px;
            border-radius: 8px;
            background-color: #f1f3f5;
            border-left: 5px solid #0d6efd;
            font-size: 1.05rem;
        }

        .result.text-danger {
            border-left-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-primary mb-4" style="font-size: 1.75rem; letter-spacing: 0.5px;">
            Convert Riel to Words <br>(Khmer & English  & USD Value)
        </h2>
        <form method="POST" id="rielForm">
            <div class="mb-3">
                <label class="form-label">Enter Amount in Riel (KHR)</label>
                <input type="text" class="form-control" name="riel" placeholder="Enter amount in Riel" value="<?php echo htmlspecialchars($_POST['riel'] ?? ''); ?>" required>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Convert</button>
                <button type="button" class="btn btn-outline-secondary" id="clearBtn">Clear</button>
            </div>
        </form>

        <div id="resultArea" class="mt-4">
            <?php
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $riel = $_POST['riel'] ?? '';
                $isNegative = false;

                if (substr($riel, 0, 1) == '-') {
                    $isNegative = true;
                    $riel = ltrim($riel, '-');
                }

                if (!is_numeric($riel) || $riel < 0) {
                    echo "<div class='result text-danger'>⚠️ Error: Please enter a valid positive number.</div>";
                } else {
                    $usd = $riel / 4000;
                    $usdFormatted = number_format($usd, 2);

                    $rielWordsEN = convertNumberToWordsEN($riel) . " Riels";
                    $rielWordsKH = convertNumberToWordsKH($riel) . " រៀល";

                    if ($isNegative) {
                        $rielWordsEN = "Negative " . $rielWordsEN;
                        $rielWordsKH = "ដក " . $rielWordsKH;
                        $usdFormatted = "-" . $usdFormatted;
                        $resultText = "Riel: -$riel | English: $rielWordsEN | Khmer: $rielWordsKH | USD: $$usdFormatted\n";
                    } else {
                        $resultText = "Riel: $riel | English: $rielWordsEN | Khmer: $rielWordsKH | USD: $$usdFormatted\n";
                    }

                    echo "<div class='result'><strong>🔤 In English:</strong> $rielWordsEN</div>
                          <div class='result'><strong>🗣 In Khmer:</strong> $rielWordsKH</div>
                          <div class='result'><strong>💵 USD Equivalent:</strong> $$usdFormatted</div>";

                    file_put_contents("conversion_results.txt", $resultText, FILE_APPEND);
                }
            }

            function convertNumberToWordsEN($num)
            {
                $ones = ["", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine"];
                $teens = ["", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"];
                $tens = ["", "ten", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"];
                $thousands = ["", "thousand", "million", "billion", "trillion"];

                if ($num == 0) return "zero";
                $numStr = strval($num);
                $numStr = str_pad($numStr, ceil(strlen($numStr) / 3) * 3, "0", STR_PAD_LEFT);
                $chunks = str_split($numStr, 3);
                $output = [];

                foreach ($chunks as $i => $chunk) {
                    $hundred = isset($chunk[0]) && $chunk[0] != "0" ? $ones[$chunk[0]] . " hundred " : "";
                    $ten = isset($chunk[1]) && $chunk[1] == "1" && isset($chunk[2]) && $chunk[2] != "0" ? $teens[$chunk[2]] : (isset($chunk[1]) && $chunk[1] != "0" ? $tens[$chunk[1]] : "");
                    $one = isset($chunk[2]) && $chunk[1] != "1" ? $ones[$chunk[2]] : "";

                    $group = trim("$hundred$ten $one");
                    if ($group) {
                        $output[] = $group . " " . ($thousands[count($chunks) - $i - 1] ?? "");
                    }
                }
                return trim(implode(" ", $output));
            }

            function convertNumberToWordsKH($num)
            {
                $khmerNumbers = [
                    0 => "សូន្យ",
                    1 => "មួយ",
                    2 => "ពីរ",
                    3 => "បី",
                    4 => "បួន",
                    5 => "ប្រាំ",
                    6 => "ប្រាំមួយ",
                    7 => "ប្រាំពីរ",
                    8 => "ប្រាំបី",
                    9 => "ប្រាំបួន",
                    10 => "ដប់",
                    20 => "ម្ភៃ",
                    30 => "សាមសិប",
                    40 => "សែសិប",
                    50 => "ហាសិប",
                    60 => "ហុកសិប",
                    70 => "ចិតសិប",
                    80 => "ប៉ែតសិប",
                    90 => "កៅសិប"
                ];
                $levels = ["", "ពាន់", "លាន", "ប៊ីលាន", "ទ្រីលាន"];

                if ($num == 0) return "សូន្យ";
                $numStr = strval($num);
                $numStr = str_pad($numStr, ceil(strlen($numStr) / 3) * 3, "0", STR_PAD_LEFT);
                $chunks = str_split($numStr, 3);
                $output = [];

                foreach ($chunks as $i => $chunk) {
                    $hundred = isset($chunk[0]) && $chunk[0] != "0" ? $khmerNumbers[$chunk[0]] . "រយ " : "";
                    $ten = isset($chunk[1]) && $chunk[1] == "1" && isset($chunk[2]) && $chunk[2] != "0" ? "ដប់" . $khmerNumbers[$chunk[2]] : (isset($chunk[1]) && $chunk[1] != "0" ? $khmerNumbers[$chunk[1] . "0"] : "");
                    $one = isset($chunk[2]) && $chunk[1] != "1" ? ($chunk[2] != "0" ? $khmerNumbers[$chunk[2]] : "") : "";

                    $group = trim("$hundred$ten $one");
                    if ($group) {
                        $output[] = $group . " " . ($levels[count($chunks) - $i - 1] ?? "");
                    }
                }
                return trim(implode(" ", $output));
            }
            ?>
        </div>

        <script>
            document.getElementById('clearBtn').addEventListener('click', function() {
                document.querySelector('[name="riel"]').value = '';
                document.getElementById('resultArea').innerHTML = '';
            });
        </script>
    </div>
</body>

</html>