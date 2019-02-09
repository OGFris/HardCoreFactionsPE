<?php
/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 20:23
 */

namespace friscowz\hc\listener;

use friscowz\{
    hc\entities\BetterEnderPearl, hc\entities\BetterThrownPotion, hc\FactionsManager, hc\MDPlayer, hc\Myriad, hc\utils\Utils
};
use pocketmine\entity\Effect;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\level\ChunkLoadEvent;
use pocketmine\level\sound\LaunchSound;
use pocketmine\event\block\{
    BlockBreakEvent, BlockPlaceEvent
};
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\{
    PlayerBucketFillEvent, PlayerChatEvent, PlayerCommandPreprocessEvent, PlayerCreationEvent, PlayerDeathEvent, PlayerInteractEvent, PlayerItemConsumeEvent, PlayerJoinEvent, PlayerMoveEvent, PlayerQuitEvent
};
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\{
    Item, ItemIds
};
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\{FloatTag, CompoundTag, DoubleTag, ListTag, ShortTag};
use pocketmine\entity\Entity;

class PlayerListener implements Listener
{
    private $plugin;
    private $lastchat = [];
    private $pearl = [];
    private $badwords = array(
        "4r5e", "cancer", "gay", "headass", "fucc", "ez", "fuckk", "hoe", "niqqa", "niqqer", "niqqers","nibba", "nibber", "5h1t", "5hit", "a55", "anal", "kys", "smd", "bish", "kids", "kid", "skid", "ffs", "suicide", "killyourself", "anus", "ar5e", "arrse", "arse", "ass", "ass-fucker", "asses", "assfucker", "assfukka", "asshole", "assholes", "asswhole", "a_s_s", "b!tch", "b00bs", "b17ch", "b1tch", "ballbag", "balls", "ballsack", "bastard", "beastial", "beastiality", "bellend", "bestial", "bestiality", "bi+ch", "biatch", "bitch", "bitcher", "bitchers", "bitches", "bitchin", "bitching", "bloody", "blow job", "blowjob", "blowjobs", "boiolas", "bollock", "bollok", "boner", "boob", "boobs", "booobs", "boooobs", "booooobs", "booooooobs", "breasts", "buceta", "bugger", "bum", "bunny fucker", "butt", "butthole", "buttmuch", "buttplug", "c0ck", "c0cksucker", "carpet muncher", "cawk", "chink", "cipa", "cl1t", "clit", "clitoris", "clits", "cnut", "cock", "cock-sucker", "cockface", "cockhead", "cockmunch", "cockmuncher", "cocks", "cocksuck", "cocksucked", "cocksucker", "cocksucking", "cocksucks", "cocksuka", "cocksukka", "cok", "cokmuncher", "coksucka", "coon", "cox", "crap", "cum", "cummer", "cumming", "cums", "cumshot", "cunilingus", "cunillingus", "cunnilingus", "cunt", "cuntlick", "cuntlicker", "cuntlicking", "cunts", "cyalis", "cyberfuc", "cyberfuck", "cyberfucked", "cyberfucker", "cyberfuckers", "cyberfucking", "d1ck", "damn", "dick", "dickhead", "dildo", "dildos", "dink", "dinks", "dirsa", "dlck", "dog-fucker", "doggin", "dogging", "donkeyribber", "doosh", "duche", "dyke", "ejaculate", "ejaculated", "ejaculates", "ejaculating", "ejaculatings", "ejaculation", "ejakulate", "f u c k", "f u c k e r", "f4nny", "fag", "fagging", "faggitt", "faggot", "faggs", "fagot", "fagots", "fags", "fanny", "fannyflaps", "fannyfucker", "fanyy", "fatass", "fcuk", "fcuker", "fcuking", "feck", "fecker", "felching", "fellate", "fellatio", "fingerfuck", "fingerfucked", "fingerfucker", "fingerfuckers", "fingerfucking", "fingerfucks", "fistfuck", "fistfucked", "fistfucker", "fistfuckers", "fistfucking", "fistfuckings", "fistfucks", "flange", "fook", "fooker", "fuck", "fucka", "fucked", "fucker", "fuckers", "fuckhead", "fuckheads", "fuckin", "fucking", "fuckings", "fuckingshitmotherfucker", "fuckme", "fucks", "fuckwhit", "fuckwit", "fudge packer", "fudgepacker", "fuk", "fuker", "fukker", "fukkin", "fuks", "fukwhit", "fukwit", "fux", "fux0r", "f_u_c_k", "gangbang", "gangbanged", "gangbangs", "gaylord", "gaysex", "goatse", "God", "god-dam", "god-damned", "goddamn", "goddamned", "hardcoresex", "hell", "heshe", "hoar", "hoare", "hoer", "homo", "hore", "horniest", "horny", "hotsex", "jack-off", "jackoff", "jap", "jerk-off", "jism", "jiz", "jizm", "jizz", "kawk", "knob", "knobead", "knobed", "knobend", "knobhead", "knobjocky", "knobjokey", "kock", "kondum", "kondums", "kum", "kummer", "kumming", "kums", "kunilingus", "l3i+ch", "l3itch", "labia", "lmfao", "lust", "lusting", "m0f0", "m0fo", "m45terbate", "ma5terb8", "ma5terbate", "masochist", "master-bate", "masterb8", "masterbat*", "masterbat3", "masterbate", "masterbation", "masterbations", "masturbate", "mo-fo", "mof0", "mofo", "mothafuck", "mothafucka", "mothafuckas", "mothafuckaz", "mothafucked", "mothafucker", "mothafuckers", "mothafuckin", "mothafucking", "mothafuckings", "mothafucks", "mother fucker", "motherfuck", "motherfucked", "motherfucker", "motherfuckers", "motherfuckin", "motherfucking", "motherfuckings", "motherfuckka", "motherfucks", "muff", "mutha", "muthafecker", "muthafuckker", "muther", "mutherfucker", "n1gga", "n1gger", "nazi", "nigg3r", "nigg4h", "nigga", "niggah", "niggas", "niggaz", "nigger", "niggers", "nob", "nob jokey", "nobhead", "nobjocky", "nobjokey", "numbnuts", "nutsack", "orgasim", "orgasims", "orgasm", "orgasms", "p0rn", "pawn", "pecker", "penis", "penisfucker", "phonesex", "phuck", "phuk", "phuked", "phuking", "phukked", "phukking", "phuks", "phuq", "pigfucker", "pimpis", "piss", "pissed", "pisser", "pissers", "pisses", "pissflaps", "pissin", "pissing", "pissoff", "poop", "porn", "porno", "pornography", "pornos", "prick", "pricks", "pron", "pube", "pusse", "pussi", "pussies", "pussy", "pussys", "rectum", "retard", "rimjaw", "rimming", "s hit", "s.o.b.", "sadist", "schlong", "screwing", "scroat", "scrote", "scrotum", "semen", "sex", "sh!+", "sh!t", "sh1t", "shag", "shagger", "shaggin", "shagging", "shemale", "shi+", "shit", "shitdick", "shite", "shited", "shitey", "shitfuck", "shitfull", "shithead", "shiting", "shitings", "shits", "shitted", "shitter", "shitters", "shitting", "shittings", "shitty", "skank", "slut", "sluts", "smegma", "smut", "snatch", "son-of-a-bitch", "spac", "spunk", "s_h_i_t", "t1tt1e5", "t1tties", "teets", "teez", "testical", "testicle", "tit", "titfuck", "tits", "titt", "tittie5", "tittiefucker", "titties", "tittyfuck", "tittywank", "titwank", "tosser", "turd", "tw4t", "twat", "twathead", "twatty", "twunt", "twunter", "v14gra", "v1gra", "vagina", "viagra", "vulva", "w00se", "wang", "wank", "wanker", "wanky", "whoar", "whore", "willies", "willy", "xrated", "xxx"
    );

