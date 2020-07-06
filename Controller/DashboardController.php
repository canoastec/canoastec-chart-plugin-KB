<?php

namespace Kanboard\Plugin\CanoastecChart\Controller;

require __DIR__.'/../vendor/autoload.php';

use Kanboard\Controller\BaseController;
use Kanboard\Plugin\CanoastecChart\Service\GenerateDataChartService;

class DashboardController extends BaseController 
{

    public function charts()
    {
        $data = (new GenerateDataChartService($this->container))->get();

        $data['title'] = t('Grafico estimado x executado');
        $data['user'] = $this->getUser();

        $this->hook->on('template:layout:js', array('template' => 'plugins/canoastecchart/Asset/Js/charts.js'));

        $this->response->html($this->helper->layout->dashboard('canoastecchart:dashboard/charts', array(
            'title' => $data['title'],
            'user' => $data['user'],
            'data' => $data,
            'percentageChart' => json_encode($data['percentageChart']),
            'tasksChart' => json_encode($data['tasksChart']),
            'timeChart' => json_encode($data['timeChart']),
        )));
    }
}