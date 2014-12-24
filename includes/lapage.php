<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php echo $page->insererTousLesCss()?>
    <?php echo $page->insererTousLesJs()?>
    <title><?php $page->afficheLeTitre()?></title>
</head>
<body>
<input id="dernierChangementIci" type="hidden" name="heureDateLecture" value="<?php echo time()?>"></input>
<?php echo $page->afficheLeContenu()?>
</body>
</html>