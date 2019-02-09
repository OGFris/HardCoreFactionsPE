<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 21/08/2017
 * Time: 19:56
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\LogoutTask;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

class LogoutCommand extends PluginCommand
{
    private $plugin;

    /**
     * LogoutCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("logout", $plugin);
        $this->setPlugin($plugin);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof MDPlayer){
            if(!$sender->isLogout()){
                $sender->setLogout(true);
                $sender->setLogoutTime(30);
                new LogoutTask($this->getMyriad(), $sender);
            }
        }
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