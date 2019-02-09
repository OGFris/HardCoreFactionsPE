<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 20:09
 */
namespace friscowz\hc;

use friscowz\{
    hc\modules\ModulesManager, hc\task\async\CheckClaimTask, hc\task\async\CheckProxyTask, hc\task\async\SaveDataTask, hc\task\HackChecker, hc\task\HudTask, hc\task\PvpTask, hc\utils\Utils
};
use pocketmine\{
    block\BlockIds, entity\Effect, item\Item, item\ItemIds, math\Vector3, math\Vector2, network\mcpe\protocol\ChangeDimensionPacket, network\mcpe\protocol\UpdateBlockPacket, network\SourceInterface, Player, plugin\PluginException, utils\Config, utils\TextFormat
};

class MDPlayer extends Player
{
    const OS_ANDROID = 1;
    const OS_IOS = 2;
    const OS_OSX = 3;
    const OS_FIREOS = 4;
    const OS_GEARVR = 5;
    const OS_HOLOLENS = 6;
    const OS_WIN10 = 7;
    const OS_WIN32 = 8;
    const OS_DEDICATED = 9;
    const OS_ORBIS = 10;
    const OS_NX = 11;

    const NORMAL = 0;
    const NETHER = 1;
    const END = 2;

    const HOME = 0;
    const STUCK = 1;

    const FIRST = 0;
    const SECOND = 1;
    const CONFIRM = 3;

    const DEFAULT = 0;
    const BRONZE = 1; // 5$
    const SILVER = 2; // 10$
    const GOLD = 3; // 20$
    const DIAMOND = 4; // 30$
    const LEGACY = 5; // 50$
    const TRIAL = 6;
    const MOD = 7;
    const ADMIN = 8;
    const HEAD_ADMIN = 9;
    const OWNER = 10;
    const FRIS = 11;
    const FAMOUS = 12;
    const PARTNER = 13;

    const PUBLIC = 0;
    const FACTION = 1;

    private $spawntag = false;
    private $teleporttype;
    private $data = null;
    private $plugin;
    private $isteleporting = false;
    private $teleporttask;
    private $tagged = false;
    private $tagtime = 0;
    private $archertagger = "null";
    private $archertagged = false;
    private $lastinvite;
    private $invited = false;
    private $task;
    private $logout = false;
    private $LogoutTime = 30;
    private $aimhack = 0;
    private $reach = 0;
    private $flyhack = 0;
    private $lastclick = 0;
    private $claiming = false;
    private $pos1;
    private $pos2;
    private $step = self::FIRST;
    private $last;
    private $claim = [
        "cost" => 0,
        "claim" => false
    ];
    private $disablemovement;
    private $region = "null";
    private $class = "normal";
    protected $os;
    private $sign;
    private $coords = false;
    private $kit = false;
    private $chat = self::PUBLIC;
    private $staff = false;
    private $oldinv = [];
    private $vanish = false;
    private $freeze = false;
    private $bardenergy = 0;

    /**
     * MDPlayer constructor.
     * @param SourceInterface $interface
     * @param null $clientID
     * @param string $ip
     * @param int $port
     */
    public function __construct(SourceInterface $interface, $clientID, $ip, $port)
    {
        parent::__construct($interface, $clientID, $ip, $port);

        if (($plugin = $this->getServer()->getPluginManager()->getPlugin("LegacyHCF")) instanceof Myriad and $plugin->isEnabled()) {
            $this->setPlugin($plugin);
        } else {
            $this->kick(TextFormat::RED . "Error #1: " . TextFormat::GRAY . "Please report this error to @LegacySupportMC !", false);
            throw new PluginException("The MyriadHC Core isn't loaded!");
            $this->getServer()->shutdown();
        }
    }

    /**
     * @return int
     */
    public function getDeviceOS() : int
    {
        return $this->os;
    }

    /**
     * @param int $os
     */
    public function setDeviceOS(int $os)
    {
        $this->os = $os;
    }

    /**
     * @return string
     */
    public function getFaction() : string
    {
        $name = $this->getName();
        $result = Myriad::getFactionsManager()->getDb()->query("SELECT * FROM players WHERE name = '$name';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return $array["faction"];
    }

    /**
     * @param string $faction
     * @param int $rank
     */
    public function addToFaction(string $faction, int $rank = FactionsManager::MEMBER)
    {
        $name = $this->getName();
        Myriad::getFactionsManager()->getDb()->exec("INSERT OR REPLACE INTO players (name, rank, faction) VALUES ('$name', " . $rank . ", '$faction');");
    }

    /**
     *
     */
    public function removeFromFaction()
    {
        $name = $this->getName();
        Myriad::getFactionsManager()->getDb()->exec("DELETE FROM players WHERE name = '$name';");
    }

    /**
     * @return bool
     */
    public function isInFaction() : bool
    {
        $name = $this->getName();
        $result = Myriad::getFactionsManager()->getDb()->query("SELECT * FROM players WHERE name = '$name';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return empty($array) == false;
    }

    /**
     *
     */
    public function int()
    {
        @mkdir($this->getPlugin()->getDataFolder() . "players");
        if (!file_exists($this->getPlugin()->getDataFolder() . "players/" . strtolower($this->getName()) . ".json")) {
            $this->data = new Config($this->getPlugin()->getDataFolder() . "players/" . strtolower($this->getName()) . ".json", Config::JSON, array("money" => 0, "Kills" => 0, "Deaths" => 0, "Diamonds" => 0, "Gold" => 0, "Iron" => 0, "Lapis" => 0, "Redstone" => 0, "Rank" => self::DEFAULT, "PvPTime" => 900, "PvP" => true, "map" => 1));
            //$this->addToMySql();
        }
        $this->data = new Config($this->getPlugin()->getDataFolder() . "players/" . strtolower($this->getName()) . ".json", Config::JSON);
        /*
        if($this->data->get("Rank") == null){
            $this->data->set("Rank", self::DEFAULT);
            $this->data->save(true);
        }

        if(!$this->data->exists("map")){
            $this->data->setAll(array("money" => 0, "Kills" => 0, "Deaths" => 0, "Diamonds" => 0, "Gold" => 0, "Iron" => 0, "Lapis" => 0, "Redstone" => 0,"Rank" => $this->data->get("Rank"),"PvPTime" => 900, "PvP" => true, "map" => 1));
            $this->data->save(true);
        }*/
        if(!$this->data->exists("pvp")){
            $this->data->set("PvP", false);
            $this->data->save();
        }
        if (!$this->data->exists("reclaim")){
            $this->data->set("reclaim", time() - Utils::DAY_TIME);
            $this->data->save();
            if ($this->getRank() > self::DEFAULT) $this->sendMessage(Utils::getPrefix() . TextFormat::YELLOW . "You haven't claimed your daily keys yet! do /reclaim to claim");
        }
        $this->setLogout(false);
        $this->setLast(new Vector3($this->x, $this->y, $this->z));
        $this->disableMovement(time());
        new HudTask($this->getPlugin(), $this);
        new PvpTask($this->getPlugin(), $this);
        if($this->getDeviceOS() === self::OS_ANDROID and $this->getRank() === self::DEFAULT){
            new HackChecker($this->getPlugin(), $this);
        }
    }

    /**
     * @return array
     */
    public function getData() : array
    {

        return $this->getDataFile()->getAll();
    }

    /**
     * @return Config
     */
    public function getDataFile() : Config
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @param int $amount
     */
    public function addData(string $data, int $amount)
    {
        $this->getDataFile()->set($data, $this->getData()[$data] + $amount);
        $this->getDataFile()->save();
    }

    /**
     * @return int
     */
    public function getMoney() : int
    {
        return $this->getData()["money"];
    }

    /**
     * @param int $amount
     */
    public function setMoney(int $amount)
    {
        $this->setData("money", $amount);
    }

    /**
     * @param int $amount
     */
    public function addMoney(int $amount)
    {
        $this->setMoney($this->getMoney() + $amount);
    }

    /**
     * @param int $amount
     */
    public function reduceMoney(int $amount)
    {
        $this->setMoney($this->getMoney() - $amount);
    }

    /**
     * @param string $data
     * @param $value
     */
    public function setData(string $data, $value)
    {
        $this->getDataFile()->set($data, $value);
        $this->getDataFile()->save();
    }


    /**
     * @return mixed
     */
    public function getDeaths(): int
    {
        return $this->getData()["Deaths"];
    }

    /**
     * @param int $deaths
     */
    public function addDeaths(int $deaths)
    {
        $this->getDataFile()->set("Deaths", $this->getDeaths() + $deaths);
        $this->getDataFile()->save();
    }

    /**
     * @return int
     */
    public function getKills(): int
    {
        return $this->getData()["Kills"];
    }

    /**
     * @param int $kills
     */
    public function addKills(int $kills)
    {
        $this->getDataFile()->set("Kills", $this->getKills() + $kills);
        $this->getDataFile()->save();
    }

    /**
     * @param int $lives
     */
    public function addLives(int $lives)
    {
        //$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SaveDataTask($this->getName(), "Lives", $lives, SaveDataTask::ADD));
    }

    /**
     * @param int $lives
     */
    public function reduceLives(int $lives)
    {
        //$this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SaveDataTask($this->getName(), "Lives", $lives, SaveDataTask::REDUCE));
    }

    /**
     * @return bool
     */
    public function isPvp(): bool
    {
        return $this->getData()["PvP"];
    }

    /**
     * @param bool $pvp
     */
    public function setPvp(bool $pvp)
    {
        $this->setData("PvP", $pvp);
    }

    /**
     * @return int
     */
    public function getPvptime() : int
    {
        return $this->getData()["PvPTime"];
    }

    /**
     * @param int $pvptime
     */
    public function setPvptime(int $pvptime)
    {
        $this->setData("PvPTime", $pvptime);
    }

    public function resetPvptimer()
    {
        $this->setPvptime(300);
        $this->setPvp(true);
    }

    public function resetReclaim()
    {
        $this->setData("reclaim", time());
    }

    /**
     * @return int
     */
    public function getReclaim() : int
    {
        return $this->getData()["reclaim"];
    }

    /**
     * @param int $tier
     */
    public function setRank(int $tier)
    {
        $this->setData("Rank", $tier);
    }

    /**
     * @return int
     */
    public function getRank() : int
    {
        return $this->getData()["Rank"];
    }

    /**
     * @param int $dimension
     * @param Vector3 $vector3
     */
    public function changeDimension(int $dimension, Vector3 $vector3)
    {
        $pk = new ChangeDimensionPacket();
        $pk->dimension = $dimension;
        $pk->x = $vector3->getX();
        $pk->y = $vector3->getY();
        $pk->z = $vector3->getZ();
        $this->dataPacket($pk);
    }

    /**
     * @param int $world
     */
    public function changeWorld(int $world)
    {
        switch ($world) {
            case self::NORMAL:
                $this->teleport($this->getPlugin()->getServer()->getDefaultLevel()->getSafeSpawn()->add(0, 1, 200));
                break;

            case self::NETHER:
                $this->teleport($this->getPlugin()->getServer()->getLevelByName("nether")->getSafeSpawn());
                break;

            case self::END:
                $this->teleport($this->getPlugin()->getServer()->getLevelByName("ender")->getSafeSpawn());
                break;
        }
    }

    /**
     *
     */
    public function Back()
    {
        switch ($this->getLevel()->getFolderName()) {
            case "end":
            case "ender":
                $this->changeWorld(self::NORMAL);
                break;

            case $this->getServer()->getDefaultLevel()->getName():
                $this->changeWorld(self::END);
                break;
        }
    }

    /**
     * @return mixed
     */
    public function getPlugin(): Myriad
    {
        return $this->plugin;
    }

    /**
     * @param mixed $plugin
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param string $message
     */
    public function sendLobby(string $message = "You have been sent to the Lobby.")
    {
        $data = Myriad::getManager()->getLobbyServer();
        $ip = "legacyuhc.tk";
        $port = $data["port"];
        $this->transfer($ip, $port, $message);
    }

    /**
     *
     */
    public function checkSets()
    {
        if ($this->isBard()) {
            $this->setClass("bard");
            $this->removeAllEffects();
            //Speed
            $speed = Effect::getEffect(1);
            $speed->setAmplifier(1);
            $speed->setDuration(20 * 999999);
            $this->addEffect($speed);
            //Regeneration
            $reg = Effect::getEffect(10);
            $reg->setAmplifier(0);
            $reg->setDuration(20 * 9999999);
            $this->addEffect($reg);
            //Resistance
            $res = Effect::getEffect(11);
            $res->setAmplifier(0);
            $res->setDuration(20 * 9999999);
            $this->addEffect($res);
            //Fire res
            $fres = Effect::getEffect(12);
            $fres->setAmplifier(1);
            $fres->setDuration(20 * 9999999);
            $this->addEffect($fres);
        } else
        if ($this->isMiner()) {
            $this->setClass("miner");
            $this->removeAllEffects();
            //NightVision
            $nv = Effect::getEffect(16);
            $nv->setAmplifier(3);
            $nv->setDuration(20 * 99999999);
            $this->addEffect($nv);
            //Haste
            $haste = Effect::getEffect(3);
            $haste->setAmplifier(1);
            $haste->setDuration(20 * 9999999);
            $this->addEffect($haste);
            //Fire res
            $fres = Effect::getEffect(12);
            $fres->setAmplifier(1);
            $fres->setDuration(20 * 9999999);
            $this->addEffect($fres);
             //Invisibilty
            if($this->getY() < 30) {
                $inv = Effect::getEffect(14);
                $inv->setAmplifier(1);
                $inv->setDuration(20 * 5);
                $this->addEffect($inv);
            }

        } else
        if ($this->isArcher()) {
            $this->setClass("archer");
            $this->removeAllEffects();
            //Speed
            $speed = Effect::getEffect(1);
            $speed->setAmplifier(2);
            $speed->setDuration(20 * 999999);
            $this->addEffect($speed);
            //Regeneration
            $reg = Effect::getEffect(10);
            $reg->setAmplifier(0);
            $reg->setDuration(20 * 99999);
            $this->addEffect($reg);
            //Resistance
            $res = Effect::getEffect(11);
            $res->setAmplifier(1);
            $res->setDuration(20 * 99999);
            $this->addEffect($res);
        } else {
            if($this->getClass() != "null"){
                $this->removeAllEffects();
                $this->setClass("null");
            }
        }
    }

    /**
     * @return bool
     */
    public function isBard(): bool
    {
        if(!$this->isOnline()) return false;

        $helmet = $this->getArmorInventory()->getHelmet()->getId();
        $chestplate = $this->getArmorInventory()->getChestplate()->getId();
        $leggigns = $this->getArmorInventory()->getLeggings()->getId();
        $boots = $this->getArmorInventory()->getBoots()->getId();

        return ($helmet == 314 and $chestplate == 315 and $leggigns == 316 and $boots == 317);
    }

    /**
     * @return bool
     */
    public function isMiner(): bool
    {
        $helmet = $this->getArmorInventory()->getHelmet()->getId();
        $chestplate = $this->getArmorInventory()->getChestplate()->getId();
        $leggigns = $this->getArmorInventory()->getLeggings()->getId();
        $boots = $this->getArmorInventory()->getBoots()->getId();

        return ($helmet == 306 and $chestplate == 307 and $leggigns == 308 and $boots == 309);
    }

    /**
     * @return bool
     */
    public function isArcher(): bool
    {
        $helmet = $this->getArmorInventory()->getHelmet()->getId();
        $chestplate = $this->getArmorInventory()->getChestplate()->getId();
        $leggigns = $this->getArmorInventory()->getLeggings()->getId();
        $boots = $this->getArmorInventory()->getBoots()->getId();

        return ($helmet == 298 and $chestplate == 299 and $leggigns == 300 and $boots == 301);
    }

    /**
     * @return bool
     */
    public function isAtSpawn(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isTeleporting() : bool
    {
        return $this->isteleporting;
    }

    /**
     * @param bool $isteleporting
     */
    public function setTeleporting(bool $isteleporting)
    {
        $this->isteleporting = $isteleporting;
    }

    /**
     * @return int
     */
    public function getTeleport() : int
    {
        return $this->teleporttype;
    }

    /**
     * @param int $type
     */
    public function setTeleport(int $type)
    {
        $this->teleporttype = $type;
    }

    /**
     * @param $task
     */
    public function setTeleportTask($task)
    {
        $this->teleporttask = $task;
    }

    /**
     * @return int
     */
    public function getTeleportTime(): int
    {
        return $this->teleporttask->getTime();
    }


    /**
     * @param Vector3 $pos
     * @param int $id
     * @param int $data
     */
    public function setFakeBlock(Vector3 $pos, int $id, int $data = 0)
    {
        $pk = new UpdateBlockPacket();
        $pk->blockId = $id;
        $pk->blockData = $data;
        $pk->x = (int)$pos->getX();
        $pk->y = (int)$pos->getY();
        $pk->z = (int)$pos->getZ();
        $pk->flags = UpdateBlockPacket::FLAG_NONE;
        $this->dataPacket($pk);
    }

    /**
     * @return bool
     */
    public function isTagged(): bool
    {
        return $this->tagged;
    }

    /**
     * @param bool $tagged
     */
    public function setTagged(bool $tagged)
    {
        $this->tagged = $tagged;
    }

    /**
     * @return string
     */
    public function getArcherTagger(): string
    {
        return $this->archertagger;
    }

    /**
     * @param string $tagger
     */
    public function setArcherTagger(string $tagger)
    {
        $this->archertagger = $tagger;
    }

    /**
     * @return bool
     */
    public function isArchertagged(): bool
    {
        return $this->archertagged;
    }

    /**
     * @param bool $archertagged
     */
    public function setArchertagged(bool $archertagged)
    {
        $this->archertagged = $archertagged;
    }

    /**
     * @return string
     */
    public function getLastinvite(): string
    {
        return $this->lastinvite;
    }

    /**
     * @param string $lastinvite
     */
    public function setLastinvite(string $lastinvite)
    {
        $this->lastinvite = $lastinvite;
    }

    /**
     * @return bool
     */
    public function isInvited(): bool
    {
        return $this->invited;
    }

    /**
     * @param bool $invited
     */
    public function setInvited(bool $invited)
    {
        $this->invited = $invited;
    }

    /**
     * @return mixed
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param mixed $task
     */
    public function setTask($task)
    {
        $this->task = $task;
    }

    /**
     * @return bool
     */
    public function isInClaim() : bool
    {
        $x = $this->getX();
        $z = $this->getZ();
        $result = Myriad::getFactionsManager()->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return empty($array) == false;
    }

    /**
     * @return bool
     */
    public function isSpawntag() : bool
    {
        return $this->spawntag;
    }

    /**
     * @param bool $spawntag
     */
    public function setSpawntag(bool $spawntag)
    {
        $this->spawntag = $spawntag;
    }

    /**
     * @return bool
     */
    public function isLogout(): bool
    {
        return $this->logout;
    }

    /**
     * @param bool $logout
     */
    public function setLogout(bool $logout)
    {
        $this->logout = $logout;
    }

    /**
     * @return int
     */
    public function getLogoutTime() : int
    {
        return $this->LogoutTime;
    }

    /**
     * @param int $LogoutTime
     */
    public function setLogoutTime(int $LogoutTime)
    {
        $this->LogoutTime = $LogoutTime;
    }

    /**
     *
     */
    public function spawnZombie()
    {
        ModulesManager::get(ModulesManager::LOGOUT)->spawnZombie($this);
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     */
    public function setKnockBack(int $x, int $y, int $z)
    {
        $base = 0.2;
        $f = sqrt($x * $x + $z * $z);
        if($f <= 0){
            return;
        }

        $f = 1 / $f;

        $motion = new Vector3($this->motionX, $this->motionY, $this->motionZ);

        $motion->x /= 2;
        $motion->y /= 2;
        $motion->z /= 2;
        $motion->x += $x * $f * $base;
        $motion->y += $y;
        $motion->z += $z * $f * $base;

        if($motion->y > $base){
            $motion->y = $base;
        }

        $this->setMotion($motion);
    }
    //$event->getPlayer()->knockBack($pl, 0, ($pl->x - ($block->x + 0.5)), ($pl->z - ($block->z + 0.5)), 0.2);

    /*
    public function canInteract(Vector3 $pos, $maxDistance, float $maxDiff = 0.5) : bool
    {
        $eyePos = $this->getPosition()->add(0, $this->getEyeHeight(), 0);
        if($eyePos->distanceSquared($pos) > $maxDistance ** 2){
            return false;
        }

        $dV = $this->getDirectionPlane();
        $dot = $dV->dot(new Vector2($eyePos->x, $eyePos->z));
        $dot1 = $dV->dot(new Vector2($pos->x, $pos->z));
        return ($dot1 - $dot) < $maxDiff;
    }*/


    /**
     * @param Vector3 $pos
     * @param int $max
     * @return bool
     */
    public function canReach(Vector3 $pos, int $max = 4) : bool
    {
        return $this->distance($pos) < $max;
    }

    /**
     * @param Vector3 $pos
     * @param float $max
     * @return bool
     */
    public function canAim(Vector3 $pos, float $max = 0.5) : bool
    {
        $eyePos = $this->getPosition()->add(0, $this->getEyeHeight(), 0);
        $dV = $this->getDirectionPlane();
        $dot = $dV->dot(new Vector2($eyePos->x, $eyePos->z));
        $dot1 = $dV->dot(new Vector2($pos->x, $pos->z));
        return ($dot1 - $dot) < $max;
    }

    /**
     * @param Vector3 $pos
     * @return float
     */
    public function getAim(Vector3 $pos) : float
    {
        $eyePos = $this->getPosition()->add(0, $this->getEyeHeight(), 0);
        $dV = $this->getDirectionPlane();
        $dot = $dV->dot(new Vector2($eyePos->x, $eyePos->z));
        $dot1 = $dV->dot(new Vector2($pos->x, $pos->z));
        return $dot1 - $dot;
    }

    /**
     * @return int
     */
    public function getAimhack() : int
    {
        return $this->aimhack;
    }

    /**
     * @param int $aimhack
     */
    public function setAimhack(int $aimhack)
    {
        $this->aimhack = $aimhack;
    }

    /**
     * @param int $aimhack
     */
    public function addAimhack(int $aimhack = 1)
    {
        $this->setAimhack($this->getAimhack() + $aimhack);
    }

    /**
     * @param bool $value
     */
    public function setBanned(bool $value)
    {
        /*
        if($value){
            $this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SaveDataTask($this->getName(), "Banned", 1, SaveDataTask::SET));
        } else {
            $this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SaveDataTask($this->getName(), "Banned", 0, SaveDataTask::SET));
        }*/
        $this->getServer()->getNameBans()->addBan($this->getName(), null,null, null);
    }

    /**
     * @param bool $value
     */
    public function setDeathBanned(bool $value)
    {
        if($value){
            $this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SaveDataTask($this->getName(), "DeathBanned", 1, SaveDataTask::SET));
        } else {
            $this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new SaveDataTask($this->getName(), "DeathBanned", 0, SaveDataTask::SET));
        }
    }

    /**
     * @return int
     */
    public function getReach() : int
    {
        return $this->reach;
    }

    /**
     * @param int $reach
     */
    public function setReach(int $reach)
    {
        $this->reach = $reach;
    }

    /**
     * @param int $reach
     */
    public function addReach(int $reach = 1)
    {
        $this->setReach($this->getReach() + $reach);
    }

    /**
     * @return float
     */
    public function getLastclick() : float
    {
        return $this->lastclick;
    }

    /**
     * @param float $lastclick
     */
    public function setLastclick(float $lastclick = 0.0)
    {
        if($lastclick == 0.0){
            $lastclick = microtime(true);
        }
        $this->lastclick = $lastclick;
    }

    /**
     * @return Vector3
     */
    public function getPos2 () : Vector3
    {
        return $this->pos2;
    }

    /**
     * @param Vector3 $pos2
     */
    public function setPos2 (Vector3 $pos2)
    {
        $this->pos2 = $pos2;
    }

    /**
     * @return Vector3
     */
    public function getPos1 () : Vector3
    {
        return $this->pos1;
    }

    /**
     * @param Vector3 $pos1
     */
    public function setPos1 (Vector3 $pos1)
    {
        $this->pos1 = $pos1;
    }

    /**
    * @return bool
    */
    public function isClaiming () : bool
    {
        return $this->claiming;
    }
    /**
    * @param bool $claiming
    */
    public function setClaiming (bool $claiming)
    {
        $this->claiming = $claiming;
    }

    /**
     *
     */
    public function checkClaim()
    {
        $dir = $this->getPlugin()->getDataFolder() . "Data.db";


        $pos1 = $this->getPos1();
        $pos2 = $this->getPos2();

        $x1 = min($pos1->getX(), $pos2->getX());
        $z1 = min($pos1->getZ(), $pos2->getZ());

        $x2 = max($pos1->getX(), $pos2->getX());
        $z2 = max($pos1->getZ(), $pos2->getZ());

        $this->getPlugin()->getServer()->getScheduler()->scheduleAsyncTask(new CheckClaimTask($x1, $z1, $x2, $z2, $dir, $this->getName()));
    }

    /**
     *
     */
    public function checkLast()
    {
        $x = $this->getPosition()->getFloorX();
        $y = $this->getPosition()->getFloorY();
        $z = $this->getPosition()->getFloorZ();

        $new = $this->getPosition();

        if($new->distance($this->getLast()) > 1){
            $this->setLast(new Vector3($x, $y, $z));
        }
    }

    /**
     * @return Vector3
     */
    public function getLast () : Vector3
    {
        return $this->last;
    }

    /**
     * @param Vector3 $last
     */
    public function setLast (Vector3 $last)
    {
        $this->last = $last;
    }

    /**
     *
     */
    public function addToMySql()
    {
        /*
        $db = @mysqli_connect("127.0.0.1", "root", "root", "hcf");
        if($db){
            if($db->query('INSERT OR REPLACE INTO Player(playername, DeathBanned, Banned) VALUES (' . $this->getName() . ', 0, 0)')) {
                $this->getPlugin()->getLogger()->debug("Successfully added the player " . $this->getName() . " to the database!");
            }
        }*/
    }

    /**
     * @return int
     */
    public function getStep () : int
    {
        return $this->step;
    }

    /**
     * @param int $step
     */
    public function setStep (int $step)
    {
        $this->step = $step;
    }

    public function getRandWallBlock() : int
    {
        switch (mt_rand(1, 3)){
            case 1:
            case 2:
                return BlockIds::GLASS;
            break;

            case 3:
                return BlockIds::EMERALD_BLOCK;
            break;
        }
        return BlockIds::GLASS;
    }

    public function buildWall(int $x, int $y, int $z)
    {
        for ($i = $y; $i < $y + 20; $i++){
            $this->setFakeBlock(new Vector3($x, $i, $z), $this->getRandWallBlock());
        }
    }

    /**
     * @param bool $claim
     */
    public function setClaim(bool $claim)
    {
        $this->claim["claim"] = $claim;
    }

    /**
     * @return bool
     */
    public function getClaim() : bool
    {
        return $this->claim["claim"];
    }

    /**
     * @return int
     */
    public function getClaimCost() : int
    {
        return $this->claim["cost"];
    }

    /**
     * @param int $cost
     */
    public function setClaimCost(int $cost)
    {
        $this->claim["cost"] = $cost;
    }

    /**
     * @param int $time
     */
    public function disableMovement(int $time)
    {
        $this->disablemovement = $time;
    }

    /**
     * @return bool
     */
    public function isMovementDisabled() : bool
    {
        $time = time();

        return ($time - $this->disablemovement) < 0;
    }

    /**
     * @return string
     */
    public function getCurrentRegion() : string
    {
        if(Myriad::getFactionsManager()->isSpawnClaim($this)){
            return "Spawn";
        } else
            if(Myriad::getFactionsManager()->isClaim($this)){
                return Myriad::getFactionsManager()->getClaimer($this->x, $this->z);
            } else {
                return "Wildness";
            }
    }

    /**
     * @return string
     */
    public function getRegion () : string
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion (string $region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getClass () : string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass (string $class)
    {
        $this->class = $class;
        //$this->sendMessage(Utils::getPrefix() . TextFormat::GRAY . "new class -> " . TextFormat::WHITE . $class);
    }

    /**
     * @return Vector3
     */
    public function getSign() : Vector3
    {
        return $this->sign;
    }

    /**
     * @param Vector3 $sign
     */
    public function setSign(Vector3 $sign)
    {
        $this->sign = $sign;
    }

    /**
     * @return bool
     */
    public function isCoords() : bool
    {
        return $this->coords;
    }

    /**
     * @param bool $coords
     */
    public function setCoords(bool $coords)
    {
        $this->coords = $coords;
    }

    /**
     * @return int
     */
    public function getTagtime() : int
    {
        return $this->tagtime;
    }

    /**
     * @param int $tagtime
     */
    public function setTagtime(int $tagtime)
    {
        $this->tagtime = $tagtime;
    }

    /**
     * @return bool
     */
    public function isKit() : bool
    {
        return $this->kit;
    }

    /**
     * @param bool $kit
     */
    public function setKit(bool $kit)
    {
        $this->kit = $kit;
    }

    /**
     *
     */
    public function checkProxy()
    {
        $this->getServer()->getScheduler()->scheduleAsyncTask(new CheckProxyTask($this->getName(), $this->getAddress()));
    }

    /**
     * @return int
     */
    public function getChat() : int
    {
        return $this->chat;
    }

    /**
     * @param int $chat
     */
    public function setChat(int $chat)
    {
        $this->chat = $chat;
    }

    /**
     * @return int
     */
    public function getFlyHack(): int
    {
        return $this->flyhack;
    }

    /**
     * @param int $flyhack
     */
    public function setFlyHack(int $flyhack)
    {
        $this->flyhack = $flyhack;
    }

    public function addFlyHack()
    {
        $this->setFlyHack($this->getFlyHack() + 1);
    }

    /**
     * @return bool
     */
    public function isStaff() : bool
    {
        return $this->staff;
    }

    /**
     * @param bool $staff
     */
    public function setStaff(bool $staff)
    {
        $this->staff = $staff;
    }

    /**
     * @return array
     */
    public function getOldinv() : array
    {
        return $this->oldinv;
    }

    /**
     * @param array $oldinv
     */
    public function setOldinv(array $oldinv)
    {
        $this->oldinv = $oldinv;
    }

    public function addStaff()
    {
        $this->setStaff(true);
        $this->setOldinv($this->getInventory()->getContents());
        $this->getInventory()->clearAll();

        $freeze = Item::get(ItemIds::FROSTED_ICE, 0, 1)->setCustomName(TextFormat::RESET . TextFormat::AQUA . "Freeze");
        $vanish = Item::get(ItemIds::DYE, 10, 1)->setCustomName(TextFormat::RESET . TextFormat::GREEN . "Vanish");
        $randtp = Item::get(ItemIds::CLOCK, 0, 1)->setCustomName(TextFormat::RESET . TextFormat::YELLOW . "Random TP");
        $neartp = Item::get(ItemIds::COMPASS, 0, 1)->setCustomName(TextFormat::RESET . TextFormat::GRAY . "TP Nearest Player");

        $this->setGamemode(self::ADVENTURE);
        $this->getInventory()->setItem(0, $vanish);
        $this->getInventory()->setItem(1, $freeze);
        $this->getInventory()->setItem(2, $randtp);
        $this->getInventory()->setItem(3, $neartp);
        $this->setFlying(true);
        $this->setAllowFlight(true);
        $this->setAllowMovementCheats(true);
    }

    public function removeStaff()
    {
        $this->setStaff(false);
        $this->setGamemode(self::SURVIVAL);
        $this->setVanish(false);
        $this->getInventory()->setContents($this->getOldinv());
        $this->setFlying(false);
        $this->setAllowMovementCheats(false);
    }

    /**
     * @return bool
     */
    public function isVanish() : bool
    {
        return $this->vanish;
    }

    /**
     * @param bool $vanish
     */
    public function setVanish(bool $vanish)
    {
        $this->vanish = $vanish;
    }

    /**
     * @return bool
     */
    public function isFreeze() : bool
    {
        return $this->freeze;
    }

    /**
     * @param bool $freeze
     */
    public function setFreeze(bool $freeze)
    {
        $this->freeze = $freeze;
    }

    public function checkVanish()
    {
        if($this->isVanish()){
            foreach ($this->getServer()->getOnlinePlayers() as $player){
                if($player instanceof MDPlayer){
                    if(!$player->isStaff()){
                        $player->hidePlayer($this);
                    }
                }
            }
        } else {
            foreach ($this->getServer()->getOnlinePlayers() as $player){
                if($player instanceof MDPlayer){
                    $player->showPlayer($this);
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getBardEnergy(): int
    {
        return $this->bardenergy;
    }

    /**
     * @param int $bardEnergy
     */
    public function setBardEnergy(int $bardEnergy)
    {
        $this->bardenergy = $bardEnergy;
    }

    /**
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @return bool
     */
    public function isOnPos(Vector3 $pos1, Vector3 $pos2)
    {
        $minX = min($pos1->getX(), $pos2->getX());
        $maxX = max($pos1->getX(), $pos2->getX());

        $minY = min($pos1->getY(), $pos2->getY());
        $maxY = max($pos1->getY(), $pos2->getY());

        $minZ = min($pos1->getZ(), $pos2->getZ());
        $maxZ = max($pos1->getZ(), $pos2->getZ());

        return ($this->getX() <= $maxX and $this->getX() >= $minX and $this->getY() <= $maxY and $this->getY() >= $minY and $this->getZ() <= $maxZ and $this->getZ() >= $minZ);
    }

    public function reclaim()
    {
        switch ($this->getRank()){
            case self::DEFAULT:
                $this->sendMessage(Utils::getPrefix() . TextFormat::RED . "You must have a rank to get access to this feature!");
                break;

            case self::BRONZE:
                if ((time() - $this->getReclaim()) > Utils::DAY_TIME){
                    Myriad::getCrate()->giveKey($this, 1, 1);
                    $this->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully claimed your keys!");
                    $this->resetReclaim();
                } else $this->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already claimed your daily keys!");
                break;

            case self::SILVER:
                if ((time() - $this->getReclaim()) > Utils::DAY_TIME){
                    Myriad::getCrate()->giveKey($this, 1, 2);
                    $this->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully claimed your keys!");
                    $this->resetReclaim();
                } else $this->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already claimed your daily keys!");
                break;

            case self::GOLD:
                if ((time() - $this->getReclaim()) > Utils::DAY_TIME){
                    Myriad::getCrate()->giveKey($this, 2, 1);
                    $this->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully claimed your keys!");
                    $this->resetReclaim();
                } else $this->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already claimed your daily keys!");
                break;

            case self::DIAMOND:
                if ((time() - $this->getReclaim()) > Utils::DAY_TIME){
                    Myriad::getCrate()->giveKey($this, 2, 2);
                    $this->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully claimed your keys!");
                    $this->resetReclaim();
                } else $this->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already claimed your daily keys!");
                break;

            case self::LEGACY:
                if ((time() - $this->getReclaim()) > Utils::DAY_TIME){
                    Myriad::getCrate()->giveKey($this, 1, 2);
                    Myriad::getCrate()->giveKey($this, 2, 2);
                    $this->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully claimed your keys!");
                    $this->resetReclaim();
                } else $this->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already claimed your daily keys!");
                break;
            default:
            if ((time() - $this->getReclaim()) > Utils::DAY_TIME){
                Myriad::getCrate()->giveKey($this, 1, 2);
                Myriad::getCrate()->giveKey($this, 2, 2);
                $this->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully claimed your keys!");
                $this->resetReclaim();
            } else $this->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already claimed your daily keys!");
            break;
        }
    }

}
