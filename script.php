<?php
// Toggle logging on or off
$enableLogging = false; // Set to false to disable logging

// Define log file
$logFile = 'curl_log.txt';

function logMessage($message, $logFile) {
    if ($GLOBALS['enableLogging']) {
        file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);
    }
}

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $domain = filter_var($_POST["domain"], FILTER_SANITIZE_STRING);

    // API keys and secrets
    $godaddyApiKey = "GODADDY_API_KEY";
    $godaddyApiSecret = "GODADDY_API_SECRET";
    $namesiloApiKey = "NAMESILO_API_KEY";

    // Check Domain Availability with NameSilo
    $namesiloUrl = 'https://www.namesilo.com/api/checkRegisterAvailability?version=1&type=json&key=' . $namesiloApiKey . '&domains=' . urlencode($domain);

    $ch = curl_init($namesiloUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);
    $namesiloResponse = curl_exec($ch);
    $namesiloHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    logMessage("NameSilo Response HTTP Code: $namesiloHttpCode", $logFile);
    logMessage("NameSilo Response: $namesiloResponse", $logFile);

    $namesiloResult = json_decode($namesiloResponse, true);

    // Check if domain is available or not
    if ($namesiloHttpCode == 200 && isset($namesiloResult['reply']['unavailable']['domain']) && $namesiloResult['reply']['unavailable']['domain'] === $domain) {
        // Suggest similar domains using GoDaddy
        $godaddySuggestionsUrl = 'https://api.ote-godaddy.com/v1/domains/suggest?query=' . urlencode($domain) . '&limit=10';

        $ch = curl_init($godaddySuggestionsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: sso-key ' . $godaddyApiKey . ':' . $godaddyApiSecret,
            'Accept: application/json'
        ]);
        $godaddySuggestionsResponse = curl_exec($ch);
        $godaddySuggestionsHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        logMessage("GoDaddy Suggestions HTTP Code: $godaddySuggestionsHttpCode", $logFile);
        logMessage("GoDaddy Suggestions Response: $godaddySuggestionsResponse", $logFile);

        $suggestions = json_decode($godaddySuggestionsResponse, true);
        $availableDomains = [];

        foreach ($suggestions as $suggestion) {
            $suggestedDomain = $suggestion['domain'];
            // Only add suggestions; no need to check availability with NameSilo
            $availableDomains[] = $suggestedDomain;
        }

        echo json_encode([
            'status' => 'warning',
            'message' => "The domain <span style='color: #07e2ff;'>$domain</span> is not available. <br> Suggested similar domains:",
            'suggestions' => $availableDomains
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'message' => "The domain <span style='color: #ffffff;'>$domain</span> is available."
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => "Invalid request method."
    ]);
}
?>