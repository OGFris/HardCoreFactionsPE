<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/11/2017
 * Time: 21:33
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;

class FactionTask extends PluginTask
{
    private $plugin;

    /**
     * FactionTask constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, (20*60) * 15)); // 5Mins
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        $factions = [];
        foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $player){
            if($player instanceof MDPlayer){
                if($player->isInFaction()){
                    $faction = $player->getFaction();
                    if(!in_array($faction, $factions)){
                        $factions[] = $faction;
                    }
                }
            }
        }
        if(count($factions) > 0){
            foreach ($factions as $faction){
                Myriad::getFactionsManager()->addDTR($faction);
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
}