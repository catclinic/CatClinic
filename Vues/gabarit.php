<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="/Ressources/Public/css/main.css" />
    <title>Cat Clinic - Console de gestion</title>
</head>
<body>
    <div id="header"><?php Vue::montrer('standard/entete'); ?></div>
    <div id="body">
       <?php echo $A_vue['body'] ?>
    </div>
    <div id="footer"><?php Vue::montrer('standard/pied'); ?></div>
</body>
</html>