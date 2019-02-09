<?php

/**
 * Created by PhpStorm.
 * User: FRISCOWZ
 * Date: 12/08/2017
 * Time: 18:27
 */
namespace friscowz\hc;

//use friscowz\hc\commands\LogoutCommand;
use friscowz\{
    hc\blocks\Observer, hc\border\Border, hc\commands\ChunksCommand, hc\commands\ClaimCommand, hc\commands\CoordsCommand, hc\commands\CrateCommand, hc\commands\FactionCommand,/* hc\commands\KitCommand, */hc\commands\KoTHCommand, hc\commands\MyMoneyCommand, hc\commands\OresCommand, hc\commands\PayCommand, hc\commands\PingCommand, hc\commands\PVPCommand, hc\commands\RankCommand, /*hc\commands\ReclaimCommand,*/ hc\commands\SOTWCommand, hc\commands\StaffCommand, hc\commands\StatsCommand, hc\commands\ViewCommand, hc\crate\Crate, hc\crate\CrateListener, hc\enchants\BaneOfArthropods, hc\enchants\BlastProtection, hc\enchants\block\EnchantingTable, hc\enchants\DepthStrider, hc\enchants\FeatherFalling, hc\enchants\FireAspect, hc\enchants\FireProtection, hc\enchants\Flame, hc\enchants\Fortune, hc\enchants\Infinity, hc\enchants\Knockback, hc\enchants\Looting, hc\enchants\Power, hc\enchants\ProjectileProtection, hc\enchants\Protection, hc\enchants\Punch, hc\enchants\Sharpness, hc\enchants\SilkTouch, hc\enchants\Smite, hc\enchants\Unbreaking, hc\end\Portal,/* hc\entities\BetterBoat,*/ hc\entities\BetterEnderPearl, hc\item\ItemManager, hc\listener\CheatListener, hc\listener\ClaimListener, hc\listener\FactionListener, hc\listener\PlayerListener, hc\listener\StaffListener, hc\modules\ModulesManager, /*hc\redstone\RedstoneManager,*/ hc\shop\Shop, hc\shop\ShopListener, hc\task\RestartTask, hc\task\SpawnersTask, hc\entities\BetterThrownPotion, hc\tiles\PotionSpawner
};
use pocketmine\{
    block\BlockFactory, item\enchantment\Enchantment, entity\Entity, plugin\PluginBase, Server, tile\Tile, utils\Config, utils\TextFormat
};

class Myriad extends PluginBase
{

    public static $FactionsManager;
    public static $Manager;
    public static $ModulesManager;
    public static $data;
    public static $shop;
    public static $crate;
    public static $border;

    /**
     * @return Crate
     */
    public static function getCrate () : Crate
    {
        return self::$crate;
    }

    /**
     * @param Crate $crate
     */
    public static function setCrate ($crate)
    {
        self::$crate = $crate;
    }

    /**
     * @return Border
     */
    public static function getBorder() : Border
    {
        return self::$border;
    }

    /**
     * @param Border $border
     */
    public static function setBorder(Border $border)
    {
        self::$border = $border;
    }


