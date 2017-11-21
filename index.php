<?php
    session_start();

    // Mot de passe de connexion
    $password = '';

    // Clé de chiffrement (16, 24 ou 32 caractères)
    $crypt_key = '';

    // Couleurs d'affichage
    $color_bar = '#1F3A93';
    $color_fg = 'black';
    $color_bg = 'white';

    /*
    FunkyNotes
    © Copyright 2017 Maya Friedrichs


    FunkyNotes est un logiciel libre : vous pouvez le redistribuer et/ou le
    modifier selon les termes de la Licence Publique Générale GNU publiée
    par la Free Software Foundation, soit la version 3 de la license, soit
    (à votre discrétion) toute version ultérieure.

    FunkyNotes est distribué dans l'espoir qu'il puisse vous être utile,
    mais SANS AUCUNE GARANTIE ; sans même de garantie de VALEUR MARCHANDE
    ou d'ADÉQUATION A UN BESOIN PARTICULIER. Consultez la Licence Publique
    Générale GNU pour plus de détails.

    Vous devez avoir reçu une copie de la Licence Publique Générale GNU en
    même temps que FunkyNotes ; si ce n'est pas le cas, consultez
    <http://www.gnu.org/licenses/>.


    FunkyNotes is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    FunkyNotes is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with FunkyNotes.  If not, see <http://www.gnu.org/licenses/>. 
    */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>FunkyNotes</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <style>
        body {
            margin: 0;
            background: <?php echo $color_bg; ?>;
        }

        body, input, button {
            font-family: "Segoe UI", Helvetica, sans-serif;
        }

        a, button {
            cursor: pointer;
        }

        p {
            margin: 0;
            padding: 0.5em;
        }

        nav, footer, form {
            padding: 1em;
            box-sizing: border-box;
        }

        nav, footer {
            width: 14em;
            position: absolute;
            top: 0;
            bottom: 0;
            background: <?php echo $color_bar; ?>;
            color: <?php echo $color_light; ?>;
        }

        footer {
            top: initial;
        }

        nav, footer, a {
            color: <?php echo $color_bg; ?>;
        }

        form {
            margin-left: 14em;
            max-width: 50em;
        }

        button, input, textarea {
            border-radius: 0.1rem;
            border-width: 0;
            box-sizing: border-box;
            padding: 0.5rem;
        }

        textarea, input {
            background: <?php echo $color_bg; ?>;
            color: <?php echo $color_fg; ?>;
            border: solid 1pt <?php echo $color_bar; ?>;
        }

        button {
            font-size: 1em;
            background: <?php echo $color_bar; ?>;
            color: <?php echo $color_bg; ?>;
        }

        input[type=text] {
            width: 100%;
            font-size: 1.5em;
        }

        textarea {
            width: 100%;
            font-size: 1.25em;
            height: calc( 100vh - 12rem);
            font-family: monospace;
        }

        @media screen
        and (orientation: portrait),
        (max-device-width: 1000px) {
            nav, footer, form {
                width: 100%;
                position: relative;
                margin-left: unset;
            }
            textarea {
                height: 60vh;
            }
        }
    </style>
    <script>
        function deleteNote(note){
            if(confirm("Delete note "+note+" ?")) {
                window.location = "index.php?delete="+note;
            }
        }
        function enableTab() {
            document.getElementsByTagName('textarea')[0].onkeydown=function(e){
                if(e.keyCode==9 || e.which==9 || e.key==9){
                    e.preventDefault();
                    this.value = this.value.substring(0,this.selectionStart)
                    + "\t"
                    + this.value.substring(this.selectionEnd);
                    this.selectionEnd = s+1; 
                }
            }
        }
    </script>
    <body>

