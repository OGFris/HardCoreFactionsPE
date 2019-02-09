<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 13/08/2017
 * Time: 01:39
 */

namespace friscowz\hc\modules;


use friscowz\hc\Myriad;
use pocketmine\utils\TextFormat;

class ModulesManager
{
    const SOTW = 0;
    const PVPTIMER = 1;
    const KOTH = 2;
    const ELEVATOR = 3;
    const SETS = 4;
    const DEATHBAN = 5;
    const SPAWNTAG = 6;
    const FD = 7;
    const LOGOUT = 8;

    private $plugin;

    public static $sotw, $pvptimer, $koth, $elevator, $sets, $deathban, $spawntag, $fd, $logout;

    /**
     * ModulesManager constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->registerModules();
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
    public function setPlugin (Myriad $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     *
     */
    public function registerModules()
    {
        self::$elevator = new Elevator($this->getPlugin());
        self::$koth = new KoTH($this->getPlugin());
        self::$deathban = new DeathBan($this->getPlugin());
        self::$pvptimer = new PVPTimer($this->getPlugin());
        self::$sotw = new SOTW($this->getPlugin());
        self::$sets = new Sets($this->getPlugin());
        self::$spawntag = new SpawnTag($this->getPlugin());
        self::$fd = new FoundDiamond($this->getPlugin());
        //self::$logout = new Logout($this->getPlugin());
        $this->getPlugin()->getLogger()->debug(TextFormat::YELLOW . "Registered All Modules(9).");
    }

    /**
     * @param int $module
     * @return mixed
     */
    public static function get(int $module)
    {
        switch ($module){
            case self::SOTW:
                return self::$sotw;
            break;

            case self::PVPTIMER:
                return self::$pvptimer;
            break;

            case self::KOTH:
                return self::$koth;
            break;

            case self::ELEVATOR:
                return self::$elevator;
            break;

            case self::SETS:
                return self::$sets;
            break;

            case self::DEATHBAN:
                return self::$deathban;
            break;

            case self::SPAWNTAG:
                return self::$spawntag;
            break;

            case self::FD:
                return self::$fd;
            break;

            case self::LOGOUT:
                return self::$logout;
            break;
        }
        return null;
    }
}