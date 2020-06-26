<?php

namespace Kanboard\Plugin\CanoastecChart\Controller;

require __DIR__.'/../vendor/autoload.php';

use Kanboard\Controller\BaseController;
use Kanboard\Api\Procedure\TaskProcedure;
use Tightenco\Collect\Support\Collection;
use Kanboard\Api\Procedure\TaskLinkProcedure;

class DashboardController extends BaseController 
{

    public function charts()
    {
        $this->hook->on('template:layout:js', array('template' => 'plugins/canoastecchart/Asset/Js/charts.js'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/canoastecchart/Asset/Js/script.js'));

        $selectedSprints = $this->getSeasonCompareHomeOffice();
        $selectedSprints = $this->getSprintsAndTasks($selectedSprints);

        $timeChart = $this->timeChart($selectedSprints);
        $tasksChart = $this->tasksChart($selectedSprints);
        $percentageChart = $this->percentageChart($selectedSprints);

        $this->response->html($this->helper->layout->dashboard('canoastecchart:dashboard/charts', [
            'title'   => t('Grafico estimado x executado'),
            'user'    => $this->getUser(),
            'selectedSprints' => $selectedSprints,
            'selectedSprints' => $selectedSprints,
            'timeChart' => $timeChart,
            'tasksChart' => $tasksChart,
            'percentageChart' => $percentageChart,
        ]));
    }

    private function getSeasonCompareHomeOffice()
    {
        $sprints = $this->getAllSprints()->keyBy('title');
        
        $currentSprint = $this->getCurrentSprint();
        $currentSprint = (int)str_replace("Sprint ", "", $currentSprint['title']);
        
        $seasonSprints = [];
        for ($i = 75-($currentSprint-75); $i <= 75; $i++) {
            array_push($seasonSprints, "Sprint ".$i);
        }
        for ($i = 76; $i <= $currentSprint; $i++) {
            array_push($seasonSprints, "Sprint ".$i);
        }
        $seasonSprints = $sprints->whereIn('title', $seasonSprints)->map(function ($sprint){
            return $sprint['id'];
        });
        
        return $seasonSprints;
    }

    private function getAllSprints()
    {
        $tasks = $this->api
            ->getProcedureHandler()
            ->executeMethod(
                new TaskProcedure($this->container), 
                'searchTasks', 
                [1, '']
            );
        $tasks = collect($tasks);

        return $tasks->sortBy(function ($sprint){
            return str_replace("Sprint ", "", $sprint['title']);
        });
    }

    private function getCurrentSprint()
    {
        $sprint = $this->api
            ->getProcedureHandler()
            ->executeMethod(
                new TaskProcedure($this->container), 
                'searchTasks', 
                [1, 'column:Andamento']
            );
        $sprint = collect($sprint);

        return $sprint->first();
    }

    private function getSprintsAndTasks(Collection $sprintsId)
    {
        return $sprintsId->map(function ($sprintId){
            $sprint = $this->getSprint($sprintId);

            $tasks = $this->getAllTaskLinks($sprintId)->map(function($task){
                $task['color_id'] == "red" && $task['task_time_estimated'] == '0' ? $task['task_time_estimated'] = '24' : '';
                $task['color_id'] == "blue" && $task['task_time_estimated'] == '0' ? $task['task_time_estimated'] = '24' : '';
                return $task;   
            });

            $tasks = $tasks->filter(function($task){
                return $task['label'] != 'possui tarefas de outra sprint';
            });

            $sprint['estimated'] = $tasks->sum(function($task){
                return $task['task_time_spent'] != '0' ? (int)$task['task_time_estimated'] : false;
            });

            $sprint['spent'] = $tasks->sum(function($task){
                return (int)$task['task_time_spent'];
            });

            $sprint['tasks'] = $tasks;

            return $sprint;
        })->filter(function($sprint){
            return $sprint['estimated'] && $sprint['spent'] != 0;
        });
    }
   
    private function getSprint($sprintId)
    {
        $sprint = $this->api
            ->getProcedureHandler()
            ->executeMethod(
                new TaskProcedure($this->container), 
                'searchTasks', 
                [1, "id:".$sprintId]
            );
        $sprint = collect($sprint);

        return $sprint->first();
    }

    private function getAllTaskLinks($sprintId)
    {
        $sprint = $this->api
            ->getProcedureHandler()
            ->executeMethod(
                new TaskLinkProcedure($this->container), 
                'getAllTaskLinks', 
                [$sprintId]
            );
    
        return collect($sprint);
    }

    private function timeChart($sprints)
    {
        $labels = [];
        $estimatedData = [];
        $spentData = [];
        foreach ($sprints as $sprint){
            array_push($labels, $sprint['title']);
            array_push($estimatedData, $sprint['estimated']);
            array_push($spentData, $sprint['spent']);
        }
        return (object) [
            'labels'=> $labels,
            'datasets' => [
                (object)[
                    'label' => 'Tempo estimado ('.collect($estimatedData)->sum().' horas)',
                    'color' => 'red',
                    'data' => $estimatedData
                ],
                (object)[
                    'label' => 'Tempo executado ('.collect($spentData)->sum().' horas)',
                    'color' => 'blue',
                    'data' => $spentData
                ]
            ]
        ];
    }

    private function tasksChart($sprints)
    {
        $labels = [];
        $tasksData = [];
        foreach ($sprints as $sprint){
            array_push($labels, $sprint['title']);
            array_push($tasksData, $sprint['tasks']->filter(function ($task){
                return $task['task_time_spent'] != '0';
            })->count());
        }
        return (object) [
            'labels'=> $labels,
            'datasets' => [
                (object)[
                    'label' => 'Total de tarefas',
                    'color' => 'orange',
                    'data' => $tasksData
                ]
            ]
        ];
    }

    private function percentageChart($sprints)
    {
        $labels = [];
        $percentSpentData = [];
        foreach ($sprints as $sprint){
            array_push($labels, $sprint['title']);
            array_push($percentSpentData, round((int)100*($sprint['spent']/$sprint['estimated'])));
        }
        return (object) [
            'labels'=> $labels,
            'datasets' => [
                (object)[
                    'label' => 'Percentual utilizado do tempo estimado',
                    'color' => 'green',
                    'data' => $percentSpentData
                ]
            ]
        ];
    }
}