<?php
//phpinfo() ;
$tableauRootDir=$_SERVER['DOCUMENT_ROOT'] ;
set_include_path(get_include_path().":$tableauRootDir/tableau/includes:$tableauRootDir/tableau/firephp") ;
spl_autoload_extensions(".class.php") ;
spl_autoload_register() ;

ob_start() ;

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

$lesEducatrices=Educatrices::getInstance() ;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link rel="stylesheet" type="text/css" media="all" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/text.css" />
    <link rel="stylesheet" type="text/css" media="all" href="css/1248_16_10_10.css" />

    <title>Où sont les enfants?(modifications)</title>
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
$firePHP->log($listeEducatrices,"listeEducatrices") ;
if(count($listeEducatrices))
{
    foreach($listeEducatrices as $educatrice)
    {
        $firePHP->log($educatrice,'educatrice') ;
?>
<div class="row framedInBlack">
    <div id="educatrice<?=$educatrice->getId()?>" class="column grid_4 framedInRed">
        <?=$educatrice->getNom()?>
    </div>
    <div id="local<?=$educatrice->getId()?>" class="column grid_4 framedInBlue">
        nom du local.
    </div>
</div>
<?php
        }
    }
?>

</body>