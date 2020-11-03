<?php include "initializer.php"; ?>

<!DOCTYPE html>
<html>
<head>

  <?php include "meta.php"; ?>

  <title>Página de Inicio - <?php getString("main_title") ?></title>

  <meta name="description" content="Descripción de inicio - <?php getString("main_description") ?>">

  <?php include "head-css.php"; ?>

  <?php include "head-js.php"; ?>

  <style type="text/css">
  	body.gray-body {
  	    background: #f1f1f1;
  	}

  	.small-card {
  	    height: 160px;
  	    background: #fff;
  	    border-radius: 10px;
  	    box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.1);
  	    overflow: hidden;
  	    margin-bottom: 15px;
  	}

  	.ftd-img {
  	    height: 100%;
  	    background: #cacaca;
  	    background-image: url(https://i0.wp.com/zblogged.com/wp-content/uploads/2019/02/FakeDP.jpeg?resize=567%2C580&ssl=1);
  	    background-size: cover;
  	    background-position: center;
  	}

  	.small-card .title {
  	    font-size: 23px;
  	    margin: 10px 0px;
  	    margin-top: 15px;
  	}

  	.small-card .subtitle {
  	    display: block;
  	}  	
  </style>

</head>
<body class="gray-body general">

	<?php include "credits.php"; ?>

	<!-- Contenido principal -->

		<div class="main">
			<div class="dashboard col-md-12">

				<h2>Fixme</h2>

			</div>
		</div>

	<!-- Fin Contenido principal -->

	<?php include "footer.php"; ?>

<?php include "body-js.php"; ?>
</body>
</html>