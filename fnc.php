
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