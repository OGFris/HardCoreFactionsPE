<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 00:59
 */
namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\modules\ModulesManager;
use friscowz\hc\Myriad;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;

class KothTask extends PluginTask
{

    private $plugin;
    private $time;
    private $endtime = 10;
    private $name;
    private $captured = false;
    private $faction = null;
    private $done = false;

    /**
     * KothTask constructor.
     * @param Myriad $plugin
     * @param string $name
     */
    public function __construct (Myriad $plugin, string $name)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $this->setName($name);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));
        $this->setTime(Myriad::getData("KothTime"));
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
        if($this->isDone()){
            $this->setEndtime($this->getEndtime()-1);
            if($this->getEndtime() == 0){
                ModulesManager::get(ModulesManager::KOTH)->stop();
            }
            return;
        }
        $file = ModulesManager::get(ModulesManager::KOTH)->getKothFile();
        $all = $file->getAll()[$this->getName()];
        $new = true;
        $maybe = null;
        foreach($this->getPlugin()->getServer()->getOnlinePlayers() as $player) {
            if ($player instanceof MDPlayer) {
                if($player->isInFaction()) {
                    if ($player->isOnPos(new Vector3($all["Pos1"]["x"], $all["Pos1"]["y"], $all["Pos1"]["z"]), new Vector3($all["Pos2"]["x"], $all["Pos2"]["y"], $all["Pos2"]["z"]))) {
                        if (!$this->isCaptured()) {
                            $this->setCaptured(true);
                            $new = false;
                            $this->setFaction($player->getFaction());
                        } else {
                            if ($player->getFaction() != $this->getFaction()) {
                                $maybe = $player->getFaction();
                            } else {
                                $new = false;
                            }
                        }
                    }
                }
            }
        }

        if($new){
            if($maybe != null){
                $this->setFaction($maybe);
            } else {
                $this->setCaptured(false);
                $this->setTime(Myriad::getData("KothTime"));
            }
        } else {
            $this->setTime($this->getTime()-1);
        }
        if($this->getTime() == 0){
            ModulesManager::get(ModulesManager::KOTH)->rewardFaction($this->getFaction());
            $this->setDone(true);
            ModulesManager::get(ModulesManager::KOTH)->setRunning(false);
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
     *
     */
    public function cancel()
    {
        $this->getHandler()->cancel();
    }

    /**
     * @return int
     */
    public function getTime () : int
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime (int $time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getName () : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName (string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEndtime () : int
    {
        return $this->endtime;
    }

    /**
     * @param mixed $endtime
     */
    public function setEndtime (int $endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * @return string
     */
    public function getFaction(): string
    {
        return $this->faction;
    }

    /**
     * @param string $faction
     */
    public function setFaction(string $faction)
    {
        $this->faction = $faction;
    }

    /**
     * @return bool
     */
    public function isCaptured(): bool
    {
        return $this->captured;
    }

    /**
     * @param bool $captured
     */
    public function setCaptured(bool $captured)
    {
        $this->captured = $captured;
    }

    /**
     * @return bool
     */
    public function isDone(): bool
    {
        return $this->done;
    }

    /**
     * @param bool $done
     */
    public function setDone(bool $done)
    {
        $this->done = $done;
    }

}