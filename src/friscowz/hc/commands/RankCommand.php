<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/18/2017
 * Time: 12:53 AM
 */

namespace friscowz\hc\commands;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class RankCommand extends PluginCommand
{
    private $whitelisted = ["friscowzmcpe", "gdrgamingzz"];
    /**
     * RankCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("rank", $plugin);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->isOp()){
            if(count($args) == 2){
                if($args[1] < MDPlayer::HEAD_ADMIN) {
                    $player = $sender->getServer()->getPlayer($args[0]);
                    if ($player) {
                        if ($player instanceof MDPlayer) {
                            $player->setRank($args[1]);
                            $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "Your rank has been successfully updated!");
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully updated " . $player->getName() . "'s rank!");
                        }
                    }
                } else {
                    if(in_array(strtolower($sender->getName()), $this->whitelisted) or $sender instanceof ConsoleCommandSender){
                        $player = $sender->getServer()->getPlayer($args[0]);
                        if ($player) {
                            if ($player instanceof MDPlayer) {
                                $player->setRank($args[1]);
                                $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "Your rank has been successfully updated!");
                                $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully updated " . $player->getName() . "'s rank!");
                            }
                        }
                    }
                }
            }
        }
    }
}