<?php
echo '<!DOCTYPE html><html><head><title>Обработка файла</title><meta charset="UTF-8"></head><body>';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload_file'])) {
    $file = $_FILES['upload_file'];

    // Проверка ошибок загрузки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("Ошибка загрузки файла.");
    }

    // Проверка расширения файла
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'txt') {
        die("Разрешены только файлы с расширением .txt");
    }

    // Чтение содержимого
    $lines = file($file['tmp_name'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Обработка данных
    $data = [];
    foreach ($lines as $line) {
        // Пропуск строк с ошибками
        if (
            preg_match('/битая\s+строка/iu', $line) ||
            preg_match('/строка\s+с\s+ошибкой/iu', $line) ||
            preg_match('/лишние\s+поля/iu', $line) ||
            preg_match('/daf/iu', $line) ||
            preg_match('/man/iu', $line) ||
            substr_count($line, ',') > 3
        ) {
            continue;
        }

        // Очистка строки
        $line = trim($line);
        $line = str_replace([',,', ', ,'], ',', $line);

        // Разделение на части
        $parts = explode(',', $line, 4);

        if (count($parts) !== 4) continue;

        // Проверка веса
        $weight = intval(trim($parts[0]));
        if ($weight === 0 && $parts[0] !== '0') continue;

        // Проверка цвета и типа
        $color = trim($parts[1]);
        $type = trim($parts[2]);

        if (stripos($color, 'битая') !== false || stripos($color, 'строка') !== false) continue;
        if (stripos($type, 'битая') !== false || stripos($type, 'строка') !== false) continue;

        // Добавление данных
        $data[] = [
            'weight' => $weight,
            'color' => $color,
            'type' => $type,
            'brand' => trim($parts[3])
        ];
        // file_put_contents('data.txt', $data, FILE_APPEND);
    }

    // Сортировка
    usort($data, function ($a, $b) {
        if ($a['weight'] !== $b['weight']) return $a['weight'] <=> $b['weight'];
        if ($a['color'] !== $b['color']) return strcmp($a['color'], $b['color']);
        if ($a['type'] !== $b['type']) return strcmp($a['type'], $b['type']);
        return strcmp($a['brand'], $b['brand']);
    });

    $resultFilename = 'result.txt';
    $resultContent = '';
    
    foreach ($data as $item) {
        $resultContent .= sprintf("%d,%s,%s,%s\n",
            $item['weight'],
            $item['color'],
            $item['type'],
            $item['brand']
        );
    }
    
    file_put_contents($resultFilename, $resultContent);

    // Вывод таблицы
    echo "<h2>Результат</h2>";
    echo "<a href='$resultFilename' download>$resultFilename</a></p>";;
    echo '<table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse;">';
    echo '<tr><th>Вес</th><th>Цвет</th><th>Тип</th><th>Марка</th></tr>';
    foreach ($data as $item) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($item['weight']) . '</td>';
        echo '<td>' . htmlspecialchars($item['color']) . '</td>';
        echo '<td>' . htmlspecialchars($item['type']) . '</td>';
        echo '<td>' . htmlspecialchars($item['brand']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "Файл не загружен.";
}

echo '</body></html>';



// function logMassege($message, $level){
//     $timestamp = date("Y-m-d H:i:s");
//     $entry = "Errors \n [$timestamp] level: $level | Name: $message \n";
//     file_put_contents('data.txt', $entry, FILE_APPEND);
// }

// function logJson($message, $level = "info"){
//     $logFile = "log.json";

    
//     if(file_exists($logFile) && filesize($logFile) > 0){
//         $json = file_get_contents($logFile);
//         $logs = json_decode($json, true);
//     } else {
//         $logs = [];
//     }

//     $logData = [
//         'timestamps' => date("Y-m-d H:i:s"),
//         'level' => $level,
//         'message' => $message,
//     ];

//     $logs[] = $logData;
//     file_put_contents($logFile, json_encode($logs));
// }

// logJson("err", "error_info");


// logMassege("error", "err");




// $filename = "data.txt";
// if(file_exists($filename)){
//     $lines = file($filename, FILE_IGNOR_NEW_LINES | FILE_SKIP_EMPTY_LINES)
//     echo "<ul>";
//     foreach($lines as $line){
//         echo "<li>" . htmlentities($line) . "</li>";
//     }
//     echo "</ul>";
// } else {
//     echo "err";
// }
