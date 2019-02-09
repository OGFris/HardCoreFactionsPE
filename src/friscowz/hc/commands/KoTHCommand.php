<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 15/08/2017
 * Time: 02:57
 */

namespace friscowz\hc\commands;


use friscowz\hc\utils\Utils;
use friscowz\hc\modules\ModulesManager;
use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class KoTHCommand extends PluginCommand
{
    private $pos = [];

    /**
     * KoTHCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("koth", $plugin);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->isOp()) {
            if (!isset($args[0])) {
                $messages = [Utils::getPrefix() . TextFormat::GREEN . "KoTH Commands Help!", TextFormat::GRAY . "/koth create <name>: Create a new KoTH Arena", TextFormat::GRAY . "/koth start <name>: Start the koth", TextFormat::GRAY . "/koth stop <name>: Stop the KoTH"];
                foreach ($messages as $message) {
                    $sender->sendMessage($message);
                }
            } else {
                switch (strtolower($args[0])){
                    case "create":
                    case "make":
                        ModulesManager::get(ModulesManager::KOTH)->createKoTH($args[1], $this->pos[$sender->getName()]["pos1"], $this->pos[$sender->getName()]["pos2"]);
                    break;

                    case "pos1":
                        $this->pos[$sender->getName()]["pos1"] = new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ());
                        $sender->sendMessage("koth pos1 set");
                    break;

                    case "pos2":
                        $this->pos[$sender->getName()]["pos2"] = new Vector3($sender->getFloorX(), $sender->getFloorY(), $sender->getFloorZ());
                        $sender->sendMessage("koth pos2 set");
                    break;

                    case "start":
                        if(isset($args[1])){
                            ModulesManager::get(ModulesManager::KOTH)->start($args[1]);
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Please chose a valid KoTH Arena name! use: /koth start name");
                    break;

                    case "stop":
                        ModulesManager::get(ModulesManager::KOTH)->stop();
                    break;
                }
            }
        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have the permission to run this command!");
    }
}