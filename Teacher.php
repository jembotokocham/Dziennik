<!DOCTYPE html>
<html>
<head>
    <title>System ocen</title>
    <style>
        .container {
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>System ocen</h2>

        <form method="POST" action="">
            <label for="class">Wybierz klasę:</label>
            <select name="class" id="class">
                <option value="">-- Wybierz --</option>
                <option value="1A">1A</option>
                <option value="1B">1B</option>
                <option value="2A">2A</option>
                <option value="2B">2B</option>
            </select>

            <button type="submit">Wybierz</button>
        </form>

        <?php
        // Sprawdzenie, czy formularz został wysłany
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sprawdzenie, czy została wybrana klasa
            if (!empty($_POST["class"])) {
                $selectedClass = $_POST["class"];

                // Pobranie listy uczniów z odpowiednimi ocenami (możesz dostosować te dane do własnych potrzeb)
                $students = getStudentsByClass($selectedClass);

                if (!empty($students)) {
                    echo "<h3>Lista uczniów klasy $selectedClass:</h3>";
                    echo "<table>";
                    echo "<tr><th>Uczeń</th><th>Oceny</th><th>Dodaj ocenę</th></tr>";

                    // Wyświetlanie listy uczniów z ocenami w tabeli
                    foreach ($students as $student) {
                        echo "<tr>";
                        echo "<td>$student[name]</td>";
                        echo "<td>";

                        if (!empty($student["grades"])) {
                            foreach ($student["grades"] as $grade) {
                                echo "$grade, ";
                            }
                        } else {
                            echo "Brak ocen";
                        }

                        echo "</td>";
                        echo "<td><button onclick=\"location.href='dodaj_ocene.php?student=$student[name]';\">Dodaj ocenę</button></td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<p>Brak uczniów w wybranej klasie.</p>";
                }
            } else {
                echo "<p>Proszę wybrać klasę.</p>";
            }
        }

        // Funkcja pomocnicza do pobrania uczniów z ocenami (możesz dostosować tę funkcję do własnych potrzeb)
        function getStudentsByClass($class) {
          // Przykładowe dane uczniów w klasie (możesz dostosować te dane do własnych potrzeb)
$students = [];

if ($class == "1A") {
  $students[] = [
      "name" => "Jan Kowalski",
      "grades" => [4, 5, 3]
  ];
  $students[] = [
      "name" => "Anna Nowak",
      "grades" => [5, 4, 4]
  ];
} elseif ($class == "1B") {
  $students[] = [
      "name" => "Piotr Zięba",
      "grades" => [3, 3, 4]
  ];
  $students[] = [
      "name" => "Marta Kowalczyk",
      "grades" => [5, 4, 5]
  ];
} elseif ($class == "2A") {
  $students[] = [
      "name" => "Adam Nowicki",
      "grades" => [4, 4, 4]
  ];
  $students[] = [
      "name" => "Magda Szymańska",
      "grades" => [5, 5, 5]
  ];
} elseif ($class == "2B") {
  $students[] = [
      "name" => "Michał Wójcik",
      "grades" => [3, 3, 3]
  ];
  $students[] = [
      "name" => "Karolina Kaczmarek",
      "grades" => [4, 4, 5]
  ];
}

return $students;
}
?>
</div>
</body>
</html>
