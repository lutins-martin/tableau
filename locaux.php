<?php
//phpinfo() ;
include_once("includes/startup.php") ;
try
{
  //create or open the database
  $database = Database::getInstance() ;
  $firePHP = FirePHP::getInstance(true) ;
  $firePHP->setEnabled(true) ;
}
catch(Exception $e)
{
  die($error);
}

$lesGroupes=Groupes::getInstance() ;
$lesLocaux=Locaux::getInstance() ;
$lesEducatrices=Educatrices::getInstance() ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" type="text/css" media="all" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/text.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/1248_16_10_10.css" />

    <title>Où sont les moussaillons?</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</html>
<body>
<div id="wrapper" class="row">
    <div id="hd">
        <h1 id="header">Où sont les moussaillons?</h1>
    </div>
</div>
<?php
$listeEducatrices = $lesEducatrices->getLesEducatrices() ;
if(count($listeEducatrices))
{
    foreach($listeEducatrices as $educatrice)
    {
        $firePHP->log($educatrice,'educatrice') ;
?>
<div class="row">
		<div class="column framedInBlack grid_9">
			<div id="educatrice<?=$educatrice->getId()?>"
				class="column grid_4 framedInRed">
				<?="{$educatrice->getNom()} ({$educatrice->getGroupe()->getNom()})"?>
			</div>
			<div id="local<?=$educatrice->getId()?>"
				class="column grid_4 framedInBlue"><?="{$educatrice->getGroupe()->getLocal()->getNom()}"?></div>
		</div>
	</div>
<?php
        }
    }
include("pieddepage.php") ;
?>

</body>