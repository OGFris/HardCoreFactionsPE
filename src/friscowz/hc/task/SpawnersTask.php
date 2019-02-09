<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 11/18/2017
 * Time: 1:23 AM
 */

namespace friscowz\hc\task;


use friscowz\hc\item\SplashPotion;
use friscowz\hc\tiles\PotionSpawner;
use friscowz\hc\utils\Utils;
use friscowz\hc\Myriad;
use pocketmine\entity\Human;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Chest;
use pocketmine\utils\TextFormat;

class SpawnersTask extends PluginTask
{
    private $plugin;
    private $time = 120;

    public function __construct(Myriad $plugin)
    {
        parent::__construct($plugin);
        $this->setPlugin($plugin);
        $plugin->getServer()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    /**
     * Actions to execute when run
     *
     * @param int $currentTick
     *
     * @return void
     */
    public function onRun(int $currentTick)
    {
        --$this->time;
        if ($this->time == 0) {
            $this->time = 120;
			$message = [Utils::getPrefix() . TextFormat::BOLD . TextFormat::GOLD . "Buy ranks @ myriadhcf.buycraft.net !" . TextFormat::RESET, Utils::getPrefix() . TextFormat::BOLD . TextFormat::GOLD . "Vote for us @ vote.legacyhcf.net for a chance to Win a rank !" . TextFormat::RESET, Utils::getPrefix() . TextFormat::BOLD . TextFormat::BLUE . "Make you sure to Follow @LegacyUHCs and @OGFris on twitter !" . TextFormat::RESET][mt_rand(0, 2)];
            $this->getPlugin()->getServer()->broadcastMessage($message);

            foreach ($this->getPlugin()->getServer()->getLevels() as $level) {
                $count = 0;
                foreach ($level->getEntities() as $entity) {
                    if (!$entity instanceof Human) {
                        $entity->close();
                    }
                }
                //$this->getPlugin()->getLogger()->info("Closed $count entity.");
                $count = 0;
                $tiles = 0;
                foreach ($level->getTiles() as $tile) {
                    if ($tile instanceof PotionSpawner) {
                        //$level->dropItem($tile->add(0, 1), new SplashPotion(22, mt_rand(1, 2)));
                        $tile = $level->getTile($tile->subtract(0, 1));
                        if ($tile instanceof Chest) {
                            $tile->getInventory()->addItem(new SplashPotion(22, mt_rand(1, 2)));
                        }
                    }
                }
                /*
                foreach ($level->getTiles() as $tile){
                        ++$tile;
                        $nbt = Entity::createBaseNBT($tile->asVector3()->add(0.5, 0, 0.5), null, lcg_value() * 360, 0);

                        $rand = ["Cow", "Chicken"][mt_rand(0, 1)];
                        $entity = Entity::createEntity($rand, $level, $nbt);

                        if($entity instanceof Entity){
                            $entity->spawnToAll();
                            ++$count;
                        }
                }*/
                //$this->getPlugin()->getLogger()->info("Spawned $count entity for $tiles tile.");
            }
        }
    }

    public function cancel()
    {
        $this->getHandler()->cancel();
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
}