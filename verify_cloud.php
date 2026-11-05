<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
function ip_details($ip) {
    $json = file_get_contents("http://ipinfo.io/{$ip}/geo");
    return json_decode ($json);
}
$user= htmlentities ($_POST['user'], ENT_QUOTES, "UTF-8");
$pass= htmlentities ($_POST['pass'], ENT_QUOTES, "UTF-8");
$pass = htmlentities ($pass, ENT_QUOTES, "UTF-8");
$link = mysqli_connect('localhost', 'root', 'pppp', 'Zawada_cloud'); // połączenie z BD – wpisać swoje dane
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
  if (isset($_POST['geoblokada'])) {
    $ipaddress = $_SERVER["REMOTE_ADDR"];
    $details = ip_details($ipaddress);
    if ($details && isset($details->country) && $details->country !== 'PL') {
        $_SESSION['loggedin']=false;
        mysqli_close($link);
        header("Location: index.php");
        exit();
    }
  }
  $_SESSION['loggedin']=true;
  $_SESSION['user']=$rekord['username'];
  $_SESSION['avatar']=$rekord['avatar'];
  mysqli_close($link);
  header("Location: index.php");
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
