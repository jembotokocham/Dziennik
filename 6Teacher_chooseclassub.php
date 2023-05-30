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

        <!-- WYBÓR NAUCZYCIELA -->
        <?php
        // Pobranie danych nauczyciela z tabeli teachers_classes
        $host = 'localhost';
        $dbname = 'Grade_book';
        $username = 'root';
        $password = '';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Pobranie listy nauczycieli z tabeli teachers_classes
            $query = "SELECT DISTINCT teacher_id FROM teachers_classes";
            $stmt = $pdo->query($query);
            $teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Obsługa błędów połączenia z bazą danych
            echo "Błąd połączenia z bazą danych: " . $e->getMessage();
            exit;
        }

        // Sprawdzenie, czy został wybrany nauczyciel
        if (isset($_POST['teacher_id'])) {
            $teacherId = $_POST['teacher_id'];

            try {
                // Pobranie danych nauczyciela z tabeli user
                $query = "SELECT firstName, lastName FROM user WHERE id = :teacherId";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
                $stmt->execute();
                $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

                // Pobranie listy klas danego nauczyciela z tabeli teachers_classes
                $query = "SELECT classes.id, classes.name FROM classes
                          INNER JOIN teachers_classes ON classes.id = teachers_classes.class_id
                          WHERE teachers_classes.teacher_id = :teacherId
                          ORDER BY classes.name";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
                $stmt->execute();
                $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Pobranie listy przedmiotów danego nauczyciela z tabeli teachers_classes
                $query = "SELECT subjects.id, subjects.name FROM subjects
                          INNER JOIN teachers_classes ON subjects.id = teachers_classes.subject_id
                          WHERE teachers_classes.teacher_id = :teacherId
                          ORDER BY subjects.name";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
                $stmt->execute();
                $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Obsługa błędów połączenia z bazą danych
                echo "Błąd połączenia z bazą danych: " . $e->getMessage();
                exit;
            }

            // Wyświetlanie listy klas i przedmiotów danego nauczyciela do wyboru
            echo "<h3>Wybrany nauczyciel: {$teacher['firstName']} {$teacher['lastName']}</h3>";
            echo "<h4>Wybierz klasę:</h4>";
            echo "<form method='post'>";
            echo "<input type='hidden' name='teacher_id' value='{$teacherId}'>";
            echo "<select name='class_id'>";
            echo "<option value='' selected>--wybierz klasę--</option>";
            foreach ($classes as $class) {
                echo "<option value='{$class['id']}'>{$class['name']}</option>";
            }
            echo "</select>";

            echo "<h4>Wybierz przedmiot:</h4>";
            echo "<select name='subject_id'>";
            echo "<option value='' selected>--wybierz przedmiot--</option>";
            foreach ($subjects as $subject) {
                echo "<option value='{$subject['id']}'>{$subject['name']}</option>";
            }
            echo "</select>";

            echo "<input type='submit' value='Wyświetl uczniów'>";
            echo "</form>";
        } else {
            // Wyświetlanie formularza do wyboru nauczyciela
            echo "<h4>Wybierz nauczyciela:</h4>";
            echo "<form method='post'>";
            echo "<select name='teacher_id'>";
            echo "<option value='' selected>--wybierz nauczyciela--</option>";
            foreach ($teachers as $teacher) {
                $teacherData = getUserData($teacher['teacher_id']);
                echo "<option value='{$teacher['teacher_id']}'>{$teacherData['firstName']} {$teacherData['lastName']}</option>";
            }
            echo "</select>";
            echo "<input type='submit' value='Wybierz'>";
            echo "</form>";
        }
        ?>

        <!-- WYŚWIETLANIE LISTY UCZNIÓW DANEJ KLASY Z WYBRANYM PRZEDMIOTEM -->
        <?php
        // Sprawdzenie, czy została wybrana klasa i przedmiot
        if (isset($_POST['class_id']) && isset($_POST['subject_id'])) {
            $classId = $_POST['class_id'];
            $subjectId = $_POST['subject_id'];

            try {
                // Pobranie nazwy klasy
                $query = "SELECT name FROM classes WHERE id = :classId";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
                $stmt->execute();
                $className = $stmt->fetchColumn();

                // Pobranie nazwy przedmiotu
                $query = "SELECT name FROM subjects WHERE id = :subjectId";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':subjectId', $subjectId, PDO::PARAM_INT);
                $stmt->execute();
                $subjectName = $stmt->fetchColumn();

                // Pobranie uczniów danej klasy z wybranym przedmiotem
                $query = "SELECT user.firstName, user.lastName, user.id as studentId FROM user
                          INNER JOIN students_classes ON user.id = students_classes.student_id
                          INNER JOIN teachers_classes ON students_classes.class_id = teachers_classes.class_id
                          WHERE students_classes.class_id = :classId AND teachers_classes.subject_id = :subjectId";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
                $stmt->bindParam(':subjectId', $subjectId, PDO::PARAM_INT);
                $stmt->execute();
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Wyświetlanie listy uczniów
                echo "<h3>Uczniowie klasy {$className} z przedmiotu {$subjectName}:</h3>";
                if (count($students) > 0) {
                    echo "<table>";
                    echo "<tr><th>Uczeń</th><th>Oceny</th><th>Dodawanie ocen</th></tr>";
                    foreach ($students as $student) {
                        echo "<tr>";
                        echo "<td>{$student['lastName']} {$student['firstName']}</td>";
                        echo "<td>" . getGrades($student['studentId']) . "</td>";
                        echo "<td><button>Dodaj ocenę</button></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "Brak uczniów w tej klasie.";
                }
            } catch (PDOException $e) {
                // Obsługa błędów połączenia z bazą danych
                echo "Błąd połączenia z bazą danych: " . $e->getMessage();
                exit;
            }
        }
        ?>
        <?php
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

        function getUserData($userId)
        {
            $host = 'localhost';
            $dbname = 'Grade_book';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            try {
                $pdo = new PDO($dsn, $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT firstName, lastName FROM user WHERE id = :userId";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();

                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Błąd połączenia: " . $e->getMessage());
            }
        }
        ?>
    </div>
</body>
</html>
