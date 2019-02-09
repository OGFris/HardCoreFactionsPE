<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 03/11/2017
 * Time: 02:10
 */

namespace friscowz\hc\commands;


use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use friscowz\hc\Myriad;
use pocketmine\utils\TextFormat;

class CrateCommand extends PluginCommand
{
    private $plugin;

    /**
     * CrateCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        parent::__construct("crate", $plugin);
        $this->setPlugin($plugin);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute (CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->isOp()) {
            if (count($args) == 0) {
                $sender->sendMessage(Utils::getPrefix() . "Crates help page 1/2:");
                $sender->sendMessage(TextFormat::GRAY . "- /crate give <(int)Crate Tier> <count> <player | all | null>");
                return;
            }
            if (isset($args[0]) and strtolower($args[0]) == "give") {
                if (isset($args[1]) and isset($args[2])) {
                    if ($args[1] > 0 and $args[1] < 6) {
                        if ($args[2] > 0) {
                            if (isset($args[2])) {
                                if ($args[3] == "all") {
                                    foreach ($this->getMyriad()->getServer()->getOnlinePlayers() as $p) {
                                        Myriad::getCrate()->giveKey($p, $args[1], $args[2]);
                                    }
                                    $this->getMyriad()->getServer()->broadcastMessage(Utils::getPrefix() . $sender->getName() . "Gave everyone " . $args[2] . "x " . Myriad::getCrate()->getKeyName($args[1] - 1) . TextFormat::GRAY . "!");
                                    return;
                                }
                                $player = $this->getMyriad()->getServer()->getPlayer($args[3]);
                                if ($player) {
                                    Myriad::getCrate()->giveKey($player, $args[1], $args[2]);
                                    $this->getMyriad()->getServer()->broadcastMessage(Utils::getPrefix() . TextFormat::GRAY . $player->getName() . " just received " . $args[1] . "x " . Myriad::getCrate()->getKeyName($args[1] - 1) . TextFormat::GRAY . "!");
                                }
                            }
                        }
                    }
                }
            }
        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::YELLOW . "Buy crate keys @ myriadhcf.buycraft.net");
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