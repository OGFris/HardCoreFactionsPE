<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 5:15 PM
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class PingCommand extends PluginCommand
{
    /**
     * PingCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("ping", $plugin);
        $this->setAliases(["ms", "letency"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof MDPlayer){
            $color = $sender->getPing() < 50 ? TextFormat::GREEN : $sender->getPing() < 200 ? TextFormat::YELLOW : TextFormat::RED;
            $sender->sendMessage(TextFormat::GREEN . "Your ping is: " . $color . $sender->getPing() . "ms.");
        }
    }
}