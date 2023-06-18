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
if (isset($_GET["numfic"])) { 
    $_SESSION['numfic']  = $_GET['numfic'];
}
?>
        <?php
        include ("base.php");
        $totalrec = "50";
        $debut = $page - 1;
        $debut = $debut * $totalrec;
        $query = "SELECT field1,numfic,date FROM trame800 WHERE trame800.numfic=:numfic UNION SELECT field1,numfic,date FROM trame806 WHERE trame806.numfic=:numfic";
        $req = $bd->prepare($query);
        $req->bindValue(':numfic', $_GET['numfic']);
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
                                        <input type="hidden" name="numfic" value="<?php echo $_GET['numfic']; ?>">
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
                                        if(isset($_GET['numfic']))
                                        {
                                            $filtre = $_GET['numfic'];
                                            $query = "SELECT numtrame,field1,numfic,date FROM trame800 WHERE trame800.numfic=:numfic UNION SELECT numtrame,field1,numfic,date FROM trame806 WHERE trame806.numfic=:numfic ORDER BY date LIMIT $debut,$totalrec";
                                            $req = $bd->prepare($query);
                                            $req->bindValue(':numfic', $_GET['numfic']);
                                            $req->execute();
                                            $res = $req->fetchall();
                                            $req->closeCursor();

                                            if($count > 0)
                                            {
                                                foreach($res as $items)
                                                {
                                                    ?>
                                                    <tr>
                                                        <td><?= $items['numtrame']; ?></td>
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
                                                        <td><?= $items['numfic']; ?></td>
                                                        <td><?= $items['date']; ?></td>
                                                        <td>
                                                        <form action="trame.php" method="GET">
                                                            <div class="input-group mb-3">
                                                                <input type="hidden" name="typetrame" value=<?php echo $typetrame; ?>>
                                                                <button type="submit" class="btn btn-primary button voirTrame" id="voirTrame_<?php echo $items['numtrame']; ?>_<?php echo $typetrame; ?>">Détails...</button>
                                                            </div>
                                                        </form>
                                                        </td>
                                                    </tr>
                                                    <tr id="iframe-detail_<?php echo $items['numtrame']; ?>_<?php echo $typetrame; ?>" class="frame_details" style="display:none; background-color:azure;">
                                                        <td colspan="5" class="iframe-container">
                                                            <iframe class="iframe-details" src="trame.php?typetrame=<?php echo $typetrame;?>&numtrame=<?php if(isset($items['numtrame'])){echo $items['numtrame']; } ?>" frameborder="0" width="100%" height="100%"></iframe>
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

        // gestion du submit du bouton Voir trame
        $('.voirTrame').on("click",function(e){
            e.preventDefault();
            console.log($(this).prop('id'));
            let iframeID='#'+$(this).prop('id').replace('voirTrame','iframe-detail');
            $(iframeID).toggle();
            adjustIframeHeight();
        });

        // traimement de la taille de l'iframe
        function adjustIframeHeight() {
        var iframes = document.getElementsByClassName('iframe-details');

        for (var i = 0; i < iframes.length; i++) {
            var iframe = iframes[i];
            var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;

            if (iframeDocument.body) {
            iframe.style.height = iframeDocument.body.scrollHeight + 'px';
            }
        }
        }
        adjustIframeHeight();
        window.addEventListener('resize', adjustIframeHeight);


        
    });
</script>

<?php
    include_once 'includes/footer.php';
?>