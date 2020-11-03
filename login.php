<?php include "initializer.php"; ?>

<!DOCTYPE html>
<html>
<head>

 <?php include "meta.php"; ?>

   <title>Página de Inicio - <?php getString("main_title") ?></title>

   <meta name="description" content="Descripción de inicio - <?php getString("main_description") ?>">

   <?php include "head-css.php"; ?>

   <?php include "head-js.php"; ?>

  <style>
    body {
        background-color: #e2e2e2!important;
        text-align: center;
    }

    .card {
        background: #fff;
        border-radius: 10px;
        display: inline-block;
        margin: 2%;
        text-align: center;
        width: 500px;
        max-width: 100%;
        padding: 30px 15px;
        border: 1px solid #d8d8d8;
        box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.2);
    }

    .form-cont input, textarea {
        width: 100%;
        display: block;
        margin: auto;
        margin-bottom: 7px;
        background: #eaeaea;
        padding: 5px 15px;
        border-radius: 3px;
        border: 1px solid #e0e0e0;
    }

    .form-cont .send-btn {
        display: inline-block;
        background: #4faacc;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
    }

    .disabled-btn {
        background: gray!important;
        pointer-events: none!important;
    }

    .logo {
        width: 200px;
        margin: auto;
        height: 100px;
    }

    .logo img {
        max-width: 100%;
        max-height: 100%;
    }

    .form-cont h2 {
        margin: 15px 0px;
        font-size: 30px;
        font-weight: 300;
    }

    #login_form .tag-input {
        width: 350px;
        margin: 15px auto;
    }

  </style>

</head>
<body class="gray-body general">

  <?php include "credits.php"; ?>

  <?php include "navbar.php"; ?>

  <!-- Contenido principal -->
  <div class="main">
    <div class="login-signup">
      <div class="card">
        <div class="logo">
          <img src="http://grupovizcaya.mx/images/logo.png">
        </div>
        <?php
          if (!$user_class->verify_login()){
            echo $user_class->verify_login();
            echo '
            <div class="form-cont">
              <h2>Inicia sesión</h2>
              <form id="login_form">
              <div class="tag-input">      
                <input type="email" name="mail" placeholder="Tu correo" required>
              </div>
              <div class="tag-input">
                <input type="password" name="password" placeholder="Tu contraseña" required>
              </div>
                <button type="button" class="send-btn" onclick="trylogin();">Iniciar sesión</button>
              </form>
              <span id="response-text"></span>
            <div>
            ';
          } else {
            $user_class->trigger_login();
            echo '
            <h2>Bienvenido '.$user_class->user_data_array["name"].'</h2>
            <span><strong>Nombre: </strong>'.$user_class->user_data_array["name"].'</span><br>
            <span><strong>Correo: </strong>'.$user_class->user_data_array["mail"].'</span><br>
            <span><strong>Fecha de nacimiento: </strong>'.$user_class->user_data_array["birth_date"].'</span><br>
            <span><strong>Teléfono: </strong>'.$user_class->user_data_array["phone"].'</span><br>
            <span><strong>Biografía: </strong>'.$user_class->user_data_array["biography"].'</span><br>
            <hr>
            <button type="button" class="send-btn" onclick="logout();">Cerrar sesión</button>
            <span></span>
            ';
          }
        ?>    
      </div>
    </div>
  </div>

  <!-- Fin Contenido principal -->  

  <?php include "footer.php"; ?>

  <?php include "body-js.php"; ?>

</body>
</html>