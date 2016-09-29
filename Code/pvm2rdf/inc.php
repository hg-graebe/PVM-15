<?php

  /* Kopiere diese Datei nach inc.php und adjustiere die Zugangsdaten zu den
     Datenbanken. */

function getConnection() {
    $dbh = new PDO('mysql:host=localhost;dbname=pruef;charset=utf8', "graebe","");
    return $dbh;
}

function selectQuery($query) {
    if (defined( 'ABSPATH' )) { // within WP
        global $wpdb;
        return $wpdb->get_results($query, ARRAY_A);
    } else {
        $dbh=getConnection(); 
        return $dbh->query($query);
    }
}

function display($out) {
    if (defined( 'ABSPATH' )) { // within WP
        return '<pre>'.htmlentities($out).'</pre>';
    } else {
        return $out;
    }
}



?>
