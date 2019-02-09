<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:26
 */
namespace friscowz\hc\modules;

use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use friscowz\hc\task\SotwTask;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;

class SOTW implements Listener
{
    private $plugin;
    private static $enabled = false;
    private static $task;
    private static $main;

    /**
     * STOW constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
        self::$main = $plugin;
    }

    /**
     * @return bool
     */
    public function isEnabled () : bool
    {
        return self::$enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled (bool $enabled)
    {
        self::$enabled = $enabled;
    }

    /**
     *
     */
    public static function start()
    {
        self::$task = new SotwTask(self::$main);
        self::$enabled = true;
    }

    /**
     * @return int
     */
    public static function getTime() : int
    {
        return self::$task->getTime();
    }

    /**
     *
     */
    public static function stop()
    {
        self::$task->cancel();
        self::$enabled = false;
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
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event)
    {
        if($player = $event->getEntity() instanceof MDPlayer and $event->getCause() != EntityDamageEvent::CAUSE_VOID){
            if($this->isEnabled()){
                $event->setCancelled(true);
            }
        }
    }

}