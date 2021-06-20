<?php

namespace elis\presenter;

use elis\utils;

/**
 * Home presenter
 * @version 0.2.0 210620 tile menu under roles
 * @version 0.0.1 201121 created
 */
class Home extends Main
{

    public function __construct(array $params)
    {
        parent::__construct($params);
    }

    public function run()
    {
        $homeTmplt = new utils\Template("page/home.html", [
            'adm' => '',
            'dsp' => '',
            'drv' => ''
        ]);
        $tileTmplt = new utils\Template("other/link-tile-item.html");
        if ($this->user->isInRole('ADM')) {
            $tileTmplt->setAllData([
                'href' => 'adm-dashboard',
                'caption' => 'Administrator',
                'subcaption' => 'User, Vehicle and Place administration'
            ]);
            $homeTmplt->setData('adm', (string)$tileTmplt);
        }
        if ($this->user->isInRole(['ADM', 'DSP'])) {
            $tileTmplt->setAllData([
                'href' => 'dsp-dashboard',
                'caption' => 'Dispatcher',
                'subcaption' => 'Route, Package and Event administration'
            ]);
            $homeTmplt->setData('dsp', (string)$tileTmplt);
        }
        if ($this->user->isInRole(['ADM', 'DRV', 'CDR'])) {
            $tileTmplt->setAllData([
                'href' => 'drv-dashboard',
                'caption' => 'Driver & Co-driver',
                'subcaption' => 'Events administration'
            ]);
            $homeTmplt->setData('drv', (string)$tileTmplt);
        }
        $this->pageTmplt->setData('title', 'European Logistic Information System');
        $this->pageTmplt->setData('main', $homeTmplt);
        echo $this->pageTmplt;
    }
}
