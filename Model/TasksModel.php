<?php

namespace Kanboard\Plugin\CanoastecChart\Model;

use Kanboard\Core\Base;
use Kanboard\Model\ColumnModel;
use Kanboard\Model\LinkModel;
use Kanboard\Model\ProjectModel;
use Kanboard\Model\TaskModel;
use Kanboard\Model\UserModel;
use Pimple\Psr11\Container;

class TasksModel extends Base{

    const TABLE = 'tasks';

    public function tasks()
    {
        return json_decode(json_encode(array_shift($this->db->table(self::TABLE)->findAll())));
    }

    public function getId(){
        return $this->tasks()->id;
    }

    public function getNome(){
        return $this->tasks()->title;
    }
      
}