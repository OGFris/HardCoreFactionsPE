<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:26
 */

namespace friscowz\hc\modules;

use friscowz\hc\Myriad;
use friscowz\hc\task\KothTask;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;

class KoTH implements Listener {

    private $plugin;
    private $koths;
    private static $running = false;
    private static $task;

    /**
     * KoTH constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
        $this->setKothFile(new Config($this->getPlugin()->getDataFolder() . "koths.json", Config::JSON));
    }

    /**
     * @return mixed
     */
    public function getTask ()
    {
        return self::$task;
    }

    /**
     * @param mixed $task
     */
    public function setTask ($task)
    {
        self::$task = $task;
    }

    /**
     * @return bool
     */
    public function isRunning() : bool
    {
        return self::$running;
    }

    /**
     * @param bool $running
     */
    public function setRunning(bool $running)
    {
        self::$running = $running;
    }

    /**
     * @param string $name
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     */
    public function createKoTH(string $name, Vector3 $pos1, Vector3 $pos2)
    {
        $all = $this->getKothFile()->getAll();
        $all[$name] = [
            "Pos1" => $pos1,
            "Pos2" => $pos2
        ];
        $this->getKothFile()->setAll($all);
        $this->getKothFile()->save();
    }

    /**
     * @param string $name
     */
    public function deleteKoTH(string $name)
    {
        $this->getKothFile()->remove($name);
        $this->getKothFile()->save();
    }

    /**
     * @param string $name
     */
    public function start(string $name)
    {
        if($this->isKoTH($name)) {
            $this->setTask(new KothTask($this->getPlugin(), $name));
            $this->setRunning(true);
        }
    }

    /**
     *
     */
    public function stop(){
        $this->getTask()->cancel();
        $this->setRunning(false);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isKoTH(string $name) : bool
    {
        return isset($this->getKothFile()->getAll()[$name]);
    }

    /**
     * @return mixed
     */
    public function getPlugin () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin ($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @return mixed
     */
    public function getKothFile () : Config
    {
        return $this->koths;
    }

    /**
     * @param mixed $koths
     */
    public function setKothFile ( Config $koths)
    {
        $this->koths = $koths;
    }

    /**
     * @param string $faction
     */
    public function rewardFaction(string $faction){
        $members = Myriad::getFactionsManager()->getOnlineMembers($faction);
        foreach ($members as $member){
            Myriad::getCrate()->giveKey($member, mt_rand(1, 3), mt_rand(1, 3));
        }
    }
}