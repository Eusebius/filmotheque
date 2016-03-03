<?php
require_once('../includes/declarations.inc.php');
require_once('../includes/initialization.inc.php');
Auth::ensurePermission('admin');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script type="text/javascript" src="test.js"></script>
        <script type="text/javascript">
            window.onload = function () {
                initAutoComplete(document.getElementById('form-test'),
                        document.getElementById('champ-texte'), document.getElementById('bouton-submit'))
            };
        </script>
        <title>AJAX Autocompletion test</title>
    </head>
    <body>
        <form name="form-test" id="form-test"
              action="javascript:alert('soumission de ' + document.getElementById('champ-texte').value)"
              style="margin-left: 50px; margin-top:20px">
            <input type="text" name="champ-texte" id="champ-texte" size="20" />
            <input type="submit" id="bouton-submit">
        </form>
        
        <p><?php echo (int) "0123abc"; ?></p>
        <p><?php echo 3 + "0123abc"; ?></p>
    </body>
</html>
