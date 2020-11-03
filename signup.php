<?php
function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
?>
<!DOCTYPE html>
<html>
<head>

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

  <title>Formulario de login y signup - Diseño por Javi</title>

  <meta name="description" content="Formulario de login y signup - Diseño por Javi">

  <link href='https://fonts.googleapis.com/css?family=Lato:100,600' rel='stylesheet' type='text/css'>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <style>
    body {
        background-color: #e2e2e2!important;
        text-align: center;
    }

    .card {
        background: #fff;
        padding: 20px;
        border-radius: 4px;
        display: inline-block;
        margin: 2%;
        text-align: center;
    }

    input, textarea {
        width: 200px;
        display: block;
        margin: auto;
        margin-bottom: 7px;
        background: #eaeaea;
        padding: 5px 15px;
        border-radius: 3px;
        border: 1px solid #e0e0e0;
    }

    form#form_demo .send-btn {
        display: inline-block;
        background: #4CAF50;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
    }

    .disabled-btn {
        background: gray!important;
        pointer-events: none!important;
    }

  </style>

</head>
<body>

  <div class="card">
    <h2>Formulario de registro</h2>
    <form id="signup_form">
      <input type="text" name="name" placeholder="Tu nombre" required value="Javi-<?php echo(rand(1,15)); ?>">
      <input type="email" name="mail" placeholder="Tu correo" required value="prueba.<?php echo(rand(1,15)); ?>@prueba.com">
      <input type="text" name="password" placeholder="Tu contraseña" required value="<?php echo randomPassword(); ?>">
      <input type="date" name="birth_date" placeholder="Tu fecha de nacimiento" required value="1996-12-09">
      <input type="phone" name="phone" placeholder="Tu teléfono" required value="<?php echo(rand(10,100)).(rand(10,100)).(rand(10,100)).(rand(1,15)); ?>">
      <textarea type="text" name="biography" placeholder="Tu biografía" required>Hola a todos, esta es una biografía de ejemplo.</textarea>
      <input type="hidden" name="method" value="form">
      <button type="button" class="send-btn" onclick="registrar();">Registrar</button>
    </form>
    <span id="response-text"></span>
  </div>


  <?php include "body-js.php"; ?>

</body>
</html>