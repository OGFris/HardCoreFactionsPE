<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/24/2017
 * Time: 1:06 AM
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use friscowz\hc\Myriad;

class StaffCommand extends PluginCommand
{
    private $plugin;

    public function __construct (Myriad $plugin)
    {
        parent::__construct("staff", $plugin);
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
        if($sender instanceof MDPlayer)
        {
            if($sender->getRank() > 5 and $sender->getRank() < 12){
                if($sender->isStaff()){
                    $sender->removeStaff();
                } else {
                    $sender->addStaff();
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
     * @param mixed $plugin
     */
    public function setPlugin ($plugin)
    {
        $this->plugin = $plugin;
    }
}