<?php

namespace elis\presenter;

use elis\controller\Router;
use elis\utils;

/**
 * Dispatcher administration presenter
 * @version 0.0.1 210610 created
 */
abstract class Dispatcher extends Main
{

    /**
     * Dispatcher template
     *
     * @var utils\Template
     */
    protected $dspTmplt;

    public function __construct(array $params)
    {
        parent::__construct($params);
        if (!$this->user->isInRole(['ADM', 'DSP'])) {
            Router::redirect("error/401");
        }
        $this->dspTmplt = new utils\Template("page/administration.html");
        new utils\Template("dsp/menu.html");
        $menuItem = new utils\Template("other/menu-item.html");
        $menuItems = "";
        foreach ([
            "" => "home",
            "dsp-dashboard" => "dashboard",
            "dsp-route" => "routes",
            "dsp-package" => "packages",
            "dsp-event" => "events",
            "dsp-drivers" => "drivers"
        ] as $href => $label) {
            $menuItem->clearData()->setAllData([
                'href' => $href,
                'label' => $label,
                'active' => $this->getParam(0) == $href ? 'active' : ''
            ]);
            $menuItems .= $menuItem;
        }
        $this->dspTmplt->setData('menu', new utils\Template("other/menu.html", [
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
                $this->dspTmplt->addData('content', new utils\Template("other/message.html", [
                    'message' => 'Bad parameter',
                    'type' => 'err'
                ]));
                $this->table();
            }
        }
        $this->pageTmplt->setData('main', $this->dspTmplt);
        echo $this->pageTmplt;
    }

    protected abstract function table();
}
