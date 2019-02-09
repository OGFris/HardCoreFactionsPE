<?php
/**
 * Created by PhpStorm.
 * User: Zakaria
 * Date: 29/10/2017
 * Time: 01:34
 */

namespace friscowz\hc\commands;


use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
class BuildRoadCommand extends PluginCommand
{
    private $plugin;

    public function __construct (Myriad $plugin)
    {
        parent::__construct("buildroad", $plugin);
        $this->setPlugin($plugin);
    }

    public function execute (CommandSender $sender, string $commandLabel, array $args)
    {

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