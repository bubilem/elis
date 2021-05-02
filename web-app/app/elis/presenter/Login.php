<?php

namespace elis\presenter;

use elis\utils;

/**
 * Login presenter
 * @version 0.0.1 210502 created
 */
class Login extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    public function run()
    {
        $this->pageTmplt->setData('title', 'Login');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        if ($email) {
            $messTmpl = new utils\Template("other/message.html");
            if ($this->user->login($email, filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING))) {
                $messTmpl->setAllData([
                    'type' => 'suc',
                    'message' => 'User has been logged.'
                ]);
                $this->pageTmplt->addData('main', $messTmpl);
                $this->pageTmplt->setData('user', $this->headerUserLoginLogoutLink());
                echo $this->pageTmplt;
                return;
            } else {
                $messTmpl->setAllData([
                    'type' => 'err',
                    'message' => 'User has not been logged. Bad email or password.'
                ]);
                $this->pageTmplt->addData('main', $messTmpl);
            }
        }
        $tmplt = new utils\Template("page/login.html");
        $tmplt->setData('email', filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $this->pageTmplt->addData('main', $tmplt);
        echo $this->pageTmplt;
    }
}
