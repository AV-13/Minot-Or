<?php
require 'vendor/autoload.php';

$uri = 'mongodb+srv://moussaclementaugustincda:z060v6yhbdRZPqYs@clusterminotor.wrfw69d.mongodb.net/?retryWrites=true&w=majority&appName=ClusterMinotor';
$client = new MongoDB\Client($uri);

try {
    $dbs = $client->listDatabases();
    echo "Connexion réussie à MongoDB.\n";
    foreach ($dbs as $db) {
        echo "- " . $db->getName() . "\n";
    }
} catch (Exception $e) {
    echo "Erreur de connexion : " . $e->getMessage() . "\n";
}
