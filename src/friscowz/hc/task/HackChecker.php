<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/25/2017
 * Time: 4:22 AM
 */

namespace friscowz\hc\task;

use friscowz\hc\Myriad;
use friscowz\hc\MDPlayer;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\network\mcpe\protocol\RemoveEntityPacket;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\UUID;

class HackChecker extends PluginTask
{
    private $plugin;
    private $player;
    private $eid = 100000000000;

    /**
     * PvpTask constructor.
     * @param Myriad $plugin
     * @param MDPlayer $player
     */
    public function __construct(Myriad $plugin, MDPlayer $player)
    {
        parent::__construct($plugin);
        $this->setPlayer($player);
        $this->setPlugin($plugin);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 10));
    }


    /**
     * @return Myriad
     */
    public function getPlugin(): Myriad
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

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        $player = $this->getPlayer();
        if ($player instanceof MDPlayer) {
            if($player->getDeviceOS() == MDPlayer::OS_ANDROID){

                $id = 10007412;
                $ign = mt_rand(15255, 546344);

                $uuid = UUID::fromRandom();
                $pkadd = new AddPlayerPacket();
                $pkadd->uuid = $uuid;
                $pkadd->username = "FrisBot $ign";
                $pkadd->entityRuntimeId = $id;
                $pkadd->entityUniqueId = $id;
                $pkadd->position = new Vector3($player->getX(), $player->getY() + 3, $player->getZ());
                $pkadd->yaw = 0;
                $pkadd->pitch = 0;
                $pkadd->item = Item::fromString(0);
                $flags = 0;
                $flags |= 1 << 5;
                $flags |= 1 << 14;
                $flags |= 1 << 15;
                $flags |= 1 << 16;
                $pkadd->metadata = [
                    Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
                    Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""]
                ];

                $pkremove = new RemoveEntityPacket();
                $pkremove->entityUniqueId = $id;


                $player->dataPacket($pkremove);
                if($player->isTagged()) {
                    $player->dataPacket($pkadd);
                }
            } else {
                $this->cancel();
            }
        } else {
            $this->cancel();
        }
    }

    public function cancel()
    {
        $this->getHandler()->cancel();
    }

    /**
     * @return MDPlayer
     */
    public function getPlayer(): MDPlayer
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