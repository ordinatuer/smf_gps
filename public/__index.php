<?php

//phpinfo();

print "<pre>";
try {
    $dbh = new PDO('pgsql:host=172.20.0.2;dbname=smf_gps_db', "smf_gps_user", "smfgpspassword");
    foreach($dbh->query('SELECT * from names') as $row) {
        print_r($row);
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
print "</pre>";
?>