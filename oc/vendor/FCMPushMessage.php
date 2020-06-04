<?php
/*
    Class to send push notifications using Google Cloud Messaging for Android

    Example usage
    -----------------------
    $an = new FCMPushMessage($apiKey);
    $an->setDevice($device);
    $response = $an->send($message);
    -----------------------

    $apiKey Your FCM api key
    $device A string of registered device tokens
    $message The mesasge you want to push out

    @author Matt Grundy

    Adapted from the code available at:
    http://stackoverflow.com/questions/11242743/gcm-with-php-google-cloud-messaging

*/
class FCMPushMessage {

    // the URL of the FCM API endpoint
    private $url = 'https://fcm.googleapis.com/fcm/send';
    // the server API key - setup on class init
    private $serverApiKey = "";
    // device to send to
    private $device;

    /*
        Constructor
        @param $apiKeyIn the server API key
    */
    function FCMPushMessage($apiKeyIn){
        $this->serverApiKey = $apiKeyIn;
    }

    /*
        Set the device to send to
        @param $deviceIds array of device tokens to send to
    */
    function setDevice($device){
        $this->device = $device;
    }

    /*
        Send the message to the device
        @param $title The title to send
        @param $message The message to send
        @param $data Array of data to accompany the notification
    */
    function send($title, $body, $data = false){

        if(empty($this->device)){
            throw new FCMPushMessageArgumentException("No device set");
        }

        if(strlen($this->serverApiKey) < 8){
            throw new FCMPushMessageArgumentException("Server API Key not set");
        }

        $fields = array(
            'to'  => $this->device,
            'notification'      => array( "title" => $title, "body" => $body, 'sound' => 'default' ),
        );

        if(is_array($data)){
            foreach ($data as $key => $value) {
                $fields['data'][$key] = $value;
            }
        }

        $headers = array(
            'Authorization: key=' . $this->serverApiKey,
            'Content-Type: application/json'
        );

        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $this->url );

        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Avoids problem with https certificate
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);

        // Execute post
        $result = curl_exec($ch);

        // Close connection
        curl_close($ch);

        return $result;
    }

}

class FCMPushMessageArgumentException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
