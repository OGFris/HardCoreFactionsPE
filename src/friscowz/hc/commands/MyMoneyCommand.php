<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 23:29
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\utils\Utils;
use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class MyMoneyCommand extends PluginCommand
{
    private $plugin;

    /**
     * MyMoneyCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("mymoney", $plugin);
        $this->setPlugin($plugin);
        $this->setAliases(["seemoney", "money"]);
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
			if($sender->getMoney() < 0) $sender->setMoney(100);
            $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have " . $sender->getMoney() . "$");
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