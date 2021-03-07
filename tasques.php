<?php
/**
 * Plugin Name: Tasques
 * Plugin URI: https://david.madav.cat/
 * Description: Organitza tasques per realitzar un projecte.
 * Version: 1.0
 * Author: David Garcia
 * Author URI: https://david.madav.cat/
 */

add_action('admin_menu', 'test_plugin_setup_menu');
 
function test_plugin_setup_menu(){
    add_menu_page( 'Tasques by David', 'Tasques', 'manage_options', 'test-plugin', 'test_init' );
}
 
function test_init(){
?>
<html>
<h1>Administrador de tasques</h1>
<br><h3>Crear tasques</h3>
<form action="" method="post">
<br>Tasca: <input type="text" name="tasca" required> <input type="submit" name="afegir" value="Afegir" class="button">
</form>
</html>
<?php

// Connexió amb la base de dades
$host = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET;
$pdo = new PDO($host, DB_USER, DB_PASSWORD);

// Afegir tasques
if (isset($_POST["afegir"])){

	$tasca = $_POST['tasca'];

	$INSRT = "INSERT INTO wp_tasques (nom, estat) VALUES (?, ?)";
	$STMT = $pdo->prepare($INSRT);
    $STMT->execute([$tasca,'pendent']);
}

if (isset($_POST["validar"])){

    $id = $_POST['validar'];

    $UPDT = "UPDATE wp_tasques SET estat=? WHERE id=?";
    $STMT = $pdo->prepare($UPDT);
    $STMT->execute(['complert',$id]);
}

echo "<table style='width:100%'>";
echo "<tr>";
echo "<td style='vertical-align:top; width:50%; word-break: break-all'>";
echo "<h3>Tasques pendents</h3>";
// Printar les tasques pendents
try
        {
        $sql = "SELECT * FROM wp_tasques WHERE estat='pendent' ";
        $query = $pdo->query($sql);
        echo "<div class='taula'>";
            echo "<table border='0'>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            </tr>";
        while( $row = $query->fetch(PDO::FETCH_ASSOC) )
        {
            echo "<tr>";
            echo "<td>" . "- ". $row['nom'] . "</td>";
            echo "<td>"."<form action='' method='post'>".
                        "<button type='submit' value=".$row['id']." name='validar'><span style='font-size:100%'>✔</span></button>".
                        "</form>".
                 "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
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

echo "<td style='vertical-align:top; width:50%; word-break: break-all''>";
echo "<h3>Tasques complertes</h3>";

// Printar les tasques complertes
try
        {
        $sql = "SELECT * FROM wp_tasques WHERE estat='complert' ";
        $query = $pdo->query($sql);
        echo "<div class='taula'>";
            echo "<table border='0'>
            <tr>
            <th></th>
            <th></th>
            <th></th>
            </tr>";
        while( $row = $query->fetch(PDO::FETCH_ASSOC) )
        {
            echo "<tr>";
            echo "<td>" . "- ". $row['nom'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    catch(PDOException $e)
        {
        echo $e->getMessage();
        }
    finally{
        //Per tancar la connexió…
       // $pdo = null;
        }

echo "</tr>";
echo "</td>";
echo "</table>";

} 
?>
