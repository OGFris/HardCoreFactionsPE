<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 04/11/2017
 * Time: 19:44
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class OresCommand extends PluginCommand
{
    private $plugin;

    public function __construct (Myriad $plugin)
    {
        parent::__construct("ores", $plugin);
        $this->setPlugin($plugin);
    }

    public function execute (CommandSender $sender, string $commandLabel, array $args)
    {

        if(count($args) == 0){
            if($sender instanceof MDPlayer){
                $diamond = $sender->getData()["Diamonds"];
                $gold = $sender->getData()["Gold"];
                $iron = $sender->getData()["Iron"];
                $lapis = $sender->getData()["Lapis"];
                $redstone = $sender->getData()["Redstone"];

                $sender->sendMessage(TextFormat::BLUE . "Lapis: " . TextFormat::GRAY . $lapis);
                $sender->sendMessage(TextFormat::AQUA . "Diamonds: " . TextFormat::GRAY . $diamond);
                $sender->sendMessage(TextFormat::WHITE . "Iron: " . TextFormat::GRAY . $iron);
                $sender->sendMessage(TextFormat::GOLD . "Gold: " . TextFormat::GRAY . $gold);
                $sender->sendMessage(TextFormat::RED . "Redstone: " . TextFormat::GRAY . $redstone);
            }
        } else {
            if(file_exists($this->getMyriad()->getDataFolder() . $args[0] . ".json")){

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