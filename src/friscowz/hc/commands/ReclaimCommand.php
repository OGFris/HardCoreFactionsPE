<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/13/2017
 * Time: 2:22 PM
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;

class ReclaimCommand extends PluginCommand
{
    /**
     * ReclaimCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("reclaim", $plugin);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof MDPlayer){
            $sender->reclaim();
        }
    }
}