<?php
    // Test de la configuration

    if($password == '' || $crypt_key == '') {
        echo '<p>Please configure password and crypt_key variables
        in this file.</p>';
        exit();
    }

    // Connexion, déconnexion

    if(isset($_GET['logout'])) {
        session_unset();
        session_destroy();
    }

    if(isset($_POST['pass']) && $_POST['pass'] == $password)
        $_SESSION['logged'] = true;

    if(!isset($_SESSION['logged'])) {
        echo '
        <nav></nav>
        <form action="index.php" method="POST">
            <p><input type="password" placeholder="Password" name="pass"></p>
            <p><button>Login</button></p>
        </form>';
        if(isset($_POST['pass']))
            echo '<p>Wrong password. :(</p>';
        exit();
    }

    // Liste des notes

    echo '<nav>';
    echo '<p><a href="index.php?logout">Logout</a></p>';
    echo '<p><a href="index.php?new">New note</a></p>';

    $liste_notes = scandir('.', SCANDIR_SORT_DESCENDING);
    foreach($liste_notes as $note) {
        if(is_file($note) && $note[0] != '.' && $note != 'index.php') {
            echo '
            <p>
                <a href="index.php?note='.$note.'">'.$note.'</a>
            </p>';
        }
    }

    echo '</nav>';

    // Suppression d'une note

    if(isset($_GET['delete'])) {
        unlink(nomProtege($_GET['delete']));
        echo '<script>window.location = "index.php";</script>';
    }

    // Affichage d'une note

    function nomProtege($nom) {
        $nom = str_replace('/', '⁄', $nom);
        $nom = str_replace('\\', '⁄', $nom);
        $nom = str_replace("\0", '', $nom);
        $nom = str_replace(':', '∶', $nom);
        $nom = str_replace('*', '⁕', $nom);
        $nom = str_replace('?', '⁈', $nom);
        $nom = str_replace('"', '‟', $nom);
        $nom = str_replace('<', '‹', $nom);
        $nom = str_replace('>', '›', $nom);
        $nom = str_replace('|', '❘', $nom);
        $nom = str_replace('&', '⅋', $nom);
        return trim($nom);
    }

    if(isset($_GET['note']) || isset($_GET['new'])) {
        $nom_fichier = '';
        $contenu_note = '';
        if(isset($_GET['note'])) {
            $nom_fichier = nomProtege($_GET['note']);
            $contenu_note = file_get_contents($nom_fichier);
            $contenu_note = trim(mcrypt_decrypt('rijndael-128', $crypt_key,
                               $contenu_note, 'ecb'), "\0");
        }
        echo '
        <form action="index.php" method="POST">
            <input type="hidden" name="nom_ancien" value="'.$nom_fichier.'">
            <p><input type="text" name="nom" value="'.$nom_fichier.'"></p>
            <p><textarea name="contenu">'.$contenu_note.'</textarea></p>
            <p>';
        if($nom_fichier != '')
            echo '
                <button type="button"
                        onclick="deleteNote(\''.$nom_fichier.'\')">
                    Delete</button>';
        echo '
                <button><strong>Save</strong></button>
            </p>
            <script>enableTab();</script>
        </form>';
    }

    // Ajout ou mise à jour d'une note

    if(isset($_POST['nom']) && isset($_POST['contenu'])) {
        $nom_fichier = nomProtege($_POST['nom']);
        $nom_fichier_ancien = nomProtege($_POST['nom_ancien']);
        if($nom_fichier == '')
            $nom_fichier = "New note";
        if($nom_fichier != $nom_fichier_ancien) {
            while(is_file($nom_fichier))
                $nom_fichier = $nom_fichier.'°';
            if($nom_fichier_ancien != '')
                rename($nom_fichier_ancien, $nom_fichier);
        }
        $contenu_fichier = mcrypt_encrypt('rijndael-128', $crypt_key,
                                          $_POST['contenu'], 'ecb');
        file_put_contents($nom_fichier, $contenu_fichier);
        echo '
        <script>
            window.location = "index.php?note='.$nom_fichier.'";
        </script>';
    }

?>

    <footer>
        <p>
            FunkyNotes by
            <a href="http://hyakosm.net" target="_blank">
            hyakosm</a>, a
            <a href="https://www.gnu.org/licenses/gpl.txt" target="_blank">
            GNU GPL</a>
            web app.
        </p>
    </footer>
    </body>
</html>
