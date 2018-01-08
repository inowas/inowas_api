<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\Tools;

use Inowas\AppBundle\Model\User;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Status\Visibility;
use Inowas\ModflowBundle\DataFixtures\Scenarios\LoadScenarioBase;
use Inowas\Tool\Model\Command\CreateToolInstance;
use Inowas\Tool\Model\ToolData;
use Inowas\Tool\Model\ToolId;
use Inowas\Tool\Model\ToolType;


class Tools extends LoadScenarioBase
{

    /** @var User */
    private $owner;

    public function load(): void
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $this->loadUsers($userManager);

        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');

        /** @var User $owner */
        $this->owner = $userManager->findUserByUsername('inowas');

        $command = $this->getCreateToolInstanceCommand(ToolType::fromString('T02'));
        $commandBus->dispatch($command);
        $command = $this->getCreateToolInstanceCommand(ToolType::fromString('T09A'));
        $commandBus->dispatch($command);
        $command = $this->getCreateToolInstanceCommand(ToolType::fromString('T09B'));
        $commandBus->dispatch($command);
        $command = $this->getCreateToolInstanceCommand(ToolType::fromString('T09C'));
        $commandBus->dispatch($command);
        $command = $this->getCreateToolInstanceCommand(ToolType::fromString('T09D'));
        $commandBus->dispatch($command);
        $command = $this->getCreateToolInstanceCommand(ToolType::fromString('T09E'));
        $commandBus->dispatch($command);
    }

    private function getCreateToolInstanceCommand(ToolType $toolType): CreateToolInstance
    {
        if ($toolType->toString() === 'T02') {
            return CreateToolInstance::newWithAllParams(
                UserId::fromString($this->owner->getId()),
                ToolId::generate(),
                $toolType,
                Name::fromString('Default'),
                Description::fromString(''),
                ToolData::fromArray(
                    json_decode(
                        '{
                        "settings":{"variable":"x"},
                        "parameters":[
                            {"id":"w","max":10,"min":0,"value":0.045},
                            {"id":"L","max":1000,"min":0,"value":40},
                            {"id":"W","max":100,"min":0,"value":20},
                            {"id":"hi","max":100,"min":0,"value":35},
                            {"id":"Sy","max":0.5,"min":0,"value":0.085},
                            {"id":"K","max":10,"min":0.1,"value":1.83},
                            {"id":"t","max":100,"min":0,"value":1.5}
                        ],
                        "tool":"'.$toolType->toString().'"
                        }',
                        true
                    )
                ),
                Visibility::public ()
            );
        }
        if ($toolType->toString() === 'T09A') {
            return CreateToolInstance::newWithAllParams(
                UserId::fromString($this->owner->getId()),
                ToolId::generate(),
                $toolType,
                Name::fromString($toolType->toString().' default'),
                Description::fromString(''),
                ToolData::fromArray(
                    json_decode(
                        '{
                        "parameters":[
                            {"id":"h","max":10,"min":0,"value":1},
                            {"id":"df","max":1.03,"min":0.9,"value":1},
                            {"id":"ds","max":1.03,"min":0.9,"value":1.025}
                        ],
                        "tool":"'.$toolType->toString().'"
                        }',
                        true
                    )
                ),
                Visibility::public ()
            );
        }
        if ($toolType->toString() === 'T09B') {
            return CreateToolInstance::newWithAllParams(
                UserId::fromString($this->owner->getId()),
                ToolId::generate(),
                $toolType,
                Name::fromString($toolType->toString().' default'),
                Description::fromString(''),
                ToolData::fromArray(
                    json_decode(
                        '{
                        "parameters": [
                            {
                              "id": "b",
                              "max": 100,
                              "min": 1,
                              "value": 50
                            },
                            {
                              "id": "i",
                              "max": 0.01,
                              "min": 0,
                              "value": 0.001
                            },
                            {
                              "id": "df",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1
                            },
                            {
                              "id": "ds",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1.025
                            }
                        ],
                        "tool":"'.$toolType->toString().'"
                        }',
                        true
                    )
                ),
                Visibility::public ()
            );
        }
        if ($toolType->toString() === 'T09C') {
            return CreateToolInstance::newWithAllParams(
                UserId::fromString($this->owner->getId()),
                ToolId::generate(),
                $toolType,
                Name::fromString($toolType->toString().' default'),
                Description::fromString(''),
                ToolData::fromArray(
                    json_decode(
                        '{
                            "parameters": [
                                {
                                  "id": "q",
                                  "max": 3000,
                                  "min": 1,
                                  "value": 2000
                                },
                                {
                                  "id": "k",
                                  "max": 100,
                                  "min": 1,
                                  "value": 50
                                },
                                {
                                  "id": "d",
                                  "max": 50,
                                  "min": 1,
                                  "value": 30
                                },
                                {
                                  "id": "df",
                                  "max": 1.03,
                                  "min": 0.9,
                                  "value": 1
                                },
                                {
                                  "id": "ds",
                                  "max": 1.03,
                                  "min": 0.9,
                                  "value": 1.025
                                }
                            ],
                            "tool":"'.$toolType->toString().'",
                        }',
                        true
                    )
                ),
                Visibility::public ()
            );
        }
        if ($toolType->toString() === 'T09D') {
            return CreateToolInstance::newWithAllParams(
                UserId::fromString($this->owner->getId()),
                ToolId::generate(),
                $toolType,
                Name::fromString($toolType->toString().' default'),
                Description::fromString(''),
                ToolData::fromArray(
                    json_decode(
                        '{
                        "parameters": [
                            {
                              "id": "k",
                              "max": 100,
                              "min": 1,
                              "value": 50
                            },
                            {
                              "id": "b",
                              "max": 100,
                              "min": 10,
                              "value": 20
                            },
                            {
                              "id": "q",
                              "max": 10,
                              "min": 0.1,
                              "value": 1
                            },
                            {
                              "id": "Q",
                              "max": 10000,
                              "min": 0,
                              "value": 5000
                            },
                            {
                              "id": "xw",
                              "max": 5000,
                              "min": 1000,
                              "value": 2000
                            },
                            {
                              "id": "rhof",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1
                            },
                            {
                              "id": "rhos",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1.025
                            }
                          ],
                          "tool":"'.$toolType->toString().'",
                          "settings": {
                            "AqType": "unconfined"
                          }
                        }',
                        true
                    )
                ),
                Visibility::public ()
            );
        }
        if ($toolType->toString() === 'T09E') {
            return CreateToolInstance::newWithAllParams(
                UserId::fromString($this->owner->getId()),
                ToolId::generate(),
                $toolType,
                Name::fromString($toolType->toString().' default'),
                Description::fromString(''),
                ToolData::fromArray(
                    json_decode(
                        '{
                            "parameters": [
                                {
                                  "id": "k",
                                  "max": 100,
                                  "min": 1,
                                  "value": 20
                                },
                                {
                                  "id": "z0",
                                  "max": 100,
                                  "min": 0,
                                  "value": 25
                                },
                                {
                                  "id": "l",
                                  "max": 10000,
                                  "min": 0,
                                  "value": 2000
                                },
                                {
                                  "id": "w",
                                  "max": 0.001,
                                  "min": 0,
                                  "value": 0.0001
                                },
                                {
                                  "id": "dz",
                                  "max": 2,
                                  "min": 0,
                                  "value": 1
                                },
                                {
                                  "id": "hi",
                                  "max": 10,
                                  "min": 0,
                                  "value": 2
                                },
                                {
                                  "id": "i",
                                  "max": 0.01,
                                  "min": 0,
                                  "value": 0.001
                                },
                                {
                                  "id": "df",
                                  "max": 1.005,
                                  "min": 1,
                                  "value": 1
                                },
                                {
                                  "id": "ds",
                                  "max": 1.03,
                                  "min": 1.02,
                                  "value": 1.025
                                }
                              ],
                            "settings": {
                                "method": "constFlux"
                            },
                            "tool":"'.$toolType->toString().'"
                        }',
                        true
                    )
                ),
                Visibility::public ()
            );
        }

        return null;
    }
}
