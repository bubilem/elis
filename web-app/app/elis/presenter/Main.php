<?php

namespace elis\presenter;

use elis\utils\db;

/**
 * Main presenter
 * @version 0.0.1 201121 created
 */
abstract class Main
{

    /**
     * Uri params
     *
     * @var array
     */
    protected $params;

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (db\MySQL::isConnected()) {
            db\MySQL::close();
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

    /**
     * Main run method
     *
     * @return void
     */
    public function run()
    {
        switch ($this->getParam(0)) {
            case 'new-form':
                $this->newForm();
                break;
            case 'new':
                $this->new();
                break;
            case 'edit-form':
                $this->editForm();
                break;
            case 'edit':
                $this->edit();
                break;
            case 'delete-question':
                $this->deleteQuestion();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                $this->table();
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
