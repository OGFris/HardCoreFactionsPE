<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 11:54 PM
 */

namespace friscowz\hc\task;


use friscowz\hc\MDPlayer;
use friscowz\hc\Myriad;
use pocketmine\scheduler\PluginTask;
use pocketmine\item\ItemIds;
use pocketmine\entity\Effect;

class BardTask extends PluginTask
{
    public function __construct(Myriad $plugin)
    {
        parent::__construct($plugin);
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
        foreach (Myriad::getInstance()->getServer()->getOnlinePlayers() as $player){
            if ($player instanceof MDPlayer){
                if ($player->isBard()){
                    if ($player->getBardEnergy() < 100 ) $player->setBardEnergy($player->getBardEnergy() + 1);
                    if ($player->isInFaction()) {
                        switch ($player->getInventory()->getItemInHand()->getId()) {
                            case ItemIds::SUGAR:
                                foreach (Myriad::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {
                                    if ($member->getName() !== $player->getName() and $member->distanceSquared($player) < 20) {
                                        if (!$member->hasEffect(Effect::SPEED)) $member->addEffect(Effect::getEffect(Effect::SPEED)->setDuration(20 * 5)->setAmplifier(0));
                                    }
                                }
                                break;

                            case ItemIds::FEATHER:
                                foreach (Myriad::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {
                                    if ($member->getName() !== $player->getName() and $member->distanceSquared($player) < 20) {
                                        if (!$member->hasEffect(Effect::JUMP)) $member->addEffect(Effect::getEffect(Effect::JUMP)->setDuration(20 * 5)->setAmplifier(2));
                                    }
                                }
                                break;

                            case ItemIds::IRON_INGOT:
                                foreach (Myriad::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {
                                    if ($member->getName() !== $player->getName() and $member->distanceSquared($player) < 20) {
                                        if (!$member->hasEffect(Effect::DAMAGE_RESISTANCE)) $member->addEffect(Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setDuration(20 * 5)->setAmplifier(0));
                                    }
                                }
                                break;

                            case ItemIds::GHAST_TEAR:
                                foreach (Myriad::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {
                                    if ($member->getName() !== $player->getName() and $member->distanceSquared($player) < 20) {
                                        if (!$member->hasEffect(Effect::REGENERATION)) $member->addEffect(Effect::getEffect(Effect::REGENERATION)->setDuration(20 * 5)->setAmplifier(0));
                                    }
                                }
                                break;

                            case ItemIds::BLAZE_POWDER:
                                foreach (Myriad::getFactionsManager()->getOnlineMembers($player->getFaction()) as $member) {
                                    if ($member->getName() !== $player->getName() and $member->distanceSquared($player) < 20) {
                                        if (!$member->hasEffect(Effect::STRENGTH)) $member->addEffect(Effect::getEffect(Effect::STRENGTH)->setDuration(20 * 5)->setAmplifier(0));
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }
    }

}