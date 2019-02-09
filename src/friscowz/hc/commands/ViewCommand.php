<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/21/2017
 * Time: 12:34 AM
 */

namespace friscowz\hc\commands;

use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

class ViewCommand extends PluginCommand
{
    private $plugin;

    public function __construct (Myriad $plugin)
    {
        parent::__construct("view", $plugin);
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
        if($sender->isOp()){
        if (isset($args[0])) {
            if (is_numeric($args[0])) {
                if ($sender instanceof MDPlayer) {
                    $sender->setViewDistance($args[0]);
                }
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