<?php

namespace elis\presenter;

use elis\utils;

/**
 * Main administration presenter
 * @version 0.0.1 201223 created
 */
abstract class Administration extends Main
{

    /**
     * Main page template
     *
     * @var utils\Template
     */
    protected $tmplt;

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->tmplt = new utils\Template("adm/administration.html");
        $this->tmplt->setData('lang', utils\Conf::get("DEF_LANG"));
        $this->tmplt->setData('base', utils\Conf::get("URL_BASE") . utils\Conf::get("URL_DIR"));
    }

    /**
     * Main run method
     *
     * @return void
     */
    public function run()
    {
        if (empty($this->getParam(0))) {
            $this->table();
        } else {
            $methodName = utils\Str::toCamelCase($this->getParam(0), "-", true);
            if (method_exists($this, $methodName)) {
                $this->$methodName();
            } else {
                $this->tmplt->addData('content', new utils\Template("adm/message.html", [
                    'message' => 'Bad parameter',
                    'type' => 'err'
                ]));
                $this->table();
            }
        }
        echo $this->tmplt;
    }

    protected abstract function newForm($model = null);

    protected abstract function new();

    protected abstract function editForm($model = null);

    protected abstract function edit();

    protected abstract function deleteQuestion();

    protected abstract function delete();

    protected abstract function table();
}
