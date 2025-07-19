<?php   

    $filename = "data.txt";
if(file_exists($filename)){
    $lines = file($filename);
    echo "<ul>";
    foreach($lines as $line){
        echo "<li>" . htmlentities($line) . "</li>";
    }
    echo "</ul>";
} else {
    echo "err";
}