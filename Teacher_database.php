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

        <!-- WYBÓR KLASY START -->
        <?php
        // Pobranie danych klas z bazy danych
        $host = 'localhost';
        $dbname = 'Grade_book';
        $username = 'root';
        $password = '';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Pobranie listy klas z tabeli classes
            $query = "SELECT * FROM classes ORDER BY name";
            $stmt = $pdo->query($query);
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Obsługa błędów połączenia z bazą danych
            echo "Błąd połączenia z bazą danych: " . $e->getMessage();
            exit;
        }

        // Sprawdzenie, czy została wybrana klasa
        if (isset($_POST['class_id'])) {
            $classId = $_POST['class_id'];

            try {
                // Pobranie uczniów danej klasy
                $query = "SELECT user.firstName, user.lastName, user.id as studentId FROM user
                          INNER JOIN students_classes ON user.id = students_classes.student_id
                          WHERE students_classes.class_id = :classId
                          ORDER BY user.lastName, user.firstName";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
                $stmt->execute();
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Obsługa błędów połączenia z bazą danych
                echo "Błąd połączenia z bazą danych: " . $e->getMessage();
                exit;
            }
 // Formularz do wyboru klasy
 echo "<form method='post'>";
 echo "<label for='class-select'>Wybierz klasę:</label>";
 echo "<select name='class_id' id='class-select'>";
 foreach ($classes as $class) {
     echo "<option value='{$class['id']}'>{$class['name']}</option>";
 }
 echo "</select>";
 echo "<input type='submit' value='Wyświetl uczniów'>";
 echo "</form>";
            // Wyświetlanie listy uczniów danej klasy w tabeli
            echo "<h2>Lista uczniów klasy</h2>";
            if (!empty($students)) {
                echo "<table>";
                echo "<tr><th>Imię</th><th>Nazwisko</th><th>Oceny</th></tr>";
                foreach ($students as $student) {
                    echo "<tr>";
                    echo "<td>{$student['firstName']}</td>";
                    echo "<td>{$student['lastName']}</td>";
                    echo "<td>" . getGrades($student['studentId']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Brak uczniów w wybranej klasie.</p>";
            }
        }

       
        ?>
        <!-- WYBÓR KLASY KONIEC -->

        <?php
        // Funkcja pomocnicza do pobierania uczniów z tabeli students_classes
        function getStudents()
        {
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
        function getGrades($studentId)
        {
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

    </div>
</body>
</html>