    /**
     *
     */
    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->setListener();
        $this->setManagers();
        $this->registerEntities();
        $this->initData();
        $this->registerCommands();
        $this->registerEnchants();
        $this->registerTiles();
        $this->registerBlocks();
        ItemManager::init();
        Entity::init();
        //$this->getServer()->loadLevel("ender");
        new SpawnersTask($this);
        new RestartTask($this);
        $this->getLogger()->info(TextFormat::GREEN . "the plugin has been enabled successfully !");
    }


    /**
     * @return Myriad
     */
    public static function getInstance() : Myriad
    {
        return Server::getInstance()->getPluginManager()->getPlugin("MyriadHC");
    }

    /**
     *
     */
    public function initData(){
        self::$data = new Config($this->getDataFolder() . "config.json", Config::JSON, [
            "MaxPlayerPerFaction" => 15,
            "KothTime" => 900,
            "MaxDTR" => 5.5,
            "DTRFreezeTime" => 900,
            "DTRLosePerDeath" => 1.5,
            "DTRWinPerRegen" => 0.5,
            "DTRRegenTime" => 300,
            "SoTW" => 60*60,
            "PvPTimer" => 60*15,
            "SpawnRadius" => 30,
            "Spawn" => [
                "x" => 0,
                "z" => 0
            ]
        ]);
        $this->getLogger()->debug(TextFormat::YELLOW . "Registered Config.");
    }

    /**
     * @param $text
     * @return mixed
     */
    public static function getData($text)
    {
        return self::$data->getAll()[$text];
    }

    public function registerBlocks()
    {
        BlockFactory::registerBlock(new Observer(), true);
        BlockFactory::registerBlock(new EnchantingTable(), true);
        $this->getLogger()->debug(TextFormat::YELLOW . "Registered all Blocks(2).");
    }

    public function registerTiles()
    {
        Tile::registerTile(PotionSpawner::class);
        $this->getLogger()->debug(TextFormat::YELLOW . "Registered all Tiles(1).");
    }

    /**
     *
     */
    public function registerCommands()
    {
        $this->getServer()->getCommandMap()->register("f", new FactionCommand($this));
        $this->getServer()->getCommandMap()->register("sotw", new SOTWCommand($this));
        //$this->getServer()->getCommandMap()->register("logout", new LogoutCommand($this));
        $this->getServer()->getCommandMap()->register("koth", new KoTHCommand($this));
        $this->getServer()->getCommandMap()->register("pvp", new PVPCommand($this));
        $this->getServer()->getCommandMap()->register("crate", new CrateCommand($this));
        $this->getServer()->getCommandMap()->register("stats", new StatsCommand($this));
        $this->getServer()->getCommandMap()->register("ores", new OresCommand($this));
        $this->getServer()->getCommandMap()->register("claim", new ClaimCommand($this));
        $this->getServer()->getCommandMap()->register("pay", new PayCommand($this));
        $this->getServer()->getCommandMap()->register("coords", new CoordsCommand($this));
        $this->getServer()->getCommandMap()->register("mymoney", new MyMoneyCommand($this));
        //$this->getServer()->getCommandMap()->register("kit", new KitCommand($this));
        $this->getServer()->getCommandMap()->register("rank", new RankCommand($this));
        $this->getServer()->getCommandMap()->register("view", new ViewCommand($this));
        $this->getServer()->getCommandMap()->register("staff", new StaffCommand($this));
        $this->getServer()->getCommandMap()->register("chunks", new ChunksCommand($this));
        $this->getServer()->getCommandMap()->register("ping", new PingCommand($this));
        //$this->getServer()->getCommandMap()->register("reclaim", new ReclaimCommand($this));
        $this->getLogger()->debug(TextFormat::YELLOW . "Registered all Commands(17).");
    }

    /**
     *
     */
    public function registerEntities()
    {
        //Entity::registerEntity(BetterBoat::class, true);
        Entity::registerEntity(BetterEnderPearl::class, true);
        Entity::registerEntity(BetterThrownPotion::class, true, ["BetterThrownPotion"]);
        $this->getLogger()->debug(TextFormat::YELLOW . "Registered all Entities(3).");
    }

    /**
     * @return FactionsManager
     */
    public static function getFactionsManager() : FactionsManager
    {
        return self::$FactionsManager;
    }


    /**
     * @param FactionsManager $FactionsManager
     */
    public static function setFactionsManager(FactionsManager $FactionsManager)
    {
        self::$FactionsManager = $FactionsManager;
    }

    /**
     * @return Manager
     */
    public static function getManager() : Manager
    {
        return self::$Manager;
    }

    /**
     * @param Manager $Manager
     */
    public static function setManager(Manager $Manager)
    {
        self::$Manager = $Manager;
    }


    /**
     * @return ModulesManager
     */
    public static function getModulesManager () : ModulesManager
    {
        return self::$ModulesManager;
    }

    /**
     * @param ModulesManager $ModulesManager
     */
    public static function setModulesManager (ModulesManager $ModulesManager)
    {
        self::$ModulesManager = $ModulesManager;
    }

    /**
     *
     */
    public function setManagers()
    {
        self::setFactionsManager(new FactionsManager($this));
        self::setManager(new Manager($this));
        self::setModulesManager(new ModulesManager($this));
        self::setShop(new Shop($this));
        self::setCrate(new Crate($this));
        self::setBorder(new Border(0, 0, 1800, $this->getServer()->getDefaultLevel()));
        $this->getLogger()->debug(TextFormat::YELLOW . "Registered all Managers(6).");
    }

    /**
     *
     */
    public function setListener()
    {
        new StaffListener($this);
        new FactionListener($this);
        new PlayerListener($this);
        new CheatListener($this);
        new Portal($this);
        new ShopListener($this);
        new CrateListener($this);
        new ClaimListener($this);

        $this->getLogger()->debug(TextFormat::YELLOW . "Registered all Listeners(7).");
    }

    /**
     * @return Shop
     */
    public static function getShop() : Shop
    {
        return self::$shop;
    }

    /**
     * @param Shop $shop
     */
    public static function setShop(Shop $shop)
    {
        self::$shop = $shop;
    }

    public function registerEnchants(){
        $this->registerProtection();
        $this->registerSharpness();
        $this->registerFireProtection();
        $this->registerFeatherFalling();
        $this->registerBlastProtection();
        $this->registerProjectileProtection();
        $this->registerThorns();
        $this->registerRespiration();
        //$this->registerDepthStrider(); #Lags
        $this->registerAquaAffinity();
        $this->registerSmite();
        $this->registerBaneOfArthropods();
        $this->registerKnockback();
        $this->registerFireAspect();
        $this->registerLooting();
        $this->registerEfficiency();
        $this->registerSilkTouch();
        $this->registerUnbreaking();
        $this->registerFortune();
        $this->registerPower();
        $this->registerPunch();
        $this->registerFlame();
        $this->registerInfinity();

        $this->getLogger()->debug(TextFormat::YELLOW . "Registred all enchants(23).");
    }

    /**
     * @void registerProtection
     */

    public function registerProtection(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::PROTECTION, "Protection", 0, 0, Enchantment::SLOT_ARMOR));
        new Protection($this);
    }

    /**
     * @void registerFireProtection
     */

    public function registerFireProtection(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::FIRE_PROTECTION, "Fire protection", 1, 0, Enchantment::SLOT_ARMOR));
        new FireProtection($this);
    }

    /**
     * @void registerFeatherFalling
     */

    public function registerFeatherFalling(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::FEATHER_FALLING, "Feather falling", 1, 0, Enchantment::SLOT_FEET));
        new FeatherFalling($this);
    }

    /**
     * @void registerBlastProtection
     */

    public function registerBlastProtection(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::BLAST_PROTECTION, "Blast protection", 1, 0, Enchantment::SLOT_ARMOR));
        new BlastProtection($this);
    }

    /**
     * @void registerProjectileProtection
     */

    public function registerProjectileProtection(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::PROJECTILE_PROTECTION, "Projectile protection", 1, 0, Enchantment::SLOT_ARMOR));
        new ProjectileProtection($this);
    }

    /**
     * @void registerThorns
     */

    public function registerThorns(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::THORNS, "Thorns", 1, 0, Enchantment::SLOT_ARMOR));
    }

    /**
     * @void registerRespiration
     */

    public function registerRespiration(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::RESPIRATION, "Respiration", 1, 0, Enchantment::SLOT_HEAD));
    }

    /**
     * @void registerDepthStrider
     */

    public function registerDepthStrider(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::DEPTH_STRIDER, "Depth strider", 1, 0, Enchantment::SLOT_FEET));
        new DepthStrider($this);
    }

    /**
     * @void registerAquaAffinity
     */

    public function registerAquaAffinity(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::AQUA_AFFINITY, "Aqua Affinity", 1, 0, Enchantment::SLOT_HEAD));
    }

    /**
     * @void registerSharpness
     */

    public function registerSharpness(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::SHARPNESS, "Sharpness", 1, 0, Enchantment::SLOT_TOOL));
        new Sharpness($this);
    }

    /**
     * @void registerSmite
     */

    public function registerSmite(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::SMITE, "Smite", 1, 0, Enchantment::SLOT_SWORD));
        new Smite($this);
    }

    /**
     * @void registerBaneOfArthropods
     */

    public function registerBaneOfArthropods(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::BANE_OF_ARTHROPODS, "Bane of Arthropods", 1, 0, Enchantment::SLOT_SWORD));
        new BaneOfArthropods($this);
    }

    /**
     * @void registerKnockback
     */
    public function registerKnockback(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::KNOCKBACK, "Knockback", 1, 0, Enchantment::SLOT_SWORD));
        new Knockback($this);
    }

     /**
      * @void registerFireAspect
      */

    public function registerFireAspect(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::FIRE_ASPECT, "Fire aspect", 1, 0, Enchantment::SLOT_SWORD));
        new FireAspect($this);
    }

    /**
     * @void registerLooting
     */

    public function registerLooting(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::LOOTING, "Looting", 1, 0, Enchantment::SLOT_SWORD));
        new Looting($this);
    }

    /**
     * @void registerEfficiency
     */

    public function registerEfficiency(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::EFFICIENCY, "Efficiency", 1, 0, Enchantment::SLOT_PICKAXE));
    }
    /**
      * @void registerSilkTouch
      */

    public function registerSilkTouch(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::SILK_TOUCH, "Silk touch", 2, 0, Enchantment::SLOT_TOOL));
        new SilkTouch($this);
    }

    /**
     * @void registerUnbreaking
     */

    public function registerUnbreaking(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::UNBREAKING, "Unbreaking", 0, 0, Enchantment::SLOT_TOOL));
        new Unbreaking($this);
    }

    /**
     * @void registerFortune
     */

    public function registerFortune(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::FORTUNE, "Fortune", 0, 0, Enchantment::SLOT_PICKAXE));
        new Fortune($this);
    }
    /**
      * @void registerPower
      */

    public function registerPower(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::POWER, "Power", 0, 0, Enchantment::SLOT_BOW));
        new Power($this);
    }

    /**
     * @void registerPunch
     */

    public function registerPunch(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::PUNCH, "Punch", 1, 0, Enchantment::SLOT_BOW));
        new Punch($this);
    }

    /**
     * @void registerFlame
     */

    public function registerFlame(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::FLAME, "Flame", 1, 0, Enchantment::SLOT_BOW));
        new Flame($this);
    }

    /**
     * @void registerInfinity
     */

    public function registerInfinity(){
        Enchantment::registerEnchantment(new Enchantment(Enchantment::INFINITY, "Infinity", 2, 0, Enchantment::SLOT_BOW));
        new Infinity($this);

    }
}