<?php

namespace elis\presenter;

use elis\controller\Router;
use elis\utils;

/**
 * Main administration presenter
 * @version 0.0.1 201223 created
 */
abstract class Administration extends Main
{

    /**
     * Administration template
     *
     * @var utils\Template
     */
    protected $adminTmplt;

    public function __construct(array $params)
    {
        parent::__construct($params);
        if (!$this->user->isInRole('ADM')) {
            Router::redirect("error/401");
            //Router::redirect("login");
        }
        $this->adminTmplt = new utils\Template("adm/administration.html");
        new utils\Template("adm/menu.html");
        $menuItem = new utils\Template("other/menu-item.html");
        $menuItems = "";
        foreach ([
            "adm-dashboard" => "dashboard",
            "adm-user" => "users",
            "adm-vehicle" => "vehicles",
            "adm-place" => "places"
        ] as $href => $label) {
            $menuItem->clearData()->setAllData([
                'href' => $href,
                'label' => $label,
                'active' => $this->getParam(0) == $href ? 'active' : ''
            ]);
            $menuItems .= $menuItem;
        }
        $this->adminTmplt->setData('menu', new utils\Template("other/menu.html", [
            'menu-items' => $menuItems
        ]));
    }

    /**
     * Main run method
     *
     * @return void
     */
    public function run()
    {
        if (empty($this->getParam(1))) {
            $this->table();
        } else {
            $methodName = utils\Str::toCamelCase($this->getParam(1), "-", true);
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                $this->adminTmplt->addData('content', new utils\Template("other/message.html", [
                    'message' => 'Bad parameter',
                    'type' => 'err'
                ]));
                $this->table();
            }
        }
        $this->pageTmplt->setData('main', $this->adminTmplt);
        echo $this->pageTmplt;
    }

    protected abstract function newForm($model = null);

    protected abstract function new();

    protected abstract function editForm($model = null);

    protected abstract function edit();

    protected abstract function deleteQuestion();

    protected abstract function delete();

    protected abstract function table();
}
