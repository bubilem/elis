<?php

namespace elis\presenter;

use elis\model;
use elis\utils;

/**
 * Main presenter
 * @version 0.1.0 210601 created
 */
abstract class Main
{

    /**
     * Undocumented variable
     *
     * @var model\User
     */
    protected $user;

    /**
     * Uri params
     *
     * @var array
     */
    protected $params;

    /**
     * Page Main Temaplate
     *
     * @param utils\Template $pageTmplt
     */
    protected $pageTmplt;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
        $this->user = new model\User();
        $this->user->retainOrLogout($this);
        $this->pageTmplt = new utils\Template("page.html");
        $this->pageTmplt->setData('lang', utils\Conf::get("DEF_LANG"));
        $this->pageTmplt->setData('base', utils\Conf::get("URL_BASE") . utils\Conf::get("URL_DIR"));
        $this->pageTmplt->setData('version', utils\Conf::get("VER"));
        $this->pageTmplt->setData('user', $this->headerUserLoginLogoutLink());
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (utils\db\MySQL::isConnected()) {
            utils\db\MySQL::close();
        }
    }

    /**
     * Get param value by index
     *
     * @param int $index
     * @return mixed string on success, otherwise false
     */
    public function getParam(int $index)
    {
        return isset($this->params[$index]) ? $this->params[$index] : false;
    }

    public function headerUserLoginLogoutLink(): string
    {
        $linkTmpl = new utils\Template("other/link.html");
        if ($this->user->empty()) {
            $linkTmpl->setAllData([
                'href' => 'login',
                'label' => 'login'
            ]);
            $out = '';
        } else {
            $linkTmpl->setAllData([
                'href' => 'logout',
                'label' => 'logout'
            ]);
            $out = $this->user->getName() . ' ' . $this->user->getSurname() . ' ';
        }
        return $out . strval($linkTmpl);
    }
}
