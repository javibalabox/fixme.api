<?php
$dataArray = array(
	"logo"=>$extras_class->logo,
	"home_url"=>$extras_class->home_url
);
echo '
<!-- Inicio Navbar -->
'.$extras_class->getTemplate("navbar",$dataArray).'
<!-- Fin Navbar -->
';
?>