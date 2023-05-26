<?php
// Pobranie danych przesłanych przez żądanie AJAX
$studentId = $_POST['studentId'];
$gradeValue = $_POST['gradeValue'];

// Połączenie z bazą danych i dodanie oceny
$host = 'localhost';
$dbname = 'Grade_book';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Wstawienie oceny do tabeli grades
    $query = "INSERT INTO grades (student_id, value_id) VALUES (:studentId, :gradeValue)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':studentId', $studentId, PDO::PARAM_INT);
    $stmt->bindParam(':gradeValue', $gradeValue, PDO::PARAM_INT);
    $stmt->execute();

    // Zwrócenie odpowiedzi do żądania AJAX
    echo "success";
} catch (PDOException $e) {
    // Obsługa błędów połączenia z bazą danych
    echo "error";
}
?>
