<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/29/2017
 * Time: 3:03 PM
 */

namespace friscowz\hc\commands;


use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use friscowz\hc\Myriad;

class ChunksCommand extends PluginCommand
{
    /**
     * ChunksCommand constructor.
     * @param Myriad $owner
     */
    public function __construct(Myriad $owner)
    {
        parent::__construct("chunks", $owner);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender->isOp()){
            $chunksCollected = 0;
            $entitiesCollected = 0;
            $tilesCollected = 0;

            $memory = memory_get_usage();

            foreach($sender->getServer()->getLevels() as $level){
                echo $level->getName() . ":" . PHP_EOL;
                $diff = [count($level->getChunks()), count($level->getEntities()), count($level->getTiles())];
                $level->doChunkGarbageCollection();
                echo "doChunkGarbage done!" . PHP_EOL;
                $level->unloadChunks(true);
                echo "unloadChunks done!" . PHP_EOL;
            }
        }
    }
}