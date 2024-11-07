<?php
    $body = file_get_contents('php://input');
    $request = json_decode($body, true);

    $pdo = new PDO('mysql:host=localhost;dbname=siswa', 'root', ''); // Ganti username dan password sesuai kebutuhan
    $query = $pdo->prepare("INSERT INTO students(nik, nama) VALUES(:nik, :name)");
    $query->bindValue(':name', $request['name'], PDO::PARAM_STR);
    $query->bindValue(':nik', $request['nik'], PDO::PARAM_STR);

    try {
        $result = $query->execute();
        $id = $pdo->lastInsertId();
        
        if ($result) {
            $query = $pdo->prepare("select * from students where id = :id");
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $students = $query->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                "status" => true,
                "student" => $students[0]
            ]);
        }
    } catch(Exception $e) {
        echo json_encode([
            "status" => false,
            "error" => $e->getMessage()
        ]);
    }
    // header('Location: http://sunupf.test/success.php');
?>