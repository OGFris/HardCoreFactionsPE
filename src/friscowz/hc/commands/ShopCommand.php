<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 25/08/2017
 * Time: 13:11
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class ShopCommand extends PluginCommand
{
    private $plugin;

    /**
     * ShopCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("shop", $plugin);
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
        /*
        if($sender instanceof MDPlayer){
            if($sender->isOp()){
                if(isset($args[0])) {
                    switch($args[0]) {
                        case "create":
                            if (isset($args[0]) and isset($args[1]) and isset($args[2]) and isset($args[3]) and isset($args[4])) {
                                Myriad::getShop()->createSign($args[0], $args[1], $args[2], $args[3], $args[4], $sender->getSign());
                            } else $sender->sendMessage();
                            break;

                        case "help":
                        case "?":
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "Shop help page!");
                            $sender->sendMessage(TextFormat::GRAY . "- /shop create <type: sell-buy> <id> <damage> <amount> <price> <name>");
                    }
                }
            }
        }*/
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