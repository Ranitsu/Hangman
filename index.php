<html>
<head>
    <title>Wisielec</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- <script src="scripts.js"></script> -->
</head>
<body>

<?php
if (isset($_POST["char"])) {
    $char_post = $_POST["char"];
} else {
    $char_post = "";
}

echo "<br><br>";

if (isset($_POST["new_game"])) {
    $chars = array();
    $wrong_chars = array();
    $random_entry = Get_random_entry();
    $entry = $random_entry[0];
    $category = $random_entry[1];
    $no_found_chars = array();
} else {
    $chars = json_decode(htmlspecialchars_decode($_POST["chars"]));
    $wrong_chars = json_decode(htmlspecialchars_decode($_POST["wrong_chars"]));
    $entry = $_POST['entry'];
    $category = $_POST['category'];
    $no_found_chars = json_decode((htmlspecialchars_decode($_POST['no_found_chars'])));
}

$lives = 10;
$char_array = str_split($entry);
$entry_count = strlen($entry);

echo "<br><br><br>";
$chars[] = $char_post;

echo $category . "<br><br>";

$found_char = false;

foreach ($char_array as $char) {
    if (stripos($char_post, $char) !== false) {
        echo $char;
        $found_char = true;
    } elseif (in_array_i($char, $chars) === true) {
        echo $char;
    } elseif ($char == " ") {
        echo "&nbsp";
    } else { 
        echo "_";
    }

    echo " ";
}

echo "<br><br>";
echo "<br><br><br>Nie trafione: <br>";

if ($found_char === false) {
    $no_found_chars[] = $char_post;

    if (count($no_found_chars) > $lives)
        echo "PRZEGRANA<br><br>";
}
echo count($no_found_chars);
DrawHangman(count($no_found_chars));

foreach ($no_found_chars as $no_found_char) {
    if ($no_found_char !== "")
        echo $no_found_char . ", ";
}


function in_array_i($char, $array) {
    foreach ($array as $value) {
        if (stripos($char, $value) !== false) {
            return true;
        }
    }
    return false;
}

function Get_random_entry() {
    $servername = "localhost";
    $username = "root";
    $password = "Budap3st";
    $database = "Wisielec";
    
    $mysqli = new mysqli($servername, $username, $password, $database);
    
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    }
    
    // echo "Connected successfully <br>";
    
    $sql = 'SELECT id, name, category FROM entry';
    
    // echo $sql . "<br>";
    $entries = array();
    $result = $mysqli->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $entries[] = ["id" => $row["id"], "name" => $row["name"], "category" => $row["category"]];
            // echo "id: " . $row["id"]. " - Name: " . $row["name"]. ", Category: " . $row["category"]. "<br>";
        }
    } else {
        echo "0 results";
    }
    $mysqli->close();

    $random_entry_key = array_rand($entries);

    // print_r($entries[$random_entry_key]);

    $entry = $entries[$random_entry_key]['name'];
    $category = $entries[$random_entry_key]['category'];

    return array($entry, $category);
}

function DrawHangman($step) {
    switch ($step) {
        case 1:
            echo "<br>|<br>|<br>|<br>|<br>|";
            break;
        case 2:
            echo "______<br>|<br>|<br>|<br>|<br>|";
            break;
        case 3:
            echo "______<br>| /<br>|/<br>|<br>|<br>|";
            break;
        case 4:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/<br>|<br>|<br>|";
            break;
        case 5:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/&nbsp&nbsp&nbspO<br>|<br>|<br>|";
            break;
        case 6:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/&nbsp&nbsp&nbspO<br>|&nbsp&nbsp&nbsp&nbsp|<br>|<br>|";
            break;
        case 7:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/&nbsp&nbsp&nbspO<br>|&nbsp&nbsp&nbsp&nbsp/|<br>|<br>|";
            break;
        case 8:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/&nbsp&nbsp&nbspO<br>|&nbsp&nbsp&nbsp&nbsp/|\<br>|<br>|";
            break;
        case 9:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/&nbsp&nbsp&nbspO<br>|&nbsp&nbsp&nbsp&nbsp/|\<br>|&nbsp&nbsp&nbsp&nbsp/<br>|";
            break;
        case 10:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/&nbsp&nbsp&nbspO<br>|&nbsp&nbsp&nbsp&nbsp/|\<br>|&nbsp&nbsp&nbsp&nbsp/\<br>|";
            break;
    }
}
?>


<form action="index.php"  method="post" >
    <br><br>
    Podaj litere: 
    <input type="text" name="char" maxlength="1" size="1">
    <br><br>
    <?php
    echo '<input type="hidden" name="chars" value="'.htmlspecialchars(json_encode($chars)).'">';
    echo '<input type="hidden" name="no_found_chars" value="'.htmlspecialchars(json_encode($no_found_chars)).'">';
    echo '<input type="hidden" name="entry" value="'.$entry.'">';
    echo '<input type="hidden" name="category" value="'.$category.'">';
    ?>
    <input type="submit" name="submit" value="Wyślij">
</form>

<form action="index.php"  method="post">
    <input type="hidden" name="new_game" value="new_game">
    <input type="submit" name="new_game" value="Nowe hasło">
</form>

</body>
</html>

<!-- _______
| /   |
|/    O
|    /I\    
|     A
| -->