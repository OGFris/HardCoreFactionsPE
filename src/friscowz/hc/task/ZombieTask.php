<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 23/08/2017
 * Time: 03:39
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\modules\ModulesManager;
use friscowz\hc\Myriad;
use pocketmine\entity\Zombie;
use pocketmine\scheduler\PluginTask;

class ZombieTask extends PluginTask
{
    private $plugin;
    private $time = 300;
    private $entity;
    private $player;

    /**
     * ZombieTask constructor.
     * @param Myriad $owner
     * @param Zombie $entity
     * @param MDPlayer $player
     */
    public function __construct(Myriad $owner, Zombie $entity, MDPlayer $player)
    {
        parent::__construct($owner);
        $this->setPlugin($owner);
        $this->entity = $entity;
        $this->player = $player;
        $this->setHandler($this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this, 20));
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
        if($this->player->isOnline()){
            $this->cancel();
            ModulesManager::get(ModulesManager::LOGOUT)->removeZombie($this->entity->getNameTag());
            $this->entity->close();
            return;
        }
        if($this->entity instanceof Zombie and $this->entity->isAlive()) {
            $this->setTime($this->getTime() - 1);
            if ($this->getTime() <= 0 || Myriad::getManager()->isRestarting()) {
                $this->cancel();
                ModulesManager::get(ModulesManager::LOGOUT)->removeZombie($this->entity->getNameTag());
                $this->entity->kill();
                return;
            }

        } else {
            $this->cancel();
        }
    }

    /**
     *
     */
    public function cancel()
    {
        $this->getHandler()->cancel();
    }

    /**
     * @return mixed
     */
    public function getPlugin() : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin(Myriad $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time)
    {
        $this->time = $time;
    }
}