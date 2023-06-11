<?php
session_start();
include ('base.php');
$output = '';
$search = $_POST["search"];
$id_fic = $_SESSION['id_fic'];

$sql = "SELECT id_trame, field1, id_fic, date FROM trame800 WHERE id_fic = :id_fic AND (id_trame LIKE :search OR field1 LIKE :search OR date LIKE :search)
        UNION
        SELECT id_trame, field1, id_fic, date FROM trame806 WHERE id_fic = :id_fic AND (id_trame LIKE :search OR field1 LIKE :search OR date LIKE :search)
        LIMIT 50";

$req = $bd->prepare($sql);
$req->bindValue(':id_fic', $id_fic);
$req->bindValue(':search', '%' . $search . '%');
$req->execute();
$res = $req->fetchAll();
$count = $req->rowCount();
$req->closeCursor();
if($count > 0)
{
    $output .= '<div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>Numéro de la trame</th>
                            <th>Type de la trame</th>
                            <th>ID du fichier</th>
                            <th>Date de la trame</th>
                            <th></th>
                        </tr>';
    foreach($res as $row)
    {
        if ($row['field1'] == 800){
            $typetrame = "trame800";
        }
        elseif ($row['field1'] == 806){
            $typetrame = "trame806";
        }
        $output .= '
            <tr>
                <td>'.$row["id_trame"].'</td>
                <td>0x'.$row['field1'].'</td>
                <td>'.$row["id_fic"].'</td>
                <td>'.$row["date"].'</td>
                <td>
                    <form action="trame.php" method="GET">
                        <div class="input-group mb-3">
                            <input type="hidden" name="typetrame" value='.$typetrame.'>
                            <button type="submit" class="btn btn-primary button" name="id_trame" value="'.$row['id_trame'].'">Voir trame</button>
                        </div>
                    </form>
                </td>
            </tr>
        ';
    }
    echo $output;
}
else
{
    echo '<tr>
            <td colspan="4">Aucun résultat</td>
        </tr>';
}
?>