<?php

require_once 'db.php';

if (isset($_POST['submit'])) {
    $countFiles = count($_FILES['file']['name']); //Количество загружаемых файлов

    if ($countFiles > 3) {
        echo 'Максимально можно отправить 3 файла';
        goto afterPost;
    } else if (count($_FILES['file']['name']) == 1 && $_FILES['file']['name'][0] == '') {
        echo 'Файлы не выбраны';
        goto afterPost;
    } else {
            for ($i = 0; $i < $countFiles; $i++) {
                $fileName = $_FILES['file']['name'][$i];
                $fileTmpName = $_FILES['file']['tmp_name'][$i];
                $fileType = $_FILES['file']['type'][$i];
                $fileError = $_FILES['file']['error'][$i];
                $fileSize = $_FILES['file']['size'][$i];

                $fileExtension = strtolower(end(explode('.', $fileName)));
                $fileName = pathinfo($fileName)['filename']; //Имя файла без расширения
                $fileName = preg_replace('/[0-9]/', '', $fileName);
                $allowedExtensions = ['jpg', 'jpeg', 'png'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    if ($fileSize < (5 * 1024 * 1024)) {
                        if ($fileError === 0) {
                            $connection->query("INSERT INTO `images` (`imgname`, `extension`)
                VALUES ('$fileName', '$fileExtension')");
                            $lastID = $connection->query("SELECT MAX(id) FROM `images`");
                            $lastID = $lastID->fetchAll();
                            $lastID = $lastID[0][0];
                            $fileNameNew = $lastID . $fileName . '.' . $fileExtension;
                            $fileDestination = 'uploads/' . $fileNameNew;
                            move_uploaded_file($fileTmpName, $fileDestination);
                            echo 'Файл ' . ($i+1) . ' - <strong>успех</strong><br>';
                        } else {
                            echo 'Файл ' . ($i+1) . ' - <strong>что-то пошло не так</strong><br>';
                        }
                    } else {
                        echo 'Файл ' . ($i+1) . ' - <strong>слишком большой размер файла</strong><br>';
                    }
                } else {
                    echo 'Файл ' . ($i+1) . ' - <strong>неверный тип файла</strong><br>';
                }

            }

    }

}

afterPost:

$data = $connection->query('SELECT * FROM `images`');
echo '<div style="display:flex; align-items: flex-end; flex-wrap: wrap">';
foreach ($data as $img) {

    $delete = 'delete' . $img['id'];
    $image = "uploads/" . $img['id'] . $img['imgname'] . '.' . $img['extension'];
    if (isset($_POST[$delete])) {
        $imageID = $img['id'];
        $connection->query("DELETE FROM `images` WHERE id = '$imageID'");
        if (file_exists($image)) {
            unlink($image);
        }
    }


    if (file_exists($image)) {
        echo "<div>";
        echo "<img width='150' height='150' src=$image>";
        echo '<form method="POST"><button name="delete' . $img['id'] . '" style="display: block; margin: auto">Удалить</button></form></div>';
    }

}
echo '</div>';

?>

<style>
    body {
        margin: 50px 100px;
        font-size: 25px;
    }
    input, button {
        outline: none;
        font-size: 25px;
    }
</style>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, user-scalable=no initial-scale=1.0 maximum-scale=1.0 minimum-scale=1.0">
    <title>Document</title>

<body>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file[]" accept=".jpg,.jpeg,.png" required multiple>
    <button name="submit">Отправить</button>
</form>

</body>
</html>