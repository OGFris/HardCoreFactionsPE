<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/29/2017
 * Time: 3:11 PM
 */

namespace friscowz\hc\border;

use pocketmine\level\Level;
use pocketmine\math\Vector3;

class Border
{
    private $x;
    private $z;

    private $radius;

    private $maxX;
    private $maxZ;

    private $minX;
    private $minZ;

    private $safeBlocks;
    private $unsafeBlocks;

    private $level;

    /**
     * Border constructor.
     * @param $x
     * @param $z
     * @param $radius
     * @param Level $level
     */
    public function __construct($x, $z, $radius, Level $level){

        $this->level = $level;

        $this->x = $x;
        $this->z = $z;

        $this->maxX = $x + $radius;
        $this->minX = $x - $radius;

        $this->maxZ = $z + $radius;
        $this->minZ = $z - $radius;

        $this->radius = $radius;

        $this->safeBlocks = [
            0, 6, 8, 9, 27, 30, 31, 32, 37,
            38, 39, 40, 50, 59, 63, 64, 65,
            66, 68, 71, 78, 83, 104, 105, 106,
            141, 142, 171, 244
        ];

        $this->unsafeBlocks = [10, 11, 51, 81];
    }

    /**
     * @return mixed
     */
    public function getX() : int {
        return $this->x;
    }

    /**
     * @return mixed
     */
    public function getZ() : int{
        return $this->z;
    }

    /**
     * @param $x
     */
    public function setX($x){
        $this->x = $x;
        $this->maxX = $x + $this->radius;
        $this->minX = $x - $this->radius;
    }

    /**
     * @param $z
     */
    public function setZ($z){
        $this->z = $z;
        $this->maxZ = $z + $this->radius;
        $this->minZ = $z - $this->radius;
    }

    /**
     * @param $radius
     */
    public function setRadiusX($radius){
        $this->radius = $radius;
        $this->maxX = $this->x + $radius;
        $this->minX = $this->x - $radius;
    }

    /**
     * @param $radius
     */
    public function setRadiusZ($radius){
        $this->radius = $radius;
        $this->maxZ = $this->z + $radius;
        $this->minZ = $this->z - $radius;
    }

    /**
     * @param $x
     * @param $z
     * @param $radius
     */
    public function changeBorder($x, $z, $radius){
        $this->x = $x;
        $this->z = $z;

        $this->maxX = $x + $radius;
        $this->minX = $x - $radius;

        $this->maxZ = $z + $radius;
        $this->minZ = $z - $radius;

        $this->radius = $radius;
    }


    /**
     * @return mixed
     */
    public function getBorder() : int {
        return $this->radius;
    }

    /**
     * @param int $int
     */
    public function setBorder($int){
        $this->radius = $int;
        $x = 0;
        $z = 0;
        $this->changeBorder($x, $z, $int);
        $this->buildBorder();
    }

    /**
     * @param $x
     * @param $z
     * @return bool
     */
    public function insideBorder($x, $z) : bool {
        return $x > $this->minX and $x < $this->maxX and $z > $this->minZ and $z < $this->maxZ;
    }



    /**
     * @param $location
     * @return \pocketmine\math\Vector3
     */
    public function correctPosition($location) : Vector3 {

        $knockback = 4.0;

        $x = $location->getX();
        $z = $location->getZ();
        $y = $location->getY();

        if($x <= $this->minX){
            $x = $this->minX + $knockback;
        }
        elseif($x >= $this->maxX){
            $x = $this->maxX - $knockback;
        }

        if($z <= $this->minZ){
            $z = $this->minZ + $knockback;
        }
        elseif($z >= $this->maxZ){
            $z = $this->maxZ - $knockback;
        }

        $y = $this->findSafeY($location->getLevel(), $x, $y, $z);

        if($y < 10){
            $y =  70;
        }
        if($this->radius === 25){
            $x = $location->getLevel()->getSpawnLocation()->getX();
            $y = $location->getLevel()->getSpawnLocation()->getY();
            $z = $location->getLevel()->getSpawnLocation()->getZ();
        }
        return new Vector3($x, $y, $z);
    }

    /**
     * @param Level $level
     * @param $x
     * @param $y
     * @param $z
     * @return int
     */
    private function findSafeY(Level $level, $x, $y, $z) : int {

        $top = $level->getHeightMap($x, $z) - 2;
        $bottom = 1;

        for($y1 = $y, $y2 = $y; ($y1 > $bottom) or ($y2 < $top); $y1--, $y2++){

            if($y1 > $bottom){
                if($this->isSafe($level, $x, $y1, $z)) return $y1;
            }

            if($y2 < $top and $y2 != $y1){
                if($this->isSafe($level, $x, $y2, $z)) return $y2;
            }

        }

        return -1;

    }

    /**
     * @param Level $level
     * @param $x
     * @param $y
     * @param $z
     * @return bool
     */
    private function isSafe(Level $level, $x, $y, $z) : bool{

        $safe = in_array($level->getBlockIdAt($x, $y, $z), $this->safeBlocks) && in_array($level->getBlockIdAt($x, $y + 1, $z), $this->safeBlocks);

        if(!$safe) return $safe;

        $below = $level->getBlockIdAt($x, $y - 1, $z);

        return ($safe and (!in_array($below, $this->safeBlocks) or $below === 8 or $below === 9) and !in_array($below, $this->unsafeBlocks));

    }
}