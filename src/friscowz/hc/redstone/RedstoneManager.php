<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/17/2017
 * Time: 12:57 AM
 */

namespace friscowz\hc\redstone;


use friscowz\hc\redstone\block\StonePressurePlate;
use friscowz\hc\redstone\block\WoodenPressurePlate;
use pocketmine\block\BlockFactory;
use pocketmine\math\Vector3;

class RedstoneManager
{
    public function __construct()
    {
        BlockFactory::registerBlock(new StonePressurePlate(), true);
        BlockFactory::registerBlock(new WoodenPressurePlate(), true);
    }
}