<?php
date_default_timezone_set('America/Los_Angeles');
require_once __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Gmail API PHP Quickstart');
define('CREDENTIALS_PATH', '/var/www/html/gmail-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');

// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/gmail-php-quickstart.json
define('SCOPES', implode(' ', [
    Google_Service_Gmail::GMAIL_SEND,
    Google_Service_Gmail::GMAIL_COMPOSE,
    Google_Service_Gmail::MAIL_GOOGLE_COM,
    Google_Service_Gmail::GMAIL_MODIFY
]
));

if (php_sapi_name() != 'cli') {
    throw new Exception('This application must be run on the command line.');
}

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->setScopes(SCOPES);
    $client->setAuthConfig(CLIENT_SECRET_PATH);
    $client->setAccessType('offline');

    // Load previously authorized credentials from a file.
    $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
    if (file_exists($credentialsPath)) {
        $accessToken = json_decode(file_get_contents($credentialsPath), true);
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        $authCode = trim(fgets(STDIN));

        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if(!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        printf("Credentials saved to %s\n", $credentialsPath);
    }
    $client->setAccessToken($accessToken);

    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
        $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
    }
    return str_replace('~', realpath($homeDirectory), $path);
}

function sendGmail($first, $last, $storeName, $currentUser, $date, $type, $editUrl) {
    // Get the API client and construct the service object.
    $client = getClient();
    $service = new Google_Service_Gmail($client);

    $user = 'me';
    $strSubject = 'Affiliate Url Changed' . date('M d, Y h:i:s A');
    $strRawMessage = "From: Watcher<dev.mccorp@gmail.com>\r\n";
    $strRawMessage .= "To: Listener <dev.mccorp@gmail.com>\r\n";
    $strRawMessage .= 'Subject: =?utf-8?B?' . base64_encode($strSubject) . "?=\r\n";
    $strRawMessage .= "MIME-Version: 1.0\r\n";
    $strRawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
    $strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
    $strRawMessage .= "<p>Hey man,</p>\r\n";
    $strRawMessage .= "<p>Affiliate Url of $type <b><a href='$editUrl' target='_blank'>$storeName</a></b> has been changed by $currentUser</p>\r\n";
    $strRawMessage .= "<p>Time: $date</p>\r\n";
    $strRawMessage .= "<p>Url before change: $first</p>\r\n";
    $strRawMessage .= "<p>Url after change: $last</p>\r\n";

    // The message needs to be encoded in Base64URL
    $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
    $msg = new Google_Service_Gmail_Message();
    $msg->setRaw($mime);
    //The special value **me** can be used to indicate the authenticated user.
    $result = $service->users_messages->send($user, $msg);
}

