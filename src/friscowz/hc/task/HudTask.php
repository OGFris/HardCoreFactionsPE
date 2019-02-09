<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 14/08/2017
 * Time: 01:16
 */

namespace friscowz\hc\task;

use friscowz\hc\MDPlayer;
use friscowz\hc\modules\ModulesManager;
use friscowz\hc\modules\SOTW;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use friscowz\hc\utils\Utils;

class HudTask extends PluginTask
{
    private $plugin;
    private $player;

    /**
     * HudTask constructor.
     * @param Myriad $plugin
     * @param MDPlayer $player
     */
    public function __construct (Myriad $plugin, MDPlayer $player)
    {
        parent::__construct($plugin);
        $this->setPlayer($player);
        $this->setPlugin($plugin);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 10));
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun (int $currentTick)
    {
        $player = $this->getPlayer();
        if(Myriad::getFactionsManager()->isSpawnClaim($player)){
            $player->setFood(20);
        }
        $text = "                                                                       " . TextFormat::BOLD . TextFormat::DARK_BLUE . "Legacy " . TextFormat::RESET . TextFormat::BLUE . "[Map 1]" . PHP_EOL . PHP_EOL;
        if (ModulesManager::get(ModulesManager::SOTW)->isEnabled()) {
            $text .= "                                                                       " . TextFormat::BOLD . TextFormat::DARK_BLUE . "SOTW: " . TextFormat::RESET . TextFormat::GRAY . Utils::intToString(SOTW::getTime()) . PHP_EOL;
        }
        if (ModulesManager::get(ModulesManager::KOTH)->isRunning()) {
            $text .= "                                                                       " . TextFormat::BOLD . TextFormat::BLUE . "KoTH: " . TextFormat::RESET . TextFormat::GRAY . Utils::intToString(ModulesManager::get(ModulesManager::KOTH)->getTask()->getTime()) . PHP_EOL;
        }
        if ($player->isPvp()) {
            $text .= "                                                                       " . TextFormat::BOLD . TextFormat::RED . "PVPTimer: " . TextFormat::RESET . TextFormat::GRAY . Utils::intToString($player->getPvptime()) . PHP_EOL;
        }
        if ($player->isLogout()) {
            $text .= "                                                                       " . TextFormat::BOLD . TextFormat::DARK_RED . "Logout: " . TextFormat::RESET . TextFormat::GRAY . Utils::intToString($player->getLogoutTime()) . PHP_EOL;
        }
        if ($player->isTagged()) {
            $text .= "                                                                       " . TextFormat::BOLD . TextFormat::RED . "CombatTag: " . TextFormat::RESET . TextFormat::GRAY . Utils::intToString($player->getTagtime()) . PHP_EOL;
        }
        if ($player->isCoords()) {
            $text .= "                                                                       " . TextFormat::BOLD . TextFormat::RED . "Coords: " . TextFormat::RESET . TextFormat::GRAY . $player->getFloorX() . ", " . $player->getFloorZ() . PHP_EOL;
        }
        if ($player->isBard()) {
            $text .= "                                                                       " . TextFormat::BOLD . TextFormat::GOLD . "BardEnergy: " . TextFormat::RESET . TextFormat::GRAY . $player->getBardEnergy() . PHP_EOL;
        }
        if ($player->isTeleporting()) {
            if ($player->getTeleport() == MDPlayer::HOME) {
                $text .= "                                                                       " . TextFormat::BOLD . TextFormat::YELLOW . "Home: " . TextFormat::RESET . TextFormat::GRAY . Utils::intToString($player->getTeleportTime()) . PHP_EOL;
            } elseif ($player->getTeleport() == MDPlayer::STUCK) {
                $text .= "                                                                       " . TextFormat::BOLD . TextFormat::YELLOW . "Stuck: " . TextFormat::RESET . TextFormat::GRAY . Utils::intToString($player->getTeleportTime()) . PHP_EOL;
            }
        }
        $text .= str_repeat(PHP_EOL, 12);
        $player->sendTip($text);

    }

    /**
     * @return Myriad
     */
    public function getPlugin () : Myriad
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

    /**
     * @return MDPlayer
     */
    public function getPlayer() : MDPlayer
    {
        return $this->player;
    }

    /**
     * @param MDPlayer $player
     */
    public function setPlayer(MDPlayer $player)
    {
        $this->player = $player;
    }
}