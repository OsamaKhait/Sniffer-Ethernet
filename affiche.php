<?php
    include_once 'includes/header.php';
?>

<?php
if (empty($_SESSION['page'])){
    $_SESSION['page']=1;
}
if (isset($_GET["page"])) { 
    $page  = $_GET["page"];
} else { 
    $page=1; 
}
if (isset($_GET["id_fic"])) { 
    $_SESSION['id_fic']  = $_GET['id_fic'];
}
?>
        <?php
        include ("base.php");
        $totalrec = "50";
        $debut = $page - 1;
        $debut = $debut * $totalrec;
        $query = "SELECT field1,id_fic,date FROM trame800 WHERE trame800.id_fic=:id_fic UNION SELECT field1,id_fic,date FROM trame806 WHERE trame806.id_fic=:id_fic";
        $req = $bd->prepare($query);
        $req->bindValue(':id_fic', $_GET['id_fic']);
        $req->execute();
        $count = $req->rowCount();
        $req->closeCursor();
        $totalpage = ceil($count / $totalrec);
        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mt-4">
                        <div class="card-header">
                        <h4 class="text-center font">Recherchez la trame souhaité par son ID dans la base ou par sa date</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="input-group mb-3">
                                        <input type="text" name="search" id="search" class="form-control" placeholder="Recherche">
                                    </div>
                                    </form> 
                                    <form method="GET" action="">
                                        <select id="selec_page" name="page" class="btn btn-primary page" onchange="this.form.submit();">  
                                            <option value="Select">Page <?php if(isset($_GET['page'])){echo $_GET['page'];} ?></option> 
                                            <?php
                                            if ($totalpage > 1) {
                                                for ($i = 1; $i <= $totalpage; $i++) {
                                                    echo '<option value="'.$i.'">'.$i.'</a>';
                                                }
                                            }
                                            ?> 
                                        <input type="hidden" name="id_fic" value="<?php echo $_GET['id_fic']; ?>">
                                        </select>   
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card mt-4">
                        <div class="card-body">
                            <table id="result" class="table table-bordered">
                            <h4 class="text-center text-decoration-underline font">Affichage des trames du fichier sélectionné par recherche</h4>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card mt-4">
                        <div class="card-body">
                            <table class="table table-bordered">
                            <h4 class="text-center text-decoration-underline font">Affichage des trames du fichier sélectionné par page</h4>
                                    <tr>
                                        <th>Numéro de la trame</th>
                                        <th>Type de trame</th>
                                        <th>ID du fichier</th>
                                        <th>Date de la trame</th>
                                    </tr>
                                    <?php 
                                        if(isset($_GET['id_fic']))
                                        {
                                            $filtre = $_GET['id_fic'];
                                            $query = "SELECT id_trame,field1,id_fic,date FROM trame800 WHERE trame800.id_fic=:id_fic UNION SELECT id_trame,field1,id_fic,date FROM trame806 WHERE trame806.id_fic=:id_fic ORDER BY date LIMIT $debut,$totalrec";
                                            $req = $bd->prepare($query);
                                            $req->bindValue(':id_fic', $_GET['id_fic']);
                                            $req->execute();
                                            $res = $req->fetchall();
                                            $req->closeCursor();

                                            if($count > 0)
                                            {
                                                foreach($res as $items)
                                                {
                                                    ?>
                                                    <tr>
                                                        <td><?= $items['id_trame']; ?></td>
                                                        <td>0x<?= $items['field1'];
                                                            if ($items['field1'] == 800){
                                                                echo "&nbsp(UDP)";
                                                                $typetrame = "trame800";
                                                            }
                                                            elseif ($items['field1'] == 806){
                                                                echo "&nbsp(ARP)";
                                                                $typetrame = "trame806";
                                                            }
                                                            ?></td>
                                                        <td><?= $items['id_fic']; ?></td>
                                                        <td><?= $items['date']; ?></td>
                                                        <td>
                                                        <form action="trame.php" method="GET">
                                                            <div class="input-group mb-3">
                                                                <input type="hidden" name="typetrame" value=<?php echo $typetrame; ?>>
                                                                <button type="submit" class="btn btn-primary button" name="id_trame" value="<?php if(isset($items['id_trame'])){echo $items['id_trame']; } ?>">Voir trame</button>
                                                            </div>
                                                        </form>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                ?>
                                                    <tr>
                                                        <td colspan="4">Aucun résultat</td>
                                                    </tr>
                                                <?php
                                            }
                                        }
                                    ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer>
        </footer>
    </body>
</html>
<script>
    $(document).ready(function(){
        $('#search').keyup(function(){
            var txt = $(this).val();
            if(txt != '')
            {
                $.ajax({
                    url:"fetchtrame.php",
                    method:"post",
                    data:{search:txt},
                    dataType:"text",
                    success:function(data)
                    {
                        $('#result').html(data);
                    }
                });
            }
            else
            {
                $('#result').html('');
            }
        });
    });
</script>

<?php
    include_once 'includes/footer.php';
?>