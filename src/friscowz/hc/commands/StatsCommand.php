<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 04/11/2017
 * Time: 16:36
 */

namespace friscowz\hc\commands;

use friscowz\hc\MDPlayer;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use friscowz\hc\Myriad;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class StatsCommand extends PluginCommand
{
    private $plugin;

    public function __construct (Myriad $plugin)
    {
        parent::__construct("stats", $plugin);
        $this->setPlugin($plugin);
    }

    public function execute (CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof MDPlayer){
            $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_BLUE . "Kills: " . TextFormat::RESET . TextFormat::GRAY . $sender->getKills());
            $sender->sendMessage(TextFormat::BOLD . TextFormat::DARK_RED . "Deaths: " . TextFormat::RESET . TextFormat::GRAY . $sender->getDeaths());
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