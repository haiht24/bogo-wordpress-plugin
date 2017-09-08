<?php
/**
 * Plugin Name: Ym9nbyB3YXRjaGVyIGJ5IGhhaWh0
 * Plugin URI:
 * Description:
 * Version: 0.1
 * Author: HaiHT
 * Author URI:
 * License:
 */

date_default_timezone_set('America/Los_Angeles');
require_once __DIR__ . '/vendor/autoload.php';
//include('/quickstart.php');

/* HTML */
add_action( 'all_admin_notices', 'my_custom_html' );
function my_custom_html(){
    $currentUser = wp_get_current_user()->user_email;
    echo "<input type='hidden' value='$currentUser' id='_currentUserEmail'>";
}

/* Script */
add_action( 'admin_enqueue_scripts', 'changingAffiliateUrl' );
function changingAffiliateUrl( $hook ) {
    $screen = get_current_screen();
//    echo '<p style="margin-left: 20%;">';
//    var_dump($hook,$screen->post_type);
//    echo '</p>';
    // Edit store
    if ( $hook == 'term.php' && $screen->post_type === 'coupon' ) {
        wp_enqueue_script( 'bogo_custom_script', plugin_dir_url( __FILE__ ) . 'script.js' );
    }
    if ( $hook == 'post.php' && $screen->post_type === 'coupon' ) {
        wp_enqueue_script( 'bogo_custom_script', plugin_dir_url( __FILE__ ) . 'script-2.js' );
    }

}

/* Action send mail */
// no require login
//add_action( 'wp_ajax_nopriv_alert_email', 'alert_email' );
// require login
add_action( 'wp_ajax_alert_email', 'alert_email' );

function alert_email() {
    if($_POST['action'] === 'alert_email'){
        $params = $_POST['params'];
        $editUrl = !empty($params['editUrl']) ? $params['editUrl'] : '#';
        $type = !empty($params['type']) ? $params['type'] : 'undefined';
        $first = $params['first'];
        $last = $params['last'];
        $storeName = $params['storeName'];
        $currentUser = wp_get_current_user()->user_email;
        $date = date("Y-m-d H:i:s", strtotime('+7 hours'));
        $mailDriver = strpos(site_url(), 'bestmattresstoday.com') === false ? 'wordpress' : '';
        sendGmail($mailDriver, $first, $last, $storeName, $currentUser, $date, $type, $editUrl);
        die;
    }else{
        die('not working');
    }
}

date_default_timezone_set('America/Los_Angeles');
require_once __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Gmail API PHP Quickstart');
define('CREDENTIALS_PATH', '~/.credentials/gmail-php-quickstart.json');
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
    $credentialsPath = '/var/www/html/gmail-php-quickstart.json';
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
 * Filter the mail content type.
 */
function wpdocs_set_html_mail_content_type() {
    return 'text/html';
}

function sendGmail($mailDriver, $first, $last, $storeName, $currentUser, $date, $type, $editUrl) {
    $subject = site_url() . ': Affiliate Url Changed';
    if($mailDriver === 'wordpress'){
        add_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );

        $arrTo = [
            'haiht369@gmail.com',
            'hoanglx@me.com',
            'toanty.kt@gmail.com'
        ];
        $msg = '';
        $msg .= "<p>Hey man,</p>\r\n";
        $msg .= "<p>Affiliate Url of $type <b><a href='$editUrl' target='_blank'>$storeName</a></b> has been changed by $currentUser</p>\r\n";
        $msg .= "<p>Time: $date</p>\r\n";
        $msg .= "<p>Url before change: $first</p>\r\n";
        $msg .= "<p>Url after change: $last</p>\r\n";
        $result = wp_mail($arrTo, $subject, $msg);
        remove_filter( 'wp_mail_content_type', 'wpdocs_set_html_mail_content_type' );
    }else {
        $arrReceipt = [
            'HoangLx <hoanglx@me.com>',
            'HaiHT <haiht369@gmail.com>',
            'ToanTy <toanty.kt@gmail.com>'
        ];
        $sendTo = implode(',', $arrReceipt);
        // Get the API client and construct the service object.
        $client = getClient();
        $service = new Google_Service_Gmail($client);

        $user = 'me';
        $strSubject = $subject;
        $strRawMessage = "From: Watcher<dev.mccorp@gmail.com>\r\n";
        $strRawMessage .= "To: $sendTo\r\n";
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
}