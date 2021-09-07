<?php

namespace elis\presenter;

use elis\model;

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
        $this->adminTmplt->setData(
            'content',
            "Hello in ELIS administration." .
                (new model\CodeList("user-roles.json"))->legendToStr("User roles") .
                (new model\CodeList("countries.json"))->legendToStr("Countries") .
                (new model\CodeList("event-types.json"))->legendToStr("Event types") .
                (new model\CodeList("package-states.json"))->legendToStr("Package states") .
                (new model\CodeList("package-types.json"))->legendToStr("Package types") .
                (new model\CodeList("languages.json"))->legendToStr("Languages")

        );
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
