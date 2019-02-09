<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 15/08/2017
 * Time: 02:45
 */

namespace friscowz\hc\commands;

use friscowz\hc\modules\SOTW;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class SOTWCommand extends PluginCommand
{
    private $plugin;
    /**
     * SOTWCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("sotw", $plugin);
        $this->setPlugin($plugin);
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
            if (isset($args[0])) {
                if (strtolower($args[0]) == "on") {
                    SOTW::start();
                } elseif (strtolower($args[0]) == "off") {
                    SOTW::stop();
                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /sotw <on|off>");
            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Usage: /sotw <on|off>");
        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have the permission to run this command!");
    }

    /**
     * @return Myriad
     */
    public function getMyriad() : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin(Myriad $plugin)
    {
        $this->plugin = $plugin;
    }
}