
<?php

function sendToDiscord($message, $channel = null) 
{
     // treat if input is array
     if($message != null && is_array($message) && count($message) > 0)
     {
          $input = implode("\n", $message);
     }
     else
     {
          $input = $message;
     }
   
     if ($input) {
          switch($channel):
               case "4fin":
                    $webhookURL = get_env_variable('DISCORD_WEBHOOK_URL_TASKS');
                    break;

               default:
               $webhookURL = get_env_variable('DISCORD_WEBHOOK_URL_TASKS');
                    break;
          endswitch;

         // Create the payload for the Discord webhook
         $payload = array(
             "content" => $input,
         );
   
         // Send the HTTP POST request to the webhook
         $ch = curl_init($webhookURL);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $response = curl_exec($ch);
         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
         curl_close($ch);   

         // Check for errors in the HTTP POST request
         if ($response === false) {
             error_log("Failed to send message to Discord: " . curl_error($ch));
         } else if ($httpCode < 200 || $httpCode >= 300) {
             error_log("Failed to send message to Discord: HTTP status code " . $httpCode);
         }
     }
}

function getRaynetApiResult($url)
{
    $username = get_env_variable('RAYNET_EMAIL');
    $apiKey = get_env_variable('RAYNET_API_KEY');
    $instanceName = get_env_variable('RAYNET_WORKSPACE_NAME');

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