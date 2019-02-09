<?php

/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 06:21
 */
namespace friscowz\hc\commands;

use friscowz\hc\Myriad;
use friscowz\hc\task\async\ShowDataTask;
use friscowz\hc\utils\Utils;
use pocketmine\command\CommandSender;
use friscowz\hc\MDPlayer;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\utils\TextFormat;

class PVPCommand extends PluginCommand
{
    private $plugin;


    /**
     * PVPCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("pvp", $plugin);
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
        if ($sender instanceof MDPlayer) {
            if (!isset($args[0])) {
                $messages=[Utils::getPrefix() . TextFormat::GREEN . "PVP Commands Help page!", TextFormat::GRAY . "/pvp lives: " . TextFormat::GRAY . "check your lives."/*, TextFormat::GRAY . "/pvp lives donate <amount> <player>: " . TextFormat::GRAY . "donate to someone with your lives."*/, TextFormat::GRAY . "/pvp enable: " . TextFormat::GRAY . "disable pvp timer and start fighting!"];
                foreach ($messages as $message) {
                    $sender->sendMessage($message);
                }
            } else
            switch (strtolower($args[0])) {
                case "enable":
                    if ($sender->isPvp()) {
                        $sender->setPvp(true);
                        $sender->setPvptime(0);
                        $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully enabled pvp!");
                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already the pvp enabled!");
                break;
/*
                case "lives":
                case "live":
                    if (!isset($args[1])) {
                        Myriad::getInstance()->getServer()->getScheduler()->scheduleAsyncTask(new ShowDataTask($sender->getName(), "Lives", Utils::getPrefix() . TextFormat::GREEN . "You have {value} Live(s) left."));
                    } else {
                        if (strtolower($args[1]) == "donate") {
                            if($sender->isOp() or $sender instanceof ConsoleCommandSender){
                                if ($player=$this->getMyriad()->getServer()->getPlayer($args[2]) and $player instanceof MDPlayer) {
                                    $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully sent " . $args[2] . " Live's to " . $player->getName() . "'s.");
                                    $player->addLives($args[2]);
                                    $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have received " . $args[2] . " Live's from " . $sender->getName() . "'s.");
                                } else {

                                }
                            } else {
                                $sender->sendMessage(TextFormat::RED . "You can't run this command because currently it's only for OPs.");
                            }

                             * TODO: Another Custom AsyncTask for this module
                            if (isset($args[2])) {
                                if (is_int($args[2])) {
                                    if ($sender->getLives() >= $args[2]) {
                                        if (isset($args[3])) {
                                            if ($player=$this->getMyriad()->getServer()->getPlayer($args[2]) and $player->isOnline() and $player instanceof MDPlayer) {
                                                $sender->reduceLives($args[2]);
                                                $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully sent " . $args[2] . " Live's to " . $player->getName() . "'s.");
                                                $player->addLives($args[2]);
                                                $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have received " . $args[2] . " Live's from " . $sender->getName() . "'s.");
                                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "This Player isn't online!");
                                        } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Please chose a player! use: /pvp lives donate <lives> <player>");
                                    } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You don't have enough of Lives!");
                                } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Please chose a valid number!");
                            } else $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "Please chose a number! use: use /pvp lives donate <lives> <player>");

                        }
                    }
                break;
*/
            }
        }
    }

    /**
     * @return mixed
     */
    public function getMyriad() : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin=$plugin;
    }
}