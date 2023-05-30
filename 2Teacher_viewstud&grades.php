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

        <?php
        // Pobranie uczniów z tabeli students_classes
        $students = getStudents();

        

        if (!empty($students)) {
            // Sortowanie uczniów alfabetycznie według nazwisk
            usort($students, function ($a, $b) {
                return strcmp($a['lastName'], $b['lastName']);
            });

            echo "<h3>Lista uczniów:</h3>";
            echo "<table>";
            echo "<tr><th>Uczeń</th><th>Oceny</th><th>Akcje</th></tr>";

            // Wyświetlanie listy uczniów w tabeli
            foreach ($students as $student) {
                echo "<tr>";
                echo "<td>$student[lastName] $student[firstName]</td>";
                echo "<td>" . getGrades($student['id']) . "</td>";
                echo "<td><button onclick=\"addGrade($student[id])\">Dodaj ocenę</button></td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<p>Brak uczniów.</p>";
        }

        // Funkcja pomocnicza do pobierania uczniów z tabeli students_classes
        function getStudents() {
            // Połączenie z bazą danych (proszę dostosować do własnych ustawień)
            $host = 'localhost';
            $dbname = 'Grade_book';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            try {
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT u.id, u.firstName, u.lastName
                          FROM students_classes AS sc
                          INNER JOIN user AS u ON u.id = sc.student_id";
                $stmt = $pdo->prepare($query);
                $stmt->execute();

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Błąd połączenia: " . $e->getMessage());
            }
        }

        // Funkcja pomocnicza do pobierania ocen ucznia
        function getGrades($studentId) {
            // Połączenie z bazą danych (proszę dostosować do własnych ustawień)
            $host = 'localhost';
            $dbname = 'Grade_book';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            try {
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT g.value_id
                          FROM grades AS g
                          INNER JOIN students_classes AS sc ON sc.id = g.student_id
                          WHERE sc.student_id = :studentId";
                $stmt = $pdo->prepare($query);
                $stmt->bindValue(':studentId', $studentId, PDO::PARAM_INT);
                $stmt->execute();

                // Tworzenie listy ocen
                $gradeList = [];
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $gradeList[] = $row['value_id'];
                }

                return implode(", ", $gradeList);
            } catch (PDOException $e) {
                die("Błąd połączenia: " . $e->getMessage());
            }
        }
        ?>

        <script>
            function addGrade(studentId) {
                // Tutaj można dodać kod obsługujący dodawanie oceny dla danego ucznia
                alert("Dodawanie oceny dla ucznia o ID: " + studentId);
            }
        </script>
    </div>
</body>
</html>
