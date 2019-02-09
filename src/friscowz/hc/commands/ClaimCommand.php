<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 05/11/2017
 * Time: 23:13
 */

namespace friscowz\hc\commands;


use friscowz\hc\FactionsManager;
use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

class ClaimCommand extends PluginCommand
{
    private $plugin;
    private $players = [];

    /**
     * ClaimCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        parent::__construct("claim", $plugin);
        $this->setPlugin($plugin);
    }

    public function execute (CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->isOp()) {
            if (!isset($this->players[$sender->getName()])) {
                $this->players[$sender->getName()] = [
                    "pos1" => null,
                    "pos2" => null
                ];
            }
            if (isset($args[0])) {
                switch ($args[0]) {
                    case "delete":
                        Myriad::getFactionsManager()->getDb()->exec("DELETE FROM claims WHERE faction = '$args[1]';");
                        break;

                    case "pos1":
                        $this->players[$sender->getName()]["pos1"] = $sender->getPosition();
                        $sender->sendMessage("pos1 set.");
                        break;

                    case "pos2":
                        $this->players[$sender->getName()]["pos2"] = $sender->getPosition();
                        $sender->sendMessage("pos2 set.");
                    break;

                    case "confirm":
                        if (isset($args[1])) {
                            if($args[1] == "spawn"){
                                Myriad::getFactionsManager()->claim($args[1], $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::SPAWN);
                                $sender->sendMessage("spawn claim set.");
                            } else {
                                Myriad::getFactionsManager()->claim($args[1], $this->players[$sender->getName()]["pos1"], $this->players[$sender->getName()]["pos2"], FactionsManager::PROTECTED);
                                $sender->sendMessage("protected claim set.");
                            }
                        }
                    break;
                }
            }
        }
    }

    /**
     * @return Myriad
     */
    public function getMyriad () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin (Myriad $plugin)
    {
        $this->plugin = $plugin;
    }
}