<?php

namespace fabrizio\mcmmo;

use pocketmine\block\BlockLegacyIds as BlockIds;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\block\Opaque;
use pocketmine\item\ItemIds;

class mcmmoListener implements Listener {

    private $plugin;

    public function __construct(mcMMO $plugin) {
        $this->plugin = $plugin;
    }

    public function onJoin(PlayerJoinEvent $event) {
        $uid = $event->getPlayer()->getXuid();
        $player = $event->getPlayer();
        $prefix = $this->plugin->prefix ?? "§a[§bmcMMO§a]";
        if($this->plugin->database()->getNested($uid.".isregister") == "") {
            $name = $player->getName();
            $uid = $player->getXuid();
            $this->plugin->getLogger()->info("$prefix §cPlayer {$name} with XUID {$uid} not found in database. Creating one.");
            $this->plugin->database()->set($uid, ["isregister" => true, "username" => $name, "text" => true]);
            $arrays = $this->plugin->database()->getAll();
            foreach($this->plugin->skills as $skill) {
                $array = [$skill => ["xp" => 1, "level" => 0]];
                $tes = $this->plugin->database()->getAll();
                $this->plugin->database()->set($uid, array_merge($tes[$uid], $array));
                $this->plugin->database()->save();
            }
            $this->plugin->database()->save();
            $this->plugin->getLogger()->info("$prefix §aSuccess making a database.");
            $this->plugin->setXuids($name, $uid);
            return true;
        }
    }

    public function onDeath(PlayerDeathEvent $event) {
        $plugin = $this->plugin;
        $death = $event->getPlayer();
        $cause = $death->getLastDamageCause();
		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();
			if($damager instanceof Player){
				$player = $damager;
				if($player->getInventory()->getItemInHand()->getId() == ItemIds::BOW){
				    $plugin->getmcMMOManager()->addXpV2(mcMMO::ARCHERY, $player);
				    return true;
				}
				$plugin->getmcMMOManager()->addXpV2(mcMMO::FIGHTER, $player);
				return true;
			}
		}
    }

    public function onPlace(BlockPlaceEvent $event) {
        if($event->isCancelled()){
            return true;
        }
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $plugin = $this->plugin;
        switch($block->getId()){
            case 2:
            case 31:
            case 37:
            case 38:
            case 39:
            case 40:
            case 111:
            case 106:
            case 175:
            case 471:
            case BlockIds::POTATOES:
            case BlockIds::CARROTS:
            case BlockIds::MELON_STEM:
            case BlockIds::PUMPKIN_STEM:
            case BlockIds::SAPLING:
            case BlockIds::BAMBOO_SAPLING:
            case BlockIds::WHEAT_BLOCK:
                $plugin->getmcMMOManager()->addXpV1(mcMMO::FARMER, $player);
                return true;
            break;
            default:
                if(!$block instanceof Opaque){
                    return true;
                }
                $plugin->getmcMMOManager()->addXpV1(mcMMO::BUILDER, $player);
            break;
        }
    }

    public function onBreak(BlockBreakEvent $event) {
        if($event->isCancelled()){
            return true;
        }
        $plugin = $this->plugin;
        $player = $event->getPlayer();
        $block = $event->getBlock();
        switch($block->getId()){
            case 2:
            case 31:
            case 37:
            case 38:
            case 39:
            case 40:
            case 111:
            case 106:
            case 175:
            case 471:
            case BlockIds::POTATOES:
            case BlockIds::CARROTS:
            case BlockIds::PUMPKIN:
            case BlockIds::BAMBOO:
            case BlockIds::MELON_BLOCK:
            case BlockIds::MELON_STEM:
            case BlockIds::PUMPKIN_STEM:
            case BlockIds::SAPLING:
            case BlockIds::BAMBOO_SAPLING:
            case BlockIds::WHEAT_BLOCK:
                $plugin->getmcMMOManager()->addXpV1(mcMMO::FARMER, $player);
                return true;
            break;
            case 14:
            case 15:
            case 16:
            case 21:
            case 56:
            case 73:
            case 129:
            case 153:
            case 543:
            case 1:
            case 4:
                $plugin->getmcMMOManager()->addXpV1(mcMMO::MINER, $player);
                return true;
            break;
            case 162:
            case 17:
            case 163:
            case 18:
                $plugin->getmcMMOManager()->addXpV1(mcMMO::LUMBERJACK, $player);
                return true;
            break;
        }
    }

    public function onUse(PlayerItemUseEvent $event) {
        if($event->isCancelled()) {
            return true;
        }
        $plugin = $this->plugin;
        $player = $event->getPlayer();
        $item = $event->getItem();
        switch($item->getId()){
            case 346:
                $plugin->getmcMMOManager()->addXpV1(mcMMO::FISHER, $player);
                return true;
            break;
        }
    }

    public function onDmg(EntityDamageByEntityEvent $event) {
        if($event->isCancelled()){
            return true;
        }
        $plugin = $this->plugin;
        $damager = $event->getDamager();
        if($damager instanceof Player){
            $player = $damager;
            $plugin->getmcMMOManager()->addXpV1(mcMMO::FIGHTER, $player);
            return true;
        }
    }

    public function onDamage(EntityDamageEvent $event) {
        if($event->isCancelled()){
            return true;
        }
        $player = $event->getEntity();
        $plugin = $this->plugin;
        switch($event->getCause()){
            case 4:
            case 5:
            case 6:
                $plugin->getmcMMOManager()->addXpV1(mcMMO::ACROBATIC, $player);
                return true;
            break;
        }
    }
    
    public function onBow(EntityShootBowEvent $event) {
        if($event->isCancelled()){
            return true;
        }
        $plugin = $this->plugin;
        if($event->getEntity() instanceof Player){
            $player = $event->getEntity();
            $plugin->getmcMMOManager()->addXpV1(mcMMO::ARCHERY, $player);
            return true;
        }
    }
}