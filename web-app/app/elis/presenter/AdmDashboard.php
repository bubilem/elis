<?php

namespace elis\presenter;

use elis\utils;

/**
 * Administration dashboard presenter
 * @version 0.0.1 201223 created
 */
class AdmDashboard extends Administration
{

    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->pageTmplt->setData('title', "Admin :: Dashboard");
        $this->adminTmplt->setData('content', "Hello in ELIS administration.");
    }

    protected function newForm($model = null)
    {
    }

    protected function new()
    {
    }

    protected function editForm($model = null)
    {
    }

    protected function edit()
    {
    }
    protected function deleteQuestion()
    {
    }

    protected function delete()
    {
    }

    protected function table()
    {
    }
}
