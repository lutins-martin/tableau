<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<?php
//phpinfo() ;
$tableauRootDir=$_SERVER['DOCUMENT_ROOT'] ;
set_include_path(get_include_path().":$tableauRootDir/tableau/includes") ;
spl_autoload_extensions(".class.php") ;
spl_autoload_register() ;

try
{
  //create or open the database
  $database = Database::getInstance() ;
}
catch(Exception $e)
{
  die($error);
}

?>
<html>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

    <title>Où sont les enfants?</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</html>
<body>
<div id="wrapper">
    <div id="hd">
        <p id="header">Où sont les moussaillons?</p>
    </div>
    <div class="container">
            <div class="boxes">
                <div class="box first">
                    <p class="main"><img src="img/car.jpg" alt="Car" /><br />
                    <span class="name">Revolution XL2343</span><br />
                    <span class="price">674 $</span>&nbsp;&nbsp;
                    <span class="number"> (DUB-2343)</span></p>
                </div>
                <div class="box">
                    <p class="main"><img src="img/helico.jpg" alt="Car" /><br />
                    <span class="name">Triceratops Plus</span><br />
                    <span class="price">3 425 $</span>&nbsp;&nbsp;
                    <span class="number">(ETST-12345)</span></p>
                </div>
                <div class="box">
                    <p class="main"><img src="img/avion.jpg" alt="Car" /><br />
                    <span class="name">Air Canada Ripp Off</span><br />
                    <span class="price">487 $</span>&nbsp;&nbsp;
                    <span class="number">(REV-0435)</span></p>
                </div>
                <div class="box first">
                    <p class="main"><img src="img/boat.jpg" alt="Car" /><br />
                    <span class="name">Bota Gaze Nitro Frigo</span><br />
                    <span class="price">14 $</span>&nbsp;&nbsp;
                    <span class="number">(DUB-3445)</span></p>
                </div>
                <div class="box">
                    <p class="main"><img src="img/sailboat.jpg" alt="Car" /><br />
                    <span class="name">Bota Ouelle Super Fix</span><br />
                    <span class="price">1 234 $</span>&nbsp;&nbsp;
                    <span class="number">(PINFE-94453)</span></p>
                </div>
                <div class="box">
                    <p class="main"><img src="img/boatengine.jpg" alt="Car" /><br />
                    <span class="name">Motor Bota Essence</span><br />
                    <span class="price">123 $</span>&nbsp;&nbsp;
                    <span class="number">(TROP-3245667)</span></p>
                </div>
                <div class="box first">
                    <p class="main"><img src="img/radio.jpg" alt="Car" /><br />
                    <span class="name">JPR Foutez-moi la paix</span><br />
                    <span class="price">348 $</span>&nbsp;&nbsp;
                    <span class="number">(STEG-87345)</span></p>
                </div>
                <div class="box">
                    <p class="main"><img src="img/carengine.jpg" alt="Car" /><br />
                    <span class="name">Car Motor Trend</span><br />
                    <span class="price">212 $</span>&nbsp;&nbsp;
                    <span class="number">(STO-18B)</span></p>
                </div>
                <div class="box">
                    <p class="main"><img src="img/truck.jpg" alt="Car" /><br />
                    <span class="name">Buggy Guppy Duddy</span><br />
                    <span class="price">235 $</span>&nbsp;&nbsp;
                    <span class="number">(MPX-3203)</span></p>
                </div>
            </div>
        </div>
    <div id="ft">
        <p id="footer">Les prix affichï¿½s sur cette annonce sont sujets ï¿½ changement sans prï¿½avis<br />Votre entreprise &copy; 2008<br />
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0 Strict" /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://jigsaw.w3.org/css-validator/">
        <img src="http://jigsaw.w3.org/css-validator/images/vcss"
            alt="CSS Valide !" />
    </a>
  </p>
    </div>
</div>
</body>