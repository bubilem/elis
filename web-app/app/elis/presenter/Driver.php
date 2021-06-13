<?php

namespace elis\presenter;

use elis\controller\Router;
use elis\utils;

/**
 * Driver administration presenter
 * @version 0.1.3 210613 created
 */
abstract class Driver extends Main
{

    /**
     * Driver template
     *
     * @var utils\Template
     */
    protected $drvTmplt;

    public function __construct(array $params)
    {
        parent::__construct($params);
        if (!$this->user->isInRole(['DRV', 'CDR', 'ADM'])) {
            Router::redirect("error/401");
        }
        $this->drvTmplt = new utils\Template("page/administration.html");
        new utils\Template("drv/menu.html");
        $menuItem = new utils\Template("other/menu-item.html");
        $menuItems = "";
        foreach ([
            "" => "home",
            "drv-dashboard" => "dashboard",
            "drv-event" => "events"
        ] as $href => $label) {
            $menuItem->clearData()->setAllData([
                'href' => $href,
                'label' => $label,
                'active' => $this->getParam(0) == $href ? 'active' : ''
            ]);
            $menuItems .= $menuItem;
        }
        $this->drvTmplt->setData('menu', new utils\Template("other/menu.html", [
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
                $this->drvTmplt->addData('content', new utils\Template("other/message.html", [
                    'message' => 'Bad parameter',
                    'type' => 'err'
                ]));
                $this->table();
            }
        }
        $this->pageTmplt->setData('main', $this->drvTmplt);
        echo $this->pageTmplt;
    }

    protected abstract function table();
}
