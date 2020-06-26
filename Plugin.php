<?php

namespace Kanboard\Plugin\CanoastecChart;

use DateTime;
use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;
use SimpleValidator\Validators\Date;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:dashboard:sidebar', 'canoastecchart:dashboard/sidebar');
        $this->route->addRoute('Gráfico estimativa x executado', 'TesteController', 'show', 'canoastefirstplugin');
    }
    
    public function getClasses()
    {
        return array(
            'Plugin\CanoastecChart\Controller' => array(
                'TasksController',
                'DashboardController'
            ),
            'Plugin\CanoastecChart\Model' => array(
                'UsuarioModel'
            )
        );
    }

    public function registeringNewHelpers(){
        $this->helper->register('myhelper', '\Kanboard\Plugin\CanoastecChart\Helper\MyHelper');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }
    
    public function getPluginName()
    {
        return 'CanoastecChart';
    }

    public function getPluginDescription()
    {
        return t('My plugin is awesome');
    }

    public function getPluginAuthor()
    {
        return 'leoni';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kanboard/plugin-myplugin';
    }

}

