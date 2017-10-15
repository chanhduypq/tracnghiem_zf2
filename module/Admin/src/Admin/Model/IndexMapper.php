<?php
namespace Admin\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Session\Container;
class IndexMapper extends AbstractTableGateway 
{

    public $table = 'user';

    public function login($username, $password) 
    {

        try {
            $username = str_replace("'", "\'", $username);
            $result = $this->select("email='$username' AND password='" . sha1($password) . "'")->toArray();
        } catch (Exception $e) {
            
        }

        if (!is_array($result) || count($result) == 0) {
            return false;
        }
        $result = $result[0];
        if ($result['is_admin'] == '1') {
            $result['user'] = 'admin';
        }

        $session = new Container('base');
        $session->offsetSet('user', $result);

        return true;
    }

    public function loginAdmin($username, $password) 
    {
        try {
            $username = str_replace("'", "\'", $username);
            $result = $this->select("email='$username' AND password='" . sha1($password) . "' AND is_admin=1")->toArray();
        } catch (Exception $e) {
            
        }

        if (!is_array($result) || count($result) == 0) {
            return false;
        }
        $result = $result[0];
        $result['user'] = 'admin';

        $session = new Container('base');
        $session->offsetSet('user', $result);

        return true;
    }

    public function changePassword($username, $newPassword) 
    {
        $data = array();
        $data['password'] = sha1($newPassword);
        try {
            $username = str_replace("'", "\'", $username);
            $this->update($data, "email='$username'");

            $session = new Container('base');
            $identity = $session->offsetGet('user');
            $identity["password"] = sha1($newPassword);
            $session->offsetSet('user', $identity);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function signup($username, $password, $firstName, $lastName, $middleName) 
    {
        $user = array();
        $user['username'] = $username;
        $user['password'] = $password;
        $user['first_name'] = $firstName;
        $user['last_name'] = $lastName;
        $user['middle_name'] = $middleName;
        foreach ($user as $key => $value) {
            if ($value === '' || $value === null) {
                unset($user[$key]);
            }
        }

        try {
            $this->insert($user);
        } catch (Exception $e) {
            
        }
    }

    public function sendEmailByIdhoso($email, $password, $firstName, $lastName, $middleName, $username) 
    {
        require_once 'Zend/Mail.php';
        require_once 'Zend/Mail/Transport/Smtp.php';

        $smtpHost = 'smtp.gmail.com';
        $smtpConf = array(
            'auth' => 'login',
            'ssl' => 'ssl',
            'port' => '465',
            'username' => 'chanhduypq@gmail.com',
            'password' => '826498meyeu'
        );
        $transport = new Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

        //Create email
        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom('chanhduypq@gmail.com', 'Tony Caporicci');
        $mail->addTo($email, $firstName . ' ' . $middleName . ' ' . $lastName);
        $mail->setSubject('Hello ' . $firstName . ' ' . $middleName . ' ' . $lastName . '!');
        $mail->setBodyText('Your password is ' . $password);



        $sent = true;
        try {
            $mail->send($transport);
        } catch (Exception $e) {


            $sent = false;
        }

        return $sent;
    }

}
