<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 19:30
 */

namespace friscowz\hc;


use friscowz\hc\task\FactionTask;
use friscowz\hc\utils\Utils;
use pocketmine\block\BlockIds;
use pocketmine\block\utils\ColorBlockMetaHelper;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class FactionsManager
{
    private $plugin;
    private $db;
    private $frozens = [];

    const CLAIM = 0;
    const SPAWN = 1;
    const PROTECTED = 2;

    const MEMBER = 0;
    const OFFICER = 1;
    const LEADER = 2;

    /**
     * FactionsManager constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $db = new \SQLite3($this->getPlugin()->getDataFolder() . "Data.db");
        $this->setDb($db);
        new FactionTask($plugin);
        $this->getDb()->exec("CREATE TABLE IF NOT EXISTS claims(faction TEXT PRIMARY KEY, type INT, x1 INT, z1 INT, x2 INT, z2 INT);");
        $this->getDb()->exec("CREATE TABLE IF NOT EXISTS players(name TEXT PRIMARY KEY, rank INT, faction TEXT);");
        $this->getDb()->exec("CREATE TABLE IF NOT EXISTS homes(name TEXT PRIMARY KEY, x INT, y INT, z INT);");
        $this->getDb()->exec("CREATE TABLE IF NOT EXISTS dtrs(name TEXT PRIMARY KEY, dtr DOUBLE);");
        $this->getDb()->exec("CREATE TABLE IF NOT EXISTS balances(name TEXT PRIMARY KEY, balance INT);");
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
     * @param string $name
     * @return bool
     */
    public function isFaction(string $name): bool
    {
        $result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return !empty($array);
    }

    /**
     * @param string $name
     * @param MDPlayer|Player $creator
     */
    public function createFaction(string $name, MDPlayer $creator)
    {
        if ($this->isFaction($name)) {
            $creator->sendMessage(Utils::getPrefix() . TextFormat::RED . "This Faction's name already exist!");
        } else {
            $creator->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "Faction successfully created !");
            $creator->addToFaction($name, self::LEADER);
            $this->setBalance($name, 0);
            $this->setDTR($name, 5);
            $this->getPlugin()->getServer()->broadcastMessage(Utils::getPrefix() . TextFormat::GRAY . "The Faction " . $name . " was created by " . $creator->getName());
        }
    }

    /**
     * @param string $name
     * @return array
     */
    public function getMembers(string $name): array
    {
        $members = [];
        $result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name';");
        while ($array = $result->fetchArray(SQLITE3_ASSOC)) {
            $members[] = $array["name"];
        }
        return $members;
    }

    /**
     * @param string $name
     * @param string $player
     * @return bool
     */
    public function isMember(string $name, string $player): bool
    {
        return in_array($player, $this->getMembers($name));
    }

    public function kick(string $name)
    {
        Myriad::getFactionsManager()->getDb()->exec("DELETE FROM players WHERE name = '$name';");
    }

    /**
     * @param string $name
     * @return int
     */
    public function getDTR(string $name): int
    {
        $result = $this->getDb()->query("SELECT * FROM dtrs WHERE name = '$name';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return $array["dtr"];
    }

    /**
     * @param string $name
     * @param int $amount
     */
    public function setDTR(string $name, int $amount)
    {
        $this->getDb()->exec("INSERT OR REPLACE INTO dtrs(name, dtr) VALUES ('$name', " . $amount . ");");
    }

    /**
     * @param string $name
     */
    public function reduceDTR(string $name)
    {
        $dtr = $this->getDTR($name);
        if ($dtr == 1) {
            $this->setDTR($name, 0);
            $this->setFrozenTime($name, time() + (60 * 30));
            $this->getDb()->exec("DELETE FROM claims WHERE faction = '$name';");
            foreach ($this->getOnlineMembers($name) as $member) {
                if ($member instanceof MDPlayer) {
                    $member->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your faction went raidable!");
                }
            }
        } elseif ($dtr == 0) {
            //Nothing
        } else {
            $this->setDTR($name, $dtr - 1);
            $this->setFrozenTime($name, time() + (60 * 30));
            foreach ($this->getOnlineMembers($name) as $member) {
                if ($member instanceof MDPlayer) {
                    $member->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your faction lost -1 DTR!");
                }
            }
        }
    }

    /**
     * @param string $name
     */
    public function addDTR(string $name)
    {
        $dtr = $this->getDTR($name);
        if ($dtr < $this->getMaxDTR($name)) {
            $this->setDTR($name, $dtr + 1);
            foreach ($this->getOnlineMembers($name) as $member) {
                if ($member instanceof MDPlayer) {
                    $member->sendMessage(TextFormat::GREEN . "+1 DTR.");
                }
            }
        }
    }

    /**
     * @param string $name
     * @return int
     */
    public function getMaxDTR(string $name): int
    {
        $count = count($this->getMembers($name));
        $max = 5;
        if ($count >= 4) {
            $max = 5;
        } else {
            $max = $count + 1;
        }
        if ($this->getDTR($name) > $max) {
            $this->setDTR($name, $max);
        }
        return $max;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getLeader(string $name): string
    {
        $result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name' AND rank = 2;");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return strval($array["name"]);
    }

    /**
     * @param string $name
     * @param string $leader
     */
    public function setLeader(string $name, string $leader)
    {
        $this->getDb()->exec("INSERT OR REPLACE INTO players(name, rank, faction) VALUES ('$leader', " . self::LEADER . ", '$name');");
    }

    /**
     * @param string $name
     * @param int $amount
     */
    public function setBalance(string $name, int $amount)
    {
        $this->getDb()->exec("INSERT OR REPLACE INTO balances(name, balance) VALUES ('$name', " . $amount . ");");
    }

    /**
     * @param string $name
     * @return int
     */
    public function getBalance(string $name): int
    {
        $result = $this->getDb()->query("SELECT * FROM balances WHERE name = '$name';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return intval($array["balance"]);
    }

    /**
     * @param string $name
     * @param int $amount
     */
    public function addBalance(string $name, int $amount)
    {
        $this->setBalance($name, $this->getBalance($name) + $amount);
    }

    /**
     * @param string $name
     * @param int $amount
     */
    public function reduceBalance(string $name, int $amount)
    {
        $this->setBalance($name, $this->getBalance($name) - $amount);
    }

    /**
     * @param string $name
     */
    public function disbandFaction(string $name)
    {
        $this->getDb()->exec("DELETE FROM players WHERE faction = '$name';");
        $this->getDb()->exec("DELETE FROM homes WHERE name = '$name';");
        $this->getDb()->exec("DELETE FROM balances WHERE name = '$name';");
        $this->getDb()->exec("DELETE FROM dtrs WHERE name = '$name';");
        $this->getDb()->exec("DELETE FROM claims WHERE faction = '$name';");

        foreach ($this->getOnlineMembers($name) as $member) {
            $member = $this->getPlugin()->getServer()->getPlayer($member);
            $member->sendMessage(Utils::getPrefix() . TextFormat::RED . "Your faction got disbanded!");
            $member->removeFromFaction();
        }
    }

    /**
     * @param string $name
     * @return MDPlayer[]
     */
    public function getOnlineMembers(string $name): array
    {
        $onlines = [];
        $result = $this->getDb()->query("SELECT * FROM players WHERE faction = '$name';");
        while ($array = $result->fetchArray(SQLITE3_ASSOC)) {
            if ($player = $this->getPlugin()->getServer()->getPlayer($array["name"])) {
                $onlines[] = $player;
            }
        }
        return $onlines;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isHome(string $name): bool
    {
        $result = $this->getDb()->query("SELECT * FROM homes WHERE name = '$name';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return !empty($array);
    }

    /**
     * @param string $name
     * @return Vector3
     */
    public function getHome(string $name): Vector3
    {
        $result = $this->getDb()->query("SELECT * FROM homes WHERE name = '$name';");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return new Vector3($array["x"], $array["y"], $array["z"]);
    }

    /**
     * @param string $name
     * @param Position $pos
     */
    public function setHome(string $name, Position $pos)
    {
        $this->getDb()->exec("INSERT OR REPLACE INTO homes(name, x, y, z) VALUES ('$name', " . $pos->getFloorX() . ", " . $pos->getFloorY() . ", " . $pos->getFloorZ() . ");");
    }

    /**
     * @return array
     */
    public function getAllFactions(): array
    {
        $result = $this->getDb()->query("SELECT * FROM players;");
        $all = [];
        while ($fac = $result->fetchArray(SQLITE3_ASSOC)) {
            $all[] = $fac["faction"];
        }
        return $all;
    }

    /**
     * @return \SQLite3
     */
    public function getDb(): \SQLite3
    {
        //$db = new BetterSQLite3($this->getPlugin()->getDataFolder() . "Data.db");
        return $this->db;
    }

    /**
     * @param \SQLite3 $db
     */
    public function setDb(\SQLite3 $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $name
     * @param Vector3 $pos1
     * @param Vector3 $pos2
     * @param int $type
     */
    public function claim(string $name, Vector3 $pos1, Vector3 $pos2, int $type = self::CLAIM)
    {

        $x1 = max($pos1->getX(), $pos2->getX());
        $z1 = max($pos1->getZ(), $pos2->getZ());

        $x2 = min($pos1->getX(), $pos2->getX());
        $z2 = min($pos1->getZ(), $pos2->getZ());

        $db = $this->getDb()->prepare("INSERT OR REPLACE INTO claims (faction, type, x1, z1, x2, z2) VALUES (:faction, :type, :x1, :z1, :x2, :z2);");
        $db->bindValue(":faction", $name);
        $db->bindValue(":type", $type);
        $db->bindValue(":x1", $x1);
        $db->bindValue(":z1", $z1);
        $db->bindValue(":x2", $x2);
        $db->bindValue(":z2", $z2);
        $result = $db->execute();
    }

    /**
     * @param Vector3 $pos
     * @return bool
     */
    public function isClaim(Vector3 $pos): bool
    {
        $x = $pos->getX();
        $z = $pos->getZ();
        /*
        if($this->isRoad($x, $z) and !$this->isSpawnClaim($pos)){
            return true;
        }*/
        $result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return empty($array) == false;
    }

    /**
     * @param Vector3 $pos
     * @return bool
     */
    public function isSpawnClaim(Vector3 $pos): bool
    {
        $x = $pos->getX();
        $z = $pos->getZ();
        $result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2 AND type = 1;");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return empty($array) == false;
    }

    /**
     * @param Vector3 $pos
     * @return bool
     */
    public function isFactionClaim(Vector3 $pos): bool
    {
        $x = $pos->getX();
        $z = $pos->getZ();
        $result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2 AND type = 0;");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        if (empty($array) == false) {
            if ($this->getDTR($array["faction"]) == 0) {
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param int $x
     * @param int $z
     * @return string
     */
    public function getClaimer(int $x, int $z): string
    {
        /*
        if($this->isRoad($x, $z) and !$this->isSpawnClaim(new Vector3($x, 0, $z))){
            return "Road";
        }*/
        $result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return $array["faction"];
    }

    public function isRoad($x, $z): bool
    {
        if ($x <= 10 and $x >= -10) {
            return true;
        }
        if ($z <= 10 and $z >= -10) {
            return true;
        }
        return false;
    }

    /**
     * @return array|null
     */
    public function getAllClaims():?array
    {
        $result = $this->getDb()->query("SELECT * FROM claims;");
        $array = [];
        while ($one = $result->fetchArray(SQLITE3_ASSOC)) {
            $array[] = $one;
        }
        return $array;
    }

    /**
     * @param int $x
     * @param int $z
     * @return int
     */
    public function getClaimType(int $x, int $z): int
    {
        /*
        if($this->isRoad($x, $z) and !$this->isSpawnClaim(new Vector3($x, 0, $z))){
            return self::PROTECTED;
        }*/
        $result = $this->getDb()->query("SELECT * FROM claims WHERE $x <= x1 AND $x >= x2 AND $z <= z1 AND $z >= z2;");
        $array = $result->fetchArray(SQLITE3_ASSOC);
        return $array["type"];
    }

    public function showMap(Player $player)
    {
        $x = $player->getFloorX();
        $y = $player->getFloorY();
        $z = $player->getFloorZ();
        $result = $this->getDb()->query("SELECT * FROM claims;");
        $data = 0;
        while ($one = $result->fetchArray(SQLITE3_ASSOC)) {
            $pos1 = new Vector3($one["x1"], $y, $one["z1"]);
            $pos2 = new Vector3($one["x2"], $y, $one["z2"]);
            if ($player->distanceSquared($pos1) < 16 or $player->distanceSquared($pos2) < 16) {
                $name = ColorBlockMetaHelper::getColorFromMeta($data);
                $player->sendMessage(Utils::getPrefix() . TextFormat::GRAY . $name . " for " . $one["faction"]);
                for ($i = $player->getLevel()->getHighestBlockAt($pos1->getX(), $pos1->getZ()) + 1; $i < 126; $i++) {
                    $pk = new UpdateBlockPacket();
                    $pk->blockId = BlockIds::STAINED_GLASS;
                    $pk->blockData = $data;
                    $pk->x = (int)$pos1->getX();
                    $pk->y = (int)$i;
                    $pk->z = (int)$pos1->getZ();
                    $pk->flags = UpdateBlockPacket::FLAG_NONE;
                    $player->dataPacket($pk);
                }

                for ($i = $player->getLevel()->getHighestBlockAt($pos2->getX(), $pos2->getZ()) + 1; $i < 126; $i++) {
                    $pk = new UpdateBlockPacket();
                    $pk->blockId = BlockIds::STAINED_GLASS;
                    $pk->blockData = $data;
                    $pk->x = (int)$pos2->getX();
                    $pk->y = (int)$i;
                    $pk->z = (int)$pos2->getZ();
                    $pk->flags = UpdateBlockPacket::FLAG_NONE;
                    $player->dataPacket($pk);
                }
                ++$data;
            }
        }
    }

    /**
     * @return array
     */
    public function getFrozens(): array
    {
        return $this->frozens;
    }

    /**
     * @param array $frozens
     */
    public function setFrozens(array $frozens)
    {
        $this->frozens = $frozens;
    }

    /**
     * @param string $faction
     * @return bool
     */
    public function isFrozen(string $faction): bool
    {
        if (!isset($this->getFrozens()[$faction])) {
            $this->setFrozenTime($faction, 0);
        }
        if ($this->getFrozenTimeLeft($faction) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $faction
     * @param int $time
     */
    public function setFrozenTime(string $faction, int $time)
    {
        $this->frozens[$faction] = $time;
    }

    /**
     * @param string $faction
     * @return int
     */
    public function getFrozenTime(string $faction): int
    {
        return $this->frozens[$faction];
    }

    /**
     * @param string $faction
     * @return int
     */
    public function getFrozenTimeLeft(string $faction): int
    {
        $time = $this->getFrozenTime($faction) - time();
        return $time;
    }

}