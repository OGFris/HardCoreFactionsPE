<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 31/10/2017
 * Time: 01:23
 */

namespace friscowz\hc\task\async;


use friscowz\hc\MDPlayer;
use friscowz\hc\utils\Utils;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class CheckClaimTask extends AsyncTask
{
    private $x1 = 0, $z1 = 0;
    private $x2 = 0, $z2 = 0;
    private $dir = "";
    private $player;

    /**
     * CheckClaimTask constructor.
     * @param int $x1
     * @param int $z1
     * @param int $x2
     * @param int $z2
     * @param string $dir
     * @param string $player
     */
    public function __construct (int $x1, int $z1, int $x2, int $z2, string $dir, string $player)
    {
        $this->setDir($dir);
        $this->setX1($x1);
        $this->setZ1($z1);
        $this->setX2($x2);
        $this->setZ2($z2);
        $this->setPlayer($player);
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun ()
    {
        $db = new \SQLite3($this->getDir());
        $count = 0;

        for ($x = $this->getX1(); $x <= $this->getX2(); $x++){
            for ($z = $this->getZ1(); $z <= $this->getZ2(); $z++){
                $result = $db->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
                $array = $result->fetchArray(SQLITE3_ASSOC);
                ++$count;

                if(empty($array) == false){
                    $this->setResult([
                        "not-claimed" => false
                    ]);
                    return;
                    break;
                }
            }
        }
        $cost = $count * 5;
        $this->setResult([
            "not-claimed" => true,
            "blocks" => $count,
            "cost" => $cost
        ]);
    }

    /**
     * @param Server $server
     */
    public function onCompletion (Server $server)
    {
        $player = $server->getPlayer($this->getPlayer());
        if($this->getResult()["not-claimed"] == false){
            $player->setClaiming(false);
            $player->setClaim(false);
            $player->setStep(MDPlayer::FIRST);
            $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You can't claim on other factions's claims!");
        } else {
            $count = $this->getResult()["blocks"];
            $cost = $this->getResult()["cost"];

            $player->setStep(MDPlayer::CONFIRM);
            $player->setClaim(true);
            $player->setClaimCost($cost);

            $player->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "Your claim of " . $count . " blocks cost " . $cost . "$. Sneak and right/left click to confirm your claim!");
            $player->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "to cancel it, type in the chat 'cancel'.");
        }
    }

    /**
     * @return int
     */
    public function getX1 () : int
    {
        return $this->x1;
    }

    /**
     * @param int $x1
     */
    public function setX1 (int $x1)
    {
        $this->x1 = $x1;
    }

    /**
     * @return int
     */
    public function getZ1 () : int
    {
        return $this->z1;
    }

    /**
     * @param int $z1
     */
    public function setZ1 (int $z1)
    {
        $this->z1 = $z1;
    }

    /**
     * @return int
     */
    public function getZ2 () : int
    {
        return $this->z2;
    }

    /**
     * @param int $z2
     */
    public function setZ2 (int $z2)
    {
        $this->z2 = $z2;
    }

    /**
     * @return int
     */
    public function getX2 () : int
    {
        return $this->x2;
    }

    /**
     * @param int $x2
     */
    public function setX2 (int $x2)
    {
        $this->x2 = $x2;
    }

    /**
     * @return string
     */
    public function getDir () : string
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     */
    public function setDir (string $dir)
    {
        $this->dir = $dir;
    }

    /**
     * @return string
     */
    public function getPlayer () : string
    {
        return $this->player;
    }

    /**
     * @param string $player
     */
    public function setPlayer (string $player)
    {
        $this->player = $player;
    }
}