<?php
    require('base.php');
    include_once 'includes/header.php';
?>

<div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card mt-4">
                        <div class="card-header">
                            <h4 class="text-center font">Recherche du fichier par son ID</h4>
                            <h5><?php if(isset($message)){echo $message;} ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="input-group mb-3">
                                        <input type="text" name="search" id="search" class="form-control" placeholder="Recherche">
                                    </div>
                                    <div>
                                        <button id="refresh-button" class="btn btn-primary" type="button">Chercher</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card mt-4">
                        <div class="card-body">
                            <table id="result" class="table table-bordered">
                            <h4 class="text-center font">Resultat</h4>
                            </table>
                        </div>
                    </div>
                </div>

<script>
    $(document).ready(function(){
        $('#search').keyup(function(){
            var txt = $(this).val();
            if(txt != '')
            {
                $.ajax({
                    url:"fetchfic.php",
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