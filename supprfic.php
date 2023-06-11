<?php
session_start();
include ('base.php');

$_SESSION['supprok'] = "Fichier et ses données supprimées avec succès.";
$tab = array("trame806", "trame800", "fic");

foreach ($tab as $i)
{
    $sql_delete_queru = "DELETE FROM $i WHERE id_fic=:id_fic";
    $req = $bd->prepare($sql_delete_queru);
    $req->bindValue(':id_fic', $_GET['id_fic']);
    $req->execute();
}

header ('Location: index.php');
exit();
?>