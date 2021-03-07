<?php
/**
 * Plugin Name: Timer
 * Plugin URI: https://david.madav.cat/
 * Description: Calcula el temps que portes destinat al projecte.
 * Version: 1.0
 * Author: David Garcia
 * Author URI: https://david.madav.cat/
 */

add_action('admin_menu', 'timer_plugin_setup_menu');
 
function timer_plugin_setup_menu(){
    add_menu_page( 'Timer by David', 'Timer', 'manage_options', 'timer_plugin', 'timer_plugin' );
}
 
function timer_plugin(){
?>
<html>
<h1>Timer - Calcula el temps del teu projecte</h1>
<form action="" method="post">
<br><input type="submit" name="sumar" value="Comença a comptar" class="button">
<input type="submit" name="parar" value="Para de comptar" class="button">
</form>
</html>
<?php

// Connexió amb la base de dades
$host = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
$pdo = new PDO($host, DB_USER, DB_PASSWORD);

// Afegir tasques
if (isset($_POST["sumar"])){

	$INSRT = "INSERT INTO wp_timer (temps_inici) VALUES (now())";
	$STMT = $pdo->prepare($INSRT);
    $STMT->execute();
    echo "<a style='color:green;'>El contador està en marxa...</a>";
}

if (isset($_POST["parar"])){

    $INSRT = "UPDATE wp_timer SET temps_fi = NOW() WHERE id=(SELECT MAX(id) FROM wp_timer)";
    $STMT = $pdo->prepare($INSRT);
    $STMT->execute();

    $INSRT2 = "UPDATE wp_timer SET duracio = TIMEDIFF(temps_fi, temps_inici) WHERE id=(SELECT MAX(id) FROM wp_timer)";
    $STMT2 = $pdo->prepare($INSRT2);
    $STMT2->execute();
}

echo "<table style='width:100%'>";
echo "<tr>";
echo "<td style='vertical-align:top; width:50%; word-break: break-all'>";
echo "<h3>Sessions de temps</h3>";
// Printar les tasques pendents
try
        {
        // Càlcul de temps total del projecte
        $query2 = $pdo->prepare("SELECT SUM(duracio) as total FROM wp_timer");
        $query2->execute();
        $row2 = $query2->fetch();
        $temps = gmdate("H:i:s", $row2['total']);


        // Printar totes les sessions de temps
        $sql = "SELECT * FROM wp_timer";
        $query = $pdo->query($sql);
        echo "<div class='taula'>";
            echo "<table border='0'>
            <tr>
            <th>Inici de la sessió</th>
            <th>&nbsp;&nbsp;&nbsp;&nbsp;Fi de la sessió</th>
            <th>&nbsp;&nbsp;&nbsp;&nbsp;Duració de la sessió</th>
            </tr>";
        while( $row = $query->fetch(PDO::FETCH_ASSOC) )
        {
            echo "<tr>";
            // BORRAR:
            // <td style='width:350px; word-break: break-all;'>
            echo "<td>".$row['temps_inici']."</td>";
            echo "<td>".'&nbsp;&nbsp;&nbsp;&nbsp;'.$row['temps_fi']."</td>";
            echo "<td>".'&nbsp;&nbsp;&nbsp;&nbsp;'.$row['duracio']."</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
        echo "<br><b>Total de temps destinat al projecte:</b> <a style='color:blue;'>$temps</a>";
    }
    catch(PDOException $e)
        {
        echo $e->getMessage();
        }
    finally{
        //Per tancar la connexió…
       // $pdo = null;
        }


echo "</td>";
echo "</tr>";
echo "</table>";

} 
?>
