<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/11/2017
 * Time: 18:19
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class PayCommand extends PluginCommand
{
    private $plugin;

    /**
     * PayCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("pay", $plugin);
        $this->setPlugin($plugin);
        $this->setAliases(["donate"]);
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

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
            if(isset($args[0]) and isset($args[1])) {
                $player = $this->getMyriad()->getServer()->getPlayer($args[0]);
                if (is_numeric($args[1]) and $player) {
                    if($sender instanceof MDPlayer) {
                        if ($player instanceof MDPlayer) {
                            if($sender->getMoney() >= $args[1] and $args[1] > 0) {
                                $player->addMoney($args[1]);
                                $sender->reduceMoney($args[1]);
                                $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully sent $args[1]$ to " . $player->getName() . "!");
                                $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have received $args[1]$ from " . $sender->getName() . "!");
                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have enough of money!");
                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This player isn't online!");
                    }
                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Invalid arguments! /pay <player> <money>");
            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Invalid arguments! /pay <player> <money>");
    }

}