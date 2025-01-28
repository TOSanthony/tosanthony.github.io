<?php
// Initialiser cURL
$ch = curl_init();

// Définir l'URL de la page Chronopost
$url = 'https://www.chronopost.fr/fr/surcharge-carburant';

// Définir les options de la requête cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Retourner le résultat sous forme de chaîne
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // Suivre les redirections
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0'); // Simuler un navigateur (parfois nécessaire)

// Exécuter la requête cURL et récupérer la réponse
$html = curl_exec($ch);

// Vérifier si une erreur s'est produite lors de la requête
if(curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
    exit;
}

// Fermer la session cURL
curl_close($ch);

// Charger le HTML dans DOMDocument
$dom = new DOMDocument();
libxml_use_internal_errors(true); // Pour éviter les erreurs de parsing sur du HTML mal formé
$dom->loadHTML($html);
libxml_clear_errors();

// Trouver la première table
$table = $dom->getElementsByTagName('table')->item(0);

// Trouver toutes les lignes (tr) de la table
$rows = $table->getElementsByTagName('tr');

// Variables pour stocker les pourcentages
$routier_percentage = '';
$aerien_percentage = '';

// Parcourir les lignes pour trouver les pourcentages
foreach ($rows as $row) {
    $cells = $row->getElementsByTagName('td');
    
    if ($cells->length > 0) {
        // Vérifier le type de service et récupérer les pourcentages
        $service = trim($cells->item(0)->nodeValue);
        if (strpos($service, 'Routier') !== false) {
            $routier_percentage = trim($cells->item(2)->nodeValue);  // Pourcentage routier
        }
        if (strpos($service, 'Aérien') !== false) {
            $aerien_percentage = trim($cells->item(2)->nodeValue);  // Pourcentage aérien
        }
    }
}

// Renvoi de la réponse sous forme de JSON
echo json_encode([
    'routier_percentage' => $routier_percentage,
    'aerien_percentage' => $aerien_percentage
]);
?>
