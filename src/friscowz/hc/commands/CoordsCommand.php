<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/11/2017
 * Time: 19:01
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class CoordsCommand extends PluginCommand
{
    private $plugin;

    /**
     * CoordsCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("coords", $plugin);
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
        if($sender instanceof MDPlayer){
            if($sender->isCoords()){
                $sender->setCoords(false);
                $sender->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "Coords disabled!");
            } else {
                $sender->setCoords(true);
                $sender->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "Coords enabled!");
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
     * @param Myriad $plugin
     */
    public function setPlugin (Myriad $plugin)
    {
        $this->plugin = $plugin;
    }
}