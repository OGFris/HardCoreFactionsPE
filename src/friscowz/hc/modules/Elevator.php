<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:29
 */

namespace friscowz\hc\modules;

use friscowz\hc\Myriad;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Sign;
use pocketmine\level\Level;

class Elevator implements Listener {

    const UP = 0;
    const DOWN = 1;

    private $plugin;

    /**
     * Elevator constructor.
     * @param Myriad $plugin
     */
    public function __construct (Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
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
     * @param SignChangeEvent $event
     */
    public function onSignChange(SignChangeEvent $event){
        if(strtolower($event->getLine(0)) != "[elevator]"){
            return;
        }

        if(strtolower($event->getLine(1)) == "up" || strtolower($event->getLine(1)) == "down"){
            $event->setLine(0, "§1[Elevator]§r");
            $event->setLine(1, strtolower($event->getLine(1)));
            $event->setLine(2, "");
            $event->setLine(3, "");
    }
}
    /**
     * @param PlayerInteractEvent $event
     */
    public function onSign(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();

        $vec = new Vector3($block->getX(), $block->getY(), $block->getZ());

        $tile = $player->getLevel()->getTile($vec);

        if($tile instanceof Sign){
            $line = $tile->getText();
            if($line[0] == "§1[Elevator]§r"){
                if(strtolower($line[1]) == "up"){
                    $this->sendTo($player, $this->getDirByText(strtolower($line[1])), $vec);
                }elseif(strtolower($line[1]) == "down"){
                    $this->sendTo($player, $this->getDirByText(strtolower($line[1])), $vec);
                }
            }
        }
    }

    /**
     * @param $text
     * @return int
     */
    public function getDirByText($text) : int
    {
        switch($text){
            case "up":
                return Elevator::UP;
            break;

            case "down":
                return Elevator::DOWN;
            break;

            default:
                return Elevator::UP;
            break;
        }
    }

    /**
     * @param Level $level
     * @param int $dir
     * @param Vector3 $vec
     * @return bool
     */
    public function signExist(Level $level, int $dir, Vector3 $vec) : bool
    {
        $bool = false;
        if($dir == Elevator::UP){
            for($i = $vec->getY()+1; $i < 256; $i++){
                $block = $level->getTile(new Vector3($vec->getX(), $i, $vec->getZ()));
                if($block instanceof Sign){
                    $line = $block->getText();
                    if($line[0] == "§1[Elevator]§r"){
                        $bool = true;
                        break;
                    }
                }
            }
        }elseif($dir == Elevator::DOWN){
            for($i = $vec->getY()-1; $i > 0; $i--){
                $block = $level->getTile(new Vector3($vec->getX(), $i, $vec->getZ()));
                if($block instanceof Sign){
                    $line = $block->getText();
                    if($line[0] == "§1[Elevator]§r"){
                        $bool = true;
                        break;
                    }
                }
            }
        }
        return $bool;
    }

    /**
     * @param Player $player
     * @param $dir
     * @param Vector3 $vec
     */
    public function sendTo(Player $player, $dir, Vector3 $vec)
    {
        if($this->signExist($player->getLevel(), $dir, $vec)){
            if($dir == Elevator::UP){
                $closest = $this->getClosestOfUp($player->getLevel(), $vec);
                $player->teleport(new Vector3($vec->getX(), $closest, $vec->getZ()));
            }elseif($dir == Elevator::DOWN){
                $closest = $this->getClosestOfDown($player->getLevel(), $vec);
                $player->teleport(new Vector3($vec->getX(), $closest, $vec->getZ()));
            }
        } else $player->sendMessage("§cThere is No Elevator sign set on this direction/position !");
    }

    /**
     * @param Level $level
     * @param Vector3 $vec
     * @return int
     */
    public function getClosestOfUp(Level $level, Vector3 $vec) : int
    {
        for($i = $vec->getY()+1; $i < 256; $i++){
            $block = $level->getTile(new Vector3($vec->getX(), $i, $vec->getZ()));
            if($block instanceof Sign){
                $line = $block->getText();
                if($line[0] == "§1[Elevator]§r"){
                    return $i;
                    break;
                }
            }
        }
        return $vec->getY();
    }

    /**
     * @param Level $level
     * @param Vector3 $vec
     * @return int
     */
    public function getClosestOfDown(Level $level, Vector3 $vec) : int
    {
        for($i = $vec->getY()-1; $i > 0; $i--){
            $block = $level->getTile(new Vector3($vec->getX(), $i, $vec->getZ()));
            if($block instanceof Sign){
                $line = $block->getText();
                if($line[0] == "§1[Elevator]§r"){
                    return $i;
                    break;
                }
            }
        }
        return $vec->getY();
    }
}