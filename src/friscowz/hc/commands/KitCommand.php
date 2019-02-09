<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/11/2017
 * Time: 21:50
 */

namespace friscowz\hc\commands;


use friscowz\hc\item\SplashPotion;
use friscowz\hc\utils\Utils;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use friscowz\hc\Myriad;
use friscowz\hc\MDPlayer;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class KitCommand extends PluginCommand
{
    private $plugin;

    /**
     * KitCommand constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        parent::__construct("kit", $plugin);
        $this->setPlugin($plugin);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool|mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($sender instanceof MDPlayer){
            if(count($args) == 0){
                $sender->sendMessage(TextFormat::RED . "Usage: /kit <map/gold/diamond/legacy>");
            } else {
                switch(strtolower($args[0])){
                    case "map":
                        if (!$sender->isKit()) {
                            $sword = Item::get(ItemIds::DIAMOND_SWORD);
                            $helmet = Item::get(ItemIds::DIAMOND_HELMET);
                            $chestplate = Item::get(ItemIds::DIAMOND_CHESTPLATE);
                            $leggins = Item::get(ItemIds::DIAMOND_LEGGINGS);
                            $boots = Item::get(ItemIds::DIAMOND_BOOTS);

                            $sword->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
                            $helmet->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
                            $chestplate->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
                            $leggins->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
                            $boots->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");

                            $sharp = Enchantment::getEnchantment(Enchantment::SHARPNESS);
                            $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
                            $sharp->setLevel(1);
                            $protection->setLevel(2);


                            $sword->addEnchantment($sharp);
                            $helmet->addEnchantment($protection);
                            $chestplate->addEnchantment($protection);
                            $leggins->addEnchantment($protection);
                            $boots->addEnchantment($protection);


                            //$sender->getInventory()->clearAll();
                            $sender->getInventory()->addItem($sword);
                            $sender->getInventory()->setHelmet($helmet);
                            $sender->getInventory()->setChestplate($chestplate);
                            $sender->getInventory()->setLeggings($leggins);
                            $sender->getInventory()->setBoots($boots);
                            $sender->getInventory()->addItem(Item::get(ItemIds::STEAK, 0, 64));

                            while($sender->getInventory()->canAddItem(new SplashPotion(22, 1))){
                                $sender->getInventory()->addItem(Item::get(ItemIds::SPLASH_POTION, 22, 1)->setCustomName(TextFormat::RESET . TextFormat::RED . "Heal Potion"));
                            }

                            $sender->setKit(true);
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully received the kit map!");
                        } else {
                            $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already used a kit!");
                        }
                            break;
                    case "gold":
                        if($sender->getRank() == MDPlayer::GOLD) {
                            if (!$sender->isKit()) {
                                $sword = Item::get(ItemIds::DIAMOND_SWORD);
                                $helmet = Item::get(ItemIds::DIAMOND_HELMET);
                                $chestplate = Item::get(ItemIds::DIAMOND_CHESTPLATE);
                                $leggins = Item::get(ItemIds::DIAMOND_LEGGINGS);
                                $boots = Item::get(ItemIds::DIAMOND_BOOTS);

                                $sword->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Kit Gold");
                                $helmet->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Kit Gold");
                                $chestplate->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Kit Gold");
                                $leggins->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Kit Gold");
                                $boots->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Kit Gold");

                                $sharp = Enchantment::getEnchantment(Enchantment::SHARPNESS);
                                $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
                                $sharp->setLevel(2);
                                $protection->setLevel(3);


                                $sword->addEnchantment($sharp);
                                $helmet->addEnchantment($protection);
                                $chestplate->addEnchantment($protection);
                                $leggins->addEnchantment($protection);
                                $boots->addEnchantment($protection);


                                //$sender->getInventory()->clearAll();
                                $sender->getInventory()->addItem($sword);
                                $sender->getInventory()->setHelmet($helmet);
                                $sender->getInventory()->setChestplate($chestplate);
                                $sender->getInventory()->setLeggings($leggins);
                                $sender->getInventory()->setBoots($boots);
                                $sender->getInventory()->addItem(Item::get(ItemIds::STEAK, 0, 64));

                                while ($sender->getInventory()->canAddItem(new SplashPotion(22, 1))) {
                                    $sender->getInventory()->addItem(Item::get(ItemIds::SPLASH_POTION, 22, 1)->setCustomName(TextFormat::RESET . TextFormat::RED . "Heal Potion"));
                                }

                                $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully received the kit gold!");
                                $sender->setKit(true);
                            } else {
                                $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already used a kit!");
                            }
                        }
                        break;

                    case "diamond":
                        if($sender->getRank() == MDPlayer::DIAMOND) {
                            if (!$sender->isKit()) {
                                $sword = Item::get(ItemIds::DIAMOND_SWORD);
                                $helmet = Item::get(ItemIds::DIAMOND_HELMET);
                                $chestplate = Item::get(ItemIds::DIAMOND_CHESTPLATE);
                                $leggins = Item::get(ItemIds::DIAMOND_LEGGINGS);
                                $boots = Item::get(ItemIds::DIAMOND_BOOTS);

                                $sword->setCustomName(TextFormat::RESET . TextFormat::AQUA . "Kit Diamond");
                                $helmet->setCustomName(TextFormat::RESET . TextFormat::AQUA . "Kit Diamond");
                                $chestplate->setCustomName(TextFormat::RESET . TextFormat::AQUA . "Kit Diamond");
                                $leggins->setCustomName(TextFormat::RESET . TextFormat::AQUA . "Kit Diamond");
                                $boots->setCustomName(TextFormat::RESET . TextFormat::AQUA . "Kit Diamond");

                                $sharp = Enchantment::getEnchantment(Enchantment::SHARPNESS);
                                $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
                                $sharp->setLevel(2);
                                $protection->setLevel(3);


                                $sword->addEnchantment($sharp);
                                $helmet->addEnchantment($protection);
                                $chestplate->addEnchantment($protection);
                                $leggins->addEnchantment($protection);
                                $boots->addEnchantment($protection);


                                //$sender->getInventory()->clearAll();
                                $sender->getInventory()->addItem($sword);
                                $sender->getInventory()->setHelmet($helmet);
                                $sender->getInventory()->setChestplate($chestplate);
                                $sender->getInventory()->setLeggings($leggins);
                                $sender->getInventory()->setBoots($boots);
                                $sender->getInventory()->addItem(Item::get(ItemIds::STEAK, 0, 64));

                                while ($sender->getInventory()->canAddItem(new SplashPotion(22, 1))) {
                                    $sender->getInventory()->addItem(Item::get(ItemIds::SPLASH_POTION, 22, 1)->setCustomName(TextFormat::RESET . TextFormat::RED . "Heal Potion"));
                                }

                                $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully received the kit map!");
                                $sender->setKit(true);
                            } else {
                                $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already used a kit!");
                            }
                        }
                        break;

                    case "legacy":
                        if($sender->getRank() == MDPlayer::LEGACY) {
                            if (!$sender->isKit()) {
                                $sword = Item::get(ItemIds::DIAMOND_SWORD);
                                $helmet = Item::get(ItemIds::DIAMOND_HELMET);
                                $chestplate = Item::get(ItemIds::DIAMOND_CHESTPLATE);
                                $leggins = Item::get(ItemIds::DIAMOND_LEGGINGS);
                                $boots = Item::get(ItemIds::DIAMOND_BOOTS);

                                $sword->setCustomName(TextFormat::RESET . TextFormat::DARK_PURPLE . "Kit Legacy");
                                $helmet->setCustomName(TextFormat::RESET . TextFormat::DARK_PURPLE . "Kit Legacy");
                                $chestplate->setCustomName(TextFormat::RESET . TextFormat::DARK_PURPLE . "Kit Legacy");
                                $leggins->setCustomName(TextFormat::RESET . TextFormat::DARK_PURPLE . "Kit Legacy");
                                $boots->setCustomName(TextFormat::RESET . TextFormat::DARK_PURPLE . "Kit Legacy");

                                $sharp = Enchantment::getEnchantment(Enchantment::SHARPNESS);
                                $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
                                $sharp->setLevel(3);
                                $protection->setLevel(4);


                                $sword->addEnchantment($sharp);
                                $helmet->addEnchantment($protection);
                                $chestplate->addEnchantment($protection);
                                $leggins->addEnchantment($protection);
                                $boots->addEnchantment($protection);


                                //$sender->getInventory()->clearAll();
                                $sender->getInventory()->addItem($sword);
                                $sender->getInventory()->setHelmet($helmet);
                                $sender->getInventory()->setChestplate($chestplate);
                                $sender->getInventory()->setLeggings($leggins);
                                $sender->getInventory()->setBoots($boots);
                                $sender->getInventory()->addItem(Item::get(ItemIds::STEAK, 0, 64));

                                while ($sender->getInventory()->canAddItem(new SplashPotion(22, 1))) {
                                    $sender->getInventory()->addItem(Item::get(ItemIds::SPLASH_POTION, 22, 1)->setCustomName(TextFormat::RESET . TextFormat::RED . "Heal Potion"));
                                }

                                $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully received the kit legacy!");
                                $sender->setKit(true);
                            } else {
                                $sender->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have already used a kit!");
                            }
                        }
                        break;
                }
            }
        } else {
            $sword = Item::get(ItemIds::DIAMOND_SWORD);
            $helmet = Item::get(ItemIds::DIAMOND_HELMET);
            $chestplate = Item::get(ItemIds::DIAMOND_CHESTPLATE);
            $leggins = Item::get(ItemIds::DIAMOND_LEGGINGS);
            $boots = Item::get(ItemIds::DIAMOND_BOOTS);

            $sword->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
            $helmet->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
            $chestplate->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
            $leggins->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");
            $boots->setCustomName(TextFormat::RESET . TextFormat::RED . "Kit Map");

            $sharp = Enchantment::getEnchantment(Enchantment::SHARPNESS);
            $protection = Enchantment::getEnchantment(Enchantment::PROTECTION);
            $sharp->setLevel(1);
            $protection->setLevel(2);


            $sword->addEnchantment($sharp);
            $helmet->addEnchantment($protection);
            $chestplate->addEnchantment($protection);
            $leggins->addEnchantment($protection);
            $boots->addEnchantment($protection);

            //$sender->getInventory()->clearAll();
            $sender->getInventory()->addItem($sword);
            $sender->getInventory()->setHelmet($helmet);
            $sender->getInventory()->setChestplate($chestplate);
            $sender->getInventory()->setLeggings($leggins);
            $sender->getInventory()->setBoots($boots);
            $sender->getInventory()->addItem(Item::get(ItemIds::STEAK, 0, 64));

            while ($sender->getInventory()->canAddItem(new SplashPotion(22, 1))) {
                $sender->getInventory()->addItem(Item::get(ItemIds::SPLASH_POTION, 22, 1)->setCustomName(TextFormat::RESET . TextFormat::RED . "Heal Potion"));
            }

            $sender->sendMessage(Utils::getPrefix() . TextFormat::GREEN . "You have successfully received the kit map!");
        }
    }

    /**
     * @return Myriad
     */
    public function getMyriad () : Myriad
    {
        return $this->plugin;
    }

    /**
     * @param Myriad $plugin
     */
    public function setPlugin (Myriad $plugin)
    {
        $this->plugin = $plugin;
    }
}