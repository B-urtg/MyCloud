<?php declare(strict_types=1);
require_once 'config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
function ip_details($ip) {
    $json = file_get_contents("http://ipinfo.io/{$ip}/geo");
    return json_decode ($json);
}
$user= htmlentities ($_POST['user'], ENT_QUOTES, "UTF-8");
$pass= htmlentities ($_POST['pass'], ENT_QUOTES, "UTF-8");
$pass = htmlentities ($pass, ENT_QUOTES, "UTF-8");
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME); // połączenie z BD – wpisać swoje dane
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD
mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków
$result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'"); // wiersza, w którym login=login z formularza
$rekord = mysqli_fetch_array($result); // wiersza z BD, struktura zmiennej jak w BD
if(!$rekord) //Jeśli brak, to nie ma użytkownika o podanym loginie
{
 mysqli_close($link); // zamknięcie połączenia z BD
 header("Location: index.php");
 exit();
}
else
{ // jeśli $rekord istnieje
 if($rekord['password']==$pass) // czy hasło zgadza się z BD
 {
  session_start();
  $ipaddress = $_SERVER["REMOTE_ADDR"];
  if (!isset($_SESSION['visitor_logged']))
  {
    if ($link) 
    {
        mysqli_query($link, "SET NAMES 'utf8'");
        $sql = "INSERT INTO visitors (ipaddress) VALUES ('$ipaddress')";
        if (mysqli_query($link, $sql))
        {
            $_SESSION['visitor_logged'] = $ipaddress;
        }
        mysqli_close($link);
    }
  }
  $clean_user_dir_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $rekord['username']);
  $_SESSION['loggedin']=true;
  $_SESSION['user']=$rekord['username'];
  $_SESSION['avatar']=$rekord['avatar'];
  $_SESSION['user_home'] = '/mnt/drive/users/' . $clean_user_dir_name . '/';
  mysqli_close($link);
  header("Location: user_panel.php");
  exit();
 }
 else
 {
  $_SESSION['loggedin']=false;
  mysqli_close($link);
  header("Location: index.php");
  exit();
 }
}
?>
