<?php

  include './templates/header.php';
  
  if (!$userController->isUserLoggedIn()) {
    header('Location: login.php');
  }

  //segundo factor
  $user = $userController->getUser();
  
  $hasTwoFactorActive = true;

  if ($user['two_factor_key'] === null) {
      $hasTwoFactorActive = false;
      $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
      $secret = $g->generateSecret();
      $qrCode = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($user['name'], $secret, "Purocodigo");
  } 

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Segundo factor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>

    <?php include './templates/nav.php' ?>

    <?php if (!$hasTwoFactorActive): ?>
        <div class="container mt-5">        
            <h5>Activar doble factor de autenticacion</h5><hr />
            <p>1. Para activar el segundo factor de autenticacion instale Google Authenticator en su telefono y escanee el codigo QR</p>
            <img src="<?= $qrCode ?>" alt="Codigo QR">

            <p class="mt-4">2. Escriba el codigo generado por Google Authenticator y presione activar doble factor</p>
            <div class="row">
                <div class="col-md-4">
                    <form id="activate-second-factor">                   
                        <div class="form-group">
                            <label for="code">Codigo</label>
                            <input type="text" class="form-control" id="code">            
                        </div>
                        <button type="submit" class="btn btn-primary">Activar doble factor</button>
                    </form>              
                    <div class="alert alert-danger mt-4 d-none" id="error-message"></div>  
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="container mt-5">        
            <h5>Desactivar doble factor de autenticacion</h5><hr />
            <button type="button" class="btn btn-primary" id="deactivate-second-factor">Desactivar doble factor</button>
        </div>
    <?php endif; ?>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.2/axios.min.js"></script>
    <?php if (!$hasTwoFactorActive): ?>
        <script>
            document.getElementById('activate-second-factor').onsubmit = (e) => {
                e.preventDefault();

                const errorMessage = document.getElementById('error-message');
                errorMessage.classList.add('d-none');            
                const code = document.getElementById('code').value;
                const secret = '<?= $secret ?>';

                if (!code || !secret) {
                    return;
                }

                axios.post('api/activatesecondfactor.php', { code: code, secret: secret })
                    .then(res => {                        
                        window.location = 'panel-secondfactor.php';
                    })
                    .catch(err => {
                        errorMessage.innerText = err.response.data;
                        errorMessage.classList.remove('d-none');                    
                    });

            }
        </script>
    <?php else: ?>
        <script>
            document.getElementById('deactivate-second-factor').onclick = (e) => {
                e.preventDefault();
                axios.post('api/deactivatesecondfactor.php')
                    .then(res => {
                        window.location = 'panel-secondfactor.php';
                    });
            }
        </script>
    <?php endif; ?>

</body>
</html>