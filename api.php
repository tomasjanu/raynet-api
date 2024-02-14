<?php

header('Content-Type: application/json');

require 'config.php';
require 'fnc.php';

$type = isset($_GET['type']) ? $_GET['type'] : 'dueTasks';

// Základní nastavení
$username = 'tomas.janu@4fin.cz'; // Vaše uživatelské jméno
$apiKey = get_env_variable('API_KEY');
$instanceName = 'tomasjanuporadce'; // Název vaší instance

if($type === null)
{
    echo 'Invalid type of request.';
    exit;
}

// Vytisknutí odpovědi
$result = getResult($type);
echo $result;

// if (isset($_SERVER['HTTP_JMENO'], $_SERVER['HTTP_PRIJMENI'])) {
//     $jmenoValue = $_SERVER['HTTP_JMENO'];
//     $prijmeniValue = $_SERVER['HTTP_PRIJMENI'];
//     $headerArray = array("jmeno" => $jmenoValue, "prijmeni" => $prijmeniValue);
//     header('Content-Type: application/json');
//     echo json_encode($headerArray);
// }

function getResult($type)
{
    if($type === null)
    {
        return null;
    }

    switch($type)
    {
        case 'dueTasks':
            // Datum a čas do kdy byly úkoly naplánovány
            $scheduledTill = date('Y-m-d H:i', strtotime('tomorrow') - 1);
            $url = 'https://app.raynet.cz/api/v2/task/?scheduledTill[LE]=' . urlencode($scheduledTill) . '&status=SCHEDULED';

            $apiResult = getRaynetApiResult($url);
            
            sendToDiscord(getTasks($apiResult), '4fin');

            return getTasks($apiResult, 'json');

        default:
            return null;
    }
}

function getTasks($result, $type = 'string')
{
    $tasks = json_decode($result, true);

    // Initialize an empty array for messages
    $messages = array();

    // Loop through the data array
    foreach ($tasks['data'] as $item) {
        // Access the title and company name
        $title = $item['title'];
        $companyName = $item['company']['name'];
        $scheduledTill = $item['scheduledTill'];

        // Create a message object and add it to the messages array
        $messageObject = new stdClass();
        $messageObject->message = "($companyName) $title - do: $scheduledTill";
        $messages[] = $messageObject;
    }

    // If type is 'json', convert the messages to a JSON string
    if ($type == 'json') {
        return json_encode($messages);
    }

    // Otherwise, return the messages as a string
    return implode("\n", array_map(function($messageObject) {
        return $messageObject->message;
    }, $messages));
}

function getRaynetApiResult($url)
{
    GLOBAL $username, $apiKey, $instanceName;

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

    return $response;
}
