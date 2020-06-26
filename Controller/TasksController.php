<?php

namespace Kanboard\Plugin\CanoastecChart\Controller;

use Kanboard\Core\Base;

class TasksController extends Base {

    public function show()
    {
        $this->template->hook->attach('template:layout:head','canoastecchart:head/head',array('usuario' => $this->usuarioModel));
    }

}