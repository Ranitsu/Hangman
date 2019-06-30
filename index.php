<html>
<head>
    <title>Wisielec</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div id="main">
<?php
if (isset($_POST["char"])) {
    $char_post = $_POST["char"];
} else {
    $char_post = "";
}

if ((!(isset($_POST["new_game"])) && !(isset($_POST["chars"]))) || isset($_POST["new_game"])) {
    $chars = array();
    $random_entry = Get_random_entry();
    $entry = $random_entry[0];
    $category = $random_entry[1];
    $no_found_chars = array();
} else {
    $chars = json_decode(htmlspecialchars_decode($_POST["chars"]));
    $entry = $_POST['entry'];
    $category = $_POST['category'];
    $no_found_chars = json_decode((htmlspecialchars_decode($_POST['no_found_chars'])));
}

$lives = 10;
$char_array = str_split($entry);
$entry_count = strlen($entry);

echo "<br>";
if($char_post != "")
    $chars[] = $char_post;


echo "<div id='category'>";
echo $category . "<br><br>";
echo "</div>";

$found_char = false;
echo "<div id='entry'>";
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

echo "</div>";
if (!(empty($chars)) && !(empty($char_array))) {
    if (CheckWin($chars, $char_array)) {
        echo "<script type='text/javascript'>
        window.onload = function() {
        alert('Wygrałeś!');
        };
        </script>";
    }
}

echo "<div id='not_found'><b>Nie trafione: </b><br>";

if (!(in_array_i($char_post, $char_array)) && $char_post != "")
    $no_found_chars[] = $char_post;

if (count($no_found_chars) > $lives)
    echo "<script type='text/javascript'>
    window.onload = function() {
    alert('Przegrałeś!');
    };
    </script>";
foreach ($no_found_chars as $no_found_char) {
    if ($no_found_char !== "")
        echo $no_found_char . ", ";
}


echo "</div>";
echo "<div id='hangman'>";
DrawHangman((count($no_found_chars)));
echo "</div><br>";

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
    $username = "xxx";
    $password = "xxx";
    $database = "xxx";
    
    $mysqli = new mysqli($servername, $username, $password, $database);
    
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    }
        
    $sql = 'SELECT id, name, category FROM entries';
    
    $entries = array();
    $result = $mysqli->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $entries[] = ["id" => $row["id"], "name" => $row["name"], "category" => $row["category"]];
        }
    } else {
        echo "0 results";
    }
    $mysqli->close();

    $random_entry_key = array_rand($entries);

    $entry = $entries[$random_entry_key]['name'];
    $category = $entries[$random_entry_key]['category'];

    return array($entry, $category);
}

function DrawHangman($step) {
    switch ($step) {
        case 0: 
            echo "";
            break;
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
        default:
            echo "______<br>| /&nbsp&nbsp&nbsp|<br>|/&nbsp&nbsp&nbspO<br>|&nbsp&nbsp&nbsp&nbsp/|\<br>|&nbsp&nbsp&nbsp&nbsp/\<br>|";
            break;
    }
}

function CheckWin($send_chars, $entry_chars) {
    $uniq_send_chars = array();
    $uniq_entry_chars = array();

    if (is_array($send_chars) && is_array($entry_chars)) {

        foreach ($entry_chars as $char) {
            if (!(in_array_i($char, $uniq_entry_chars)))
                $uniq_entry_chars[] = $char;
        }

        foreach ($send_chars as $char) {
            if (!(in_array_i($char, $uniq_send_chars)))
                $uniq_send_chars[] = $char;
        }

        $found_chars = array();

        foreach ($uniq_send_chars as $char) {
            if (in_array_i($char, $uniq_entry_chars) && !(in_array_i($char, $found_chars))) {
                $found_chars[] = $char;
            }
        }

        $uec_count = count($uniq_entry_chars);
        $ufc_count = count($found_chars);

        if ($ufc_count > 1 && $uec_count === $ufc_count) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
?>
<div id='form'><br>
    <form action="index.php"  method="post" >
    <br>
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
</div>
</div>

</body>
</html>