<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 20:18
 */

namespace friscowz\hc\modules;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\DeathBanTask;
use friscowz\hc\task\KickTask;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use friscowz\hc\utils\Utils;

class DeathBan implements Listener
{
    private $plugin;
    public $bans = [];

    /**
     * DeathBan constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
        new DeathBanTask($this->getPlugin());

    }

    /**
     * @param PlayerPreLoginEvent $event
     */
    public function onPreLogin(PlayerPreLoginEvent $event)
    {
        if($this->isDeathBanned($event->getPlayer())){
            $event->setCancelled(true);
            $event->setKickMessage(Utils::getPrefix() . TextFormat::RED . "You are DeathBanned for " . $this->getBanTime($event->getPlayer())/* . PHP_EOL . TextFormat::GOLD . "Make you sure to joib our practice server while waiting!" . PHP_EOL . "IP: Impulsepe.ml Port: 19134"*/);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof MDPlayer) {
            $ban = true;
            $time = 60 * 30;
            switch ($player->getRank()){
                case MDPlayer::DEFAULT:
                    $time = 60 * 30;
                    break;

                case MDPlayer::BRONZE:
                    $time = 60 * 25;
                    break;

                case MDPlayer::SILVER:
                    $time = 60 * 20;
                    break;

                case MDPlayer::GOLD:
                    $time = 60 * 15;
                    break;

                case MDPlayer::DIAMOND:
                    $time = 60 * 10;
                    break;

                case MDPlayer::LEGACY:
                    $time = 60 * 5;
                    break;

                default:
                    $time = 0;
                    $ban = false;
                    break;
            }
            if($ban) {
                $this->addDeathBan($player, $time);
                new KickTask($this->getPlugin(), Utils::getPrefix() . TextFormat::RED . "You are DeathBanned for " . $this->getBanTime($player)/* . PHP_EOL . TextFormat::GOLD . "Make you sure to joib our practice server while waiting!" . PHP_EOL . "IP: Impulsepe.ml Port: 19134"*/, $player, true);
            }
        }
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
     * @param Player $player
     * @param int $time
     */
    public function addDeathBan(Player $player, int $time)
    {
        $ban = [
            "bans" => [
                "name" => $player->getName(),
                "ip" => $player->getAddress(),
            ],
            "time" => $time,
        ];
        array_push($this->bans, $ban);

    }

    /**
     * @param Player $p
     * @return string
     */
    public function getBanTime(Player $p) : string
    {
        foreach ($this->bans as $ban) {
            if ($ban['bans']['name'] == $p->getName()) {

                $hours = floor($ban['time'] / 3600);
                $minutes = floor(($ban['time'] / 60) % 60);
                $seconds = $ban['time'] % 60;
                $msg =
                    (($hours > 1) ? "$hours Hour" . ($hours > 1 ? 's' : '') : '') .
                    (($minutes > 1) ? " $minutes Minute" . ($minutes > 1 ? 's' : '') : '') .
                    (($seconds > 1) ? " $seconds Second" . ($seconds > 1 ? 's' : '') : '');
                return $msg;
            }
        }
        return "00:00";
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function isDeathBanned(Player $p)
    {
        if(count($this->bans) != 0) {
            foreach ($this->bans as $ban) {
                if ($ban['bans']['name'] == $p->getDisplayName()) {
                    return true;
                }
                /**if ($ban['bans']['ip'] == $p->getAddress()) {
                 * return true; //the ip does not matter because sometimes there is some kids playing with their bros
                 * }**/
            }
            return false;
        } else return false;
    }

    /**
     *
     */
    public function check()
    {
        if(count($this->bans) > 0) {
            foreach ($this->bans as $index => $ban) {
                if ($ban['time'] < 0) {
                    unset($this->bans[$index]);
                } else {
                    if($this->getPlugin()->getServer()->getTicksPerSecondAverage() > 10) {
                        $this->bans[$index]['time'] = $ban['time'] - 1;
                    } else {
                        $this->bans[$index]['time'] = $ban['time'] - 2;
                    }
                }
            }
            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player){
                if($player instanceof MDPlayer){
                    if($this->isDeathBanned($player)){

                    }
                }
            }
        }
    }

}