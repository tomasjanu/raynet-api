<?php
require 'config.php';

// Základní nastavení
$username = 'tomas.janu@4fin.cz'; // Vaše uživatelské jméno
$apiKey = get_env_variable('API_KEY');
$instanceName = 'tomasjanuporadce'; // Název vaší instance

// Datum a čas do kdy byly úkoly naplánovány
$scheduledTill = date('Y-m-d H:i', strtotime('tomorrow') - 1);

// URL endpointu pro úkoly s filtrem pro neudělané úkoly naplánované do určitého data a času
$url = 'https://app.raynet.cz/api/v2/task/?scheduledTill[LE]=' . urlencode($scheduledTill) . '&status=SCHEDULED';

// Inicializace cURL
$ch = curl_init();

// Nastavení cURL možností
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $apiKey); // Autentizace
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Instance-Name: ' . $instanceName, // Přidání vlastní hlavičky
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Vrátí výsledek jako řetězec

// Provedení volání
$response = curl_exec($ch);

// Kontrola chyby
if(curl_errno($ch)){
    echo 'cURL error: ' . curl_error($ch);
}

// Ukončení session
curl_close($ch);

// Vytisknutí odpovědi
echo $response;