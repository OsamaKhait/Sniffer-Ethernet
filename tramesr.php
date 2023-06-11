<?php
    require('base.php');
    include_once 'includes/header.php';
?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-body">
                        <h4 class="text-center font">Les fichiers insérés dans la base de données</h4>
                        <table id="data-table" class="table table-bordered">
                            <!-- Contenu du tableau -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-4">
        <!-- Contenu du pied de page -->
    </footer>

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

        $(document).ready(function() {
            // Function to refresh the data
            function refreshData() {
                $.ajax({
                    url:'refresh.php', // Remplacez 'refresh.php' par l'URL de votre fichier PHP contenant la requête
                    type:'GET',
                    success: function(response) {
                        $('#data-table').html(response); // Met à jour le contenu du tableau avec la réponse du serveur
                    },
                    error: function() {
                        alert('Une erreur s\'est produite lors du rafraîchissement des données.');
                    }
                });
            }

            // Événement de clic sur le bouton
            $('#refresh-button').click(function() {
                refreshData();
            });

            // Chargement initial des données
            refreshData();
        });

        $(document).on('click', '.confirm', function(e) {
            e.preventDefault(); // Empêche la soumission par défaut du formulaire
            var form = $(this).closest('form'); // Récupère le formulaire le plus proche
            
            $.confirm({
                title: 'Attention !',
                content: 'Êtes-vous sûr de vouloir supprimer ce fichier ?',
                buttons: {
                    Oui: function () {
                        form.trigger('submit'); // Soumet le formulaire si l'utilisateur confirme
                    },
                    Annuler: function () {
                        // Ne rien faire si l'utilisateur annule
                    }
                }
            });
        });
    </script>

<?php
    include_once 'includes/footer.php';
?>