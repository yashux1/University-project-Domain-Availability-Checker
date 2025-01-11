# Domain Availability Checker

This PHP script checks the availability of a domain name using the NameSilo API.
If the domain is unavailable, it suggests alternative domains using the GoDaddy API. The script can be integrated into a web application or used as a stand-alone tool.

## Features:
- Check domain availability with NameSilo API.
- Suggest similar available domains with GoDaddy API if the domain is unavailable.
- Logging functionality for debugging.

## Installation

### Prerequisites:
- PHP 7.0 or later
- cURL extension for PHP enabled
- Access to the NameSilo and GoDaddy APIs (API keys required)

### Steps:

1. **Clone the repository or download the files**:

2. **Set up your API keys**:
- Create a .env file in the root directory of the project.
- Add the following lines to your .env file:
  
GODADDY_API_KEY=your-godaddy-api-key
  GODADDY_API_SECRET=your-godaddy-api-secret
  NAMESILO_API_KEY=your-namesilo-api-key


3. **Ensure your server supports PHP**:
- If you're running it locally, ensure you have a local server like XAMPP or WAMP.
- If you're deploying it online, ensure PHP is installed on your web hosting server.

4. **Upload the PHP file to your server** and ensure your server supports POST requests.

## API Key Setup

To use the script, you need to obtain API keys for the following services:

- **NameSilo API Key**:
  1. Go to [NameSilo API Documentation](https://www.namesilo.com/api).
  2. Sign up for an account if you don’t already have one.
  3. Generate your API key and use it in the .env file under the NAMESILO_API_KEY key.

- **GoDaddy API Key**:
  1. Go to [GoDaddy Developer Portal](https://developer.godaddy.com/keys).
  2. Create an account and generate API keys.
  3. Use the GoDaddy API key and secret in the .env file under GODADDY_API_KEY and GODADDY_API_SECRET.

## Configuration

The script has the following configurable options:

- **Logging**: You can enable or disable logging by setting the $enableLogging variable in the script:
php
$enableLogging = true;  // Set to false to disable logging

Log File: The default log file is curl_log.txt. You can change the log file location by modifying the $logFile variable:

$logFile = '/path/to/your/logfile.txt';

## Usage
To use the domain availability checker, send a POST request to the PHP script with a domain parameter.

Example Request:
URL: /check_domain.php
Method: POST
Parameters: domain (the domain name to check)
Example cURL Command:

curl -X POST -d "domain=example.com" https://yourdomain.com/check_domain.php

Example Response:
{
  "status": "success",
  "message": "The domain <span style='color: #ffffff;'>example.com</span> is available."
}

If the domain is unavailable, it will return similar domain suggestions:

{
  "status": "warning",
  "message": "The domain <span style='color: #07e2ff;'>example.com</span> is not available. <br> Suggested similar domains:",
  "suggestions": ["example.net", "example.org", "example.co"]
}

## Error Handling
The script returns the following status codes:

200 OK: The domain availability check was successful.
400 Bad Request: The domain parameter is missing or invalid.
500 Internal Server Error: An error occurred when contacting the NameSilo or GoDaddy API.
In case of an API failure, the script will return an error message with details:

{
  "status": "error",
  "message": "Error contacting NameSilo API."
}

## Logging
If logging is enabled, all API requests and responses are saved to a log file (curl_log.txt by default). Logs can be useful for debugging API issues. To disable logging, set the $enableLogging variable to false in the script.

Example log entry:

2025-01-11 12:00:00 - NameSilo Response HTTP Code: 200
NameSilo Response: {"reply":{"unavailable":{"domain":"example.com"}}}