    /**
     * PlayerListener constructor.
     * @param Myriad $plugin
     */
    public function __construct(Myriad $plugin)
    {
        $this->setPlugin($plugin);
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return mixed
     */
    public function getPlugin() : Myriad
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
     * @param PlayerCreationEvent $event
     */
    public function onCreation(PlayerCreationEvent $event)
    {
        $event->setPlayerClass(MDPlayer::class);
    }



    /**
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        /*
        if($event->getTo()->getX() <= -900 or $event->getTo()->getX() >= 900 or $event->getTo()->getZ() <= -900 or $event->getTo()->getZ() >= 900){
            $event->setCancelled(true);
            $player->sendPopup(TextFormat::RED . TextFormat::BOLD . "You have reached the border!" . TextFormat::RESET);
        }*/
        if(!Myriad::getBorder()->insideBorder($player->getFloorX(), $player->getFloorZ())){
            $player->teleport(Myriad::getBorder()->correctPosition($player->getLocation()));
            $player->sendPopup(TextFormat::RED . TextFormat::BOLD . "You have reached the border!" . TextFormat::RESET);
        }
        if($player instanceof MDPlayer) {
            if($player->getRegion() == "null"){
                if(Myriad::getFactionsManager()->isSpawnClaim($player)){
                    $player->setRegion("Spawn");
                } else
                    if(Myriad::getFactionsManager()->isClaim($player)){
                        $region = Myriad::getFactionsManager()->getClaimer($player->getX(), $player->getZ());
                        $player->setRegion($region);
                    } else {
                        $player->setRegion("Wildness");
                    }
            }
            if($player->getRegion() != $player->getCurrentRegion()) {
                if ($player->getCurrentRegion() == "Spawn") {
                    $player->sendMessage(TextFormat::YELLOW . "Now Leaving " . TextFormat::GRAY . $player->getRegion() . TextFormat::YELLOW . " (" . TextFormat::RED . "Deathban" . TextFormat::YELLOW . ")");
                    $player->sendMessage(TextFormat::YELLOW . "Now Entering" . TextFormat::GRAY . " Spawn " . TextFormat::YELLOW . "(" . TextFormat::GREEN . "Non-Deathban" . TextFormat::YELLOW . ")");
                } else {
                    if ($player->getRegion() == "Spawn") {
                        $player->sendMessage(TextFormat::YELLOW . "Now Leaving" . TextFormat::GRAY . " Spawn " . TextFormat::YELLOW . "(" . TextFormat::GREEN . "Non-Deathban" . TextFormat::YELLOW . ")");
                        $player->sendMessage(TextFormat::YELLOW . "Now Entering " . TextFormat::GRAY . $player->getCurrentRegion() . TextFormat::YELLOW . " (" . TextFormat::RED . "Deathban" . TextFormat::YELLOW . ")");
                    } else {
                        $player->sendMessage(TextFormat::YELLOW . "Now Leaving " . TextFormat::GRAY . $player->getRegion() . TextFormat::YELLOW . " (" . TextFormat::RED . "Deathban" . TextFormat::YELLOW . ")");
                        $player->sendMessage(TextFormat::YELLOW . "Now Entering " . TextFormat::GRAY . $player->getCurrentRegion() . TextFormat::YELLOW . " (" . TextFormat::RED . "Deathban" . TextFormat::YELLOW . ")");
                    }
                }
                $player->setRegion($player->getCurrentRegion());
            }
            $player->checkLast();
            switch ($player->getLevel()->getBlockIdAt($player->getFloorX(), $player->getFloorY(), $player->getFloorZ())) {
                case 119:
                    if (!$player->isPvp()) {
                        $player->Back();
                    } else {
                        $block = $player->getLevel()->getBlock($player->subtract(0, 1, 0));
                        $player->knockBack($player, 0, ($player->x - ($block->x + 0.5)), ($player->z - ($block->z + 0.5)), 0.3);
                        $player->sendPopup(TextFormat::RED . TextFormat::BOLD . "You can't do that in PVPTimer!");
                    }
                    break;
            }

            if ($event->getTo()->getX() != $event->getFrom()->getX() || $event->getTo()->getZ() != $event->getFrom()->getZ()) {
                if ($player->isTeleporting()) {
                    $player->setTeleporting(false);
                    $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "Teleportation cancelled for moving!");
                }
                if ($player->isLogout()) {
                    $player->setLogout(false);
                    $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have been removed from the Logout for moving!");
                }
            }
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event)
    {
        $block = $event->getBlock();
        $spawn = new Vector3(0, 50, 0);
        $player = $event->getPlayer();
        if($player->getLevel()->getName() != $this->getPlugin()->getServer()->getDefaultLevel()->getName() and !$player->isOp()){
            $player->sendMessage(TextFormat::RED . "You can only build on the overworld!");
            $event->setCancelled(true);
            return;
        }
        if($spawn->distance($block) < 200 and !$player->isOp()){
            $player->sendMessage(TextFormat::RED . "You can't build near the spawn!");
            $event->setCancelled(true);
            return;
        }
        if(Myriad::getFactionsManager()->isRoad($block->x, $block->z)){
            if(!$player->isOp()) $event->setCancelled(true);
            return;
        }
        if(Myriad::getFactionsManager()->isClaim($block)){
            if(Myriad::getFactionsManager()->getClaimType($block->x, $block->z) == FactionsManager::PROTECTED){
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @param PlayerBucketFillEvent $event
     */
    public function onBucketFill(PlayerBucketFillEvent $event)
    {
        $player = $event->getPlayer();
        $block = $event->getBlockClicked();
        if(Myriad::getFactionsManager()->isRoad($block->x, $block->z)){
            if(!$player->isOp()) $event->setCancelled(true);
            return;
        }
        if(Myriad::getFactionsManager()->isClaim($block)){
            if(Myriad::getFactionsManager()->getClaimType($block->x, $block->z) == FactionsManager::PROTECTED || Myriad::getFactionsManager()->getClaimType($block->x, $block->z) == FactionsManager::SPAWN){
                $event->setCancelled(true);
            }
        }

    }


    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event)
    {
        $block = $event->getBlock();
        $spawn = new Vector3(0, 50, 0);
        $player = $event->getPlayer();
        if($player->getLevel()->getName() != $this->getPlugin()->getServer()->getDefaultLevel()->getName() and !$player->isOp()){
            $player->sendMessage(TextFormat::RED . "You can only build on the overworld!");
            $event->setCancelled(true);
            return;
        }
        if($spawn->distance($block) < 200 and !$player->isOp()){
            $player->sendMessage(TextFormat::RED . "You can't build near the spawn!");
            $event->setCancelled(true);
            return;
        }
        if(Myriad::getFactionsManager()->isRoad($block->x, $block->z)){
            if(!$player->isOp()) $event->setCancelled(true);
            return;
        }
        if(Myriad::getFactionsManager()->isClaim($block)){
            if(Myriad::getFactionsManager()->getClaimType($block->x, $block->z) == FactionsManager::PROTECTED){
                $event->setCancelled(true);
                return;
            }
        }
        if($event->isCancelled()) return;
        if($player instanceof MDPlayer){
            switch ($block->getId()){
                case 14:
                    $player->addData("Gold", 1);
                    if($player->isMiner()) {
                        $player->getInventory()->addItem(Item::get(266, 0, 1));
                        $player->sendPopup(TextFormat::GOLD . "+1 Gold");
                        $event->setDrops([]);
                    }
                break;

                case 15:
                    $player->addData("Iron", 1);
                    if($player->isMiner()) {
                        $player->getInventory()->addItem(Item::get(265, 0, 1));
                        $player->sendPopup(TextFormat::WHITE . "+1 Iron");
                        $event->setDrops([]);
                    }
                break;

                case 21:
                    $player->addData("Lapis", 1);
                    if($player->isMiner()) {
                        $player->getInventory()->addItem(Item::get(351, 4, $rand = mt_rand(1, 3)));
                        $player->sendPopup(TextFormat::DARK_BLUE . "+" . $rand . "Lapis");
                        $event->setDrops([]);
                    }
                break;

                case 56:
                    $player->addData("Diamonds", 1);
                    if($player->isMiner()) {
                        $player->getInventory()->addItem(Item::get(264, 0, 1));
                        $player->sendPopup(TextFormat::AQUA . "+1 Diamond");
                        $event->setDrops([]);
                    }
                break;
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onProjectile(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();
        /*
        if(Myriad::getManager()->isPCBlock($block)){
            Myriad::getManager()->convertBlock($block);
        }*/
        //$player->sendMessage($block->getId() . " : " . $block->getDamage());
        if($item->getId() == ItemIds::ENDER_PEARL) {
            if($player->getLevel()->getBlockIdAt($player->x, $player->y+1, $player->z) != 0){
                $event->setCancelled(true);
                return;
            }
            $event->setCancelled(true);
            if ($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_AIR or $event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                if(!isset($this->pearl[$player->getName()])){
                    $this->pearl[$player->getName()] = time();
                } else {
                    if((time() - $this->pearl[$player->getName()]) < 16){
                        $timer = time() - $this->pearl[$player->getName()];
                        $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have to wait " . (16 - $timer) . " more second(s) to use EnderPearl!");
                        return;
                    } else {
                        $this->pearl[$player->getName()] = time();
                    }
                }
                $nbt = new CompoundTag("", [
                    "Pos" => new ListTag("Pos", [
                        new DoubleTag("", $player->x),
                        new DoubleTag("", $player->y + $player->getEyeHeight()),
                        new DoubleTag("", $player->z),
                    ]),
                    "Motion" => new ListTag("Motion", [
                        new DoubleTag("", -sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
                        new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
                        new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
                    ]),
                    "Rotation" => new ListTag("Rotation", [
                        new FloatTag("", $player->yaw),
                        new FloatTag("", $player->pitch),
                    ]),
                ]);
                $entity = Entity::createEntity("BetterEnderPearl", $player->getLevel(), $nbt, $player);
                if($entity instanceof BetterEnderPearl){
                    if($entity->isGoingToColide()){
                        $entity->kill();
                        return;
                    }
                }
                $entity->setMotion($entity->getMotion()->multiply(1.4)); //i think it should be better

                if ($player->isSurvival()) {
                    $item->setCount($item->getCount() - 1);
                    $player->getInventory()->setItemInHand($item->getCount() > 0 ? $item : Item::get(Item::AIR));
                }

                if ($entity instanceof BetterEnderPearl) {
                    $entity->spawnToAll();
                    $player->getLevel()->addSound(new LaunchSound($player->asVector3()), $player->getViewers());
                }
            }
        } else
            if($item->getId() == ItemIds::SPLASH_POTION){
                if ($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_AIR or $event->getAction() == PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                    $nbt = new CompoundTag("", [
                        "Pos" => new ListTag("Pos", [
                            new DoubleTag("", $player->x),
                            new DoubleTag("", $player->y + $player->getEyeHeight()),
                            new DoubleTag("", $player->z),
                        ]),
                        "Motion" => new ListTag("Motion", [
                            new DoubleTag("", -sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
                            new DoubleTag("", -sin($player->pitch / 180 * M_PI)),
                            new DoubleTag("", cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)),
                        ]),
                        "Rotation" => new ListTag("Rotation", [
                            new FloatTag("", $player->yaw),
                            new FloatTag("", $player->pitch),
                        ]),
                    ]);
                    $nbt["PotionId"] = new ShortTag("PotionId", $item->getDamage());
                    $entity = Entity::createEntity("BetterThrownPotion", $player->getLevel(), $nbt, null);
                    if($entity !=  null) {
                        $entity->setMotion($entity->getMotion()->multiply(1.2));
                    }

                    if ($player->isSurvival()) {
                        $item->setCount($item->getCount() - 1);
                        $player->getInventory()->setItemInHand(Item::get(Item::AIR));
                    }

                    if ($entity instanceof BetterThrownPotion) {
                        $entity->spawnToAll();
                        $player->getLevel()->addSound(new LaunchSound($player->asVector3()), $player->getViewers());
                    }
                }
            }
    }

    /**
     * @param DataPacketReceiveEvent $event
     */
    public function onReceivePacket(DataPacketReceiveEvent $event)
    {
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if($packet instanceof LoginPacket){
            if($player instanceof MDPlayer){
                if(isset($packet->clientData["DeviceOS"])){
                    $player->setDeviceOS($packet->clientData["DeviceOS"]);
                }
            }
        }
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $event->setJoinMessage(null);
        if($player instanceof MDPlayer) {
            $player->init();
            $player->checkSets();
            $player->checkProxy();
        }
    }

    /**
     * @param ChunkLoadEvent $event
     */
    public function onChunkLoad(ChunkLoadEvent $event)
    {
        if($event->isNewChunk()){
            $event->setCancelled(true);
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $event->setQuitMessage(false);
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event)
    {
        $player = $event->getEntity();
        if($player instanceof MDPlayer) {
            if ($player->isTeleporting()) {
                $player->setTeleporting(false);
                $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "Teleportation cancelled for moving!");
            }
            if ($player->isLogout()) {
                $player->setLogout(false);
                $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "You have been removed from the Logout for moving!");
            }
            if (Myriad::getFactionsManager()->isSpawnClaim($player)) {
                $event->setCancelled(true);
            }
            if($event->getCause() == $event::CAUSE_LAVA){
                $event->setCancelled(true);
            }
            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if ($damager instanceof BetterThrownPotion) {
                    $event->setCancelled(true);
                    $damager->splash(1);
                }
            }
        }
    }

    /**
     * @param PlayerItemConsumeEvent $event
     */
    public function onItemConsume(PlayerItemConsumeEvent $event)
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($item->getId() == ItemIds::GOLDEN_APPLE){
            $regeneration = Effect::getEffect(Effect::REGENERATION)->setAmplifier(0)->setDuration(20 * 4);
            $abso = Effect::getEffect(Effect::ABSORPTION)->setAmplifier(1)->setDuration(20*15);
            $player->addEffect($regeneration);
            $player->addEffect($regeneration);
        }
    }

    public function filterBadwords($text, $replaceChar = '*')
    {
        $badwords = $this->badwords;
        return preg_replace_callback(
            array_map(function ($w) {
                return '/\b' . preg_quote($w, '/') . '\b/i';
            }, $badwords),
            function ($match) use ($replaceChar) {
                return str_repeat($replaceChar, strlen($match[0]));
            },
            $text
        );
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        if(!isset($this->lastchat[$player->getName()])){
            $this->lastchat[$player->getName()] = time();
        } else {
            if((time() - $this->lastchat[$player->getName()]) < 3 and $player->getRank() == 0){
                $event->setCancelled(true);
                $player->sendMessage(Utils::getPrefix() . TextFormat::RED . "Please don't spam the chat! You can buy a rank to talk faster.");
                return;
            } else {
                $this->lastchat[$player->getName()] = time();
            }
        }

        $rank = TextFormat::GRAY . $player->getName() . TextFormat::DARK_GRAY . ": " . TextFormat::GRAY;

        if($player instanceof MDPlayer){
            switch ($player->getRank()){
                case MDPlayer::BRONZE:
                    $rank = TextFormat::GRAY . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::SILVER:
                    $rank = TextFormat::GRAY . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::GOLD:
                    $rank = TextFormat::GOLD . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::DIAMOND:
                    $rank = TextFormat::DARK_AQUA . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::LEGACY:
                    $rank = TextFormat::LIGHT_PURPLE . TextFormat::OBFUSCATED . "iii" . TextFormat::RESET . TextFormat::DARK_PURPLE . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::TRIAL:
                    $rank = TextFormat::DARK_GRAY . TextFormat::BOLD . "(" . TextFormat::GRAY . "Trial" . TextFormat::BOLD . TextFormat::DARK_GRAY . ") " . TextFormat::GRAY . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::MOD:
                    $rank = TextFormat::DARK_GRAY . TextFormat::BOLD . "(" . TextFormat::WHITE . "Mod" . TextFormat::BOLD . TextFormat::DARK_GRAY . ") " . TextFormat::GRAY . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::ADMIN:
                    $rank = TextFormat::DARK_GRAY . TextFormat::BOLD . "(" . TextFormat::GREEN . "Admin" . TextFormat::BOLD . TextFormat::DARK_GRAY . ") " . TextFormat::GRAY . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::HEAD_ADMIN:
                    $rank = TextFormat::DARK_GRAY . TextFormat::BOLD . "(" . TextFormat::DARK_GREEN . "Head-Admin" . TextFormat::BOLD . TextFormat::DARK_GRAY . ") " . TextFormat::GRAY . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::OWNER:
                    $rank = TextFormat::DARK_GRAY . TextFormat::BOLD . "(" . TextFormat::RED . "Owner" . TextFormat::BOLD . TextFormat::DARK_GRAY . ") " . TextFormat::GRAY . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::FRIS:
                    $rank = TextFormat::DARK_GRAY . TextFormat::BOLD . "(" . TextFormat::DARK_RED . "Main-Owner" . TextFormat::BOLD . TextFormat::DARK_GRAY . ") " . TextFormat::DARK_RED . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::RED;
                    break;
                case MDPlayer::PARTNER:
                    $rank = TextFormat::RED . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
                case MDPlayer::FAMOUS:
                    $rank = TextFormat::DARK_RED . TextFormat::BOLD . $player->getName() . ": " . TextFormat::RESET . TextFormat::GRAY;
                    break;
            }
            if($player->isInFaction()){
                $faction = $player->getFaction();
                if($player->isOp()){
                    $event->setFormat(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . TextFormat::UNDERLINE . $faction . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . $rank . TextFormat::clean($event->getMessage(), true));
                } else {
                    $event->setFormat(TextFormat::DARK_GRAY . "[" . TextFormat::GRAY . TextFormat::UNDERLINE . $faction . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . $rank . ucfirst(strtolower($this->filterBadwords(TextFormat::clean($event->getMessage(), true)))));
                }
            } else {
                $event->setFormat($rank . ucfirst(strtolower($this->filterBadwords(TextFormat::clean($event->getMessage(), true)))));
            }
        } else {
            $event->setFormat(TextFormat::GRAY . $player->getName() . TextFormat::DARK_GRAY . ": " . TextFormat::GRAY .  ucfirst(strtolower($this->filterBadwords(TextFormat::clean($event->getMessage(), true)))));
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function onCommand(PlayerCommandPreprocessEvent $event)
    {
        $message = $event->getMessage();
        $words = explode(" ", $message);
        $cmd = strtolower(substr(array_shift($words), 1));
        if($cmd == "me"){
            $event->setCancelled(true);
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        if($player instanceof MDPlayer){
            $player->addDeaths(1);
            $player->resetPvptimer();
            $dead = $player->getName() . TextFormat::DARK_RED . "[" . TextFormat::RED . $player->getKills() . TextFormat::DARK_RED . "]" . TextFormat::YELLOW;
            $cause = $player->getLastDamageCause();
            if ($cause instanceof EntityDamageByEntityEvent) {
                $killer = $cause->getDamager();
                $kill = $killer->getName();
                if($killer instanceof MDPlayer){
                    $killer->addKills(1);
                    $kill = $killer->getName() . TextFormat::DARK_RED . "[" . TextFormat::RED . $killer->getKills() . TextFormat::DARK_RED . "]";
                }
                $event->setDeathMessage(TextFormat::RED . $dead . TextFormat::YELLOW . " was killed by " . TextFormat::RED . $kill);
            } else {
                $cause = $player->getLastDamageCause()->getCause();
                if($cause === EntityDamageEvent::CAUSE_SUFFOCATION)
                {
                    $event->setDeathMessage(TextFormat::RED . $dead . " suffocated");
                } elseif ($cause === EntityDamageEvent::CAUSE_DROWNING)
                {
                    $event->setDeathMessage(TextFormat::RED . $dead . " drowned");
                } elseif ($cause === EntityDamageEvent::CAUSE_FALL)
                {
                    $event->setDeathMessage(TextFormat::RED . $dead . " fell to hard");
                } elseif ($cause === EntityDamageEvent::CAUSE_FIRE)
                {
                    $event->setDeathMessage(TextFormat::RED . $dead . " burned");
                } elseif ($cause === EntityDamageEvent::CAUSE_FIRE_TICK)
                {
                    $event->setDeathMessage(TextFormat::RED . $dead . " burned");
                } elseif ($cause === EntityDamageEvent::CAUSE_LAVA)
                {
                    $event->setDeathMessage(TextFormat::RED . $dead . " tried to swim in lava");
                } elseif ($cause === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION)
                {
                    $event->setDeathMessage(TextFormat::RED . $dead . " explode");
                } else {
                    $event->setDeathMessage(TextFormat::RED . $dead . " died");
                }
            }

        }
    }



}
