<?php

namespace fabrizio\mcmmo;

use pocketmine\player\Player;
use pocketmine\entity\Entity;
use fabrizio\mcmmo\mcMMO;

class mcMMOManager {

    private $plugin;
    private $rands = [1, 2, 3, 4];

    public function __construct(mcMMO $plugin) {
        $this->plugin = $plugin;
    }

    public function addXpV1(string $skill, Player $player) : bool {
        $plugin = $this->plugin;
        $rand = $this->rands[array_rand($this->rands)];
        if($plugin->getLevel($player, $skill) == 0){
            $data = $player->getName().":herbalism";
            $xp = $plugin->getXp($player, $skill);
            if($plugin->database()->get($player->getXuid())["text"] == true) {
                $player->sendActionBarMessage("§6??? §aSkill\n§7Learning new skill... ({$xp}/10)");
            }
            if($xp == 9){
                $plugin->setXp($skill, 0, $player);
                $plugin->addLevel($skill, 1, $player);
                return true;
            }
            $plugin->addXp($skill, 1, $player);
            return true;
        }
        $target = $plugin->getLevel($player, $skill) * 100;
        if($plugin->getXp($player, $skill) >= $target){
            $plugin->setXp($skill, 0, $player);
            $plugin->addLevel($skill, 1, $player);
            return true;
        }
        $this->plugin->addXp($skill, $rand, $player);
        $xp = $plugin->getXp($player, $skill);
        $level = (int) $plugin->getLevel($player, $skill);
        $targetXp = $level * 100;
        if($plugin->database()->get($player->getXuid())["text"] == true) {
            $skillName = ucfirst($skill);
            $player->sendActionBarMessage("§6{$skillName} §aSkill\n§fLevel: §6{$level} §f(§3{$xp}§7/§b{$targetXp}§f) §e+{$rand}");
        }
        return true;
    }

    public function addXpV2(string $skill, Player $player) : bool {
        $plugin = $this->plugin;
        $rand = $this->rands[array_rand($this->rands)];
        if($plugin->getLevel($player, $skill) == 0){
            $data = $player->getName().":herbalism";
            $xp = $plugin->getXp($player, $skill);
            if($plugin->database()->get($player->getXuid())["text"] == true) {
                $player->sendActionBarMessage("§6??? §aSkill\n§7Learning new skill... ({$xp}/10)");
            }
            if($xp == 9){
                $plugin->setXp($skill, 0, $player);
                $plugin->addLevel($skill, 1, $player);
                return true;
            }
            $plugin->addXp($skill, 1, $player);
            return true;
        }
        $target = $plugin->getLevel($player, $skill) * 100;
        if($plugin->getXp($player, $skill) >= $target){
            $plugin->setXp($skill, 0, $player);
            $plugin->addLevel($skill, 1, $player);
            return true;
        }
        $this->plugin->addXp($skill, 50, $player);
        $xp = $plugin->getXp($player, $skill);
        $level = (int) $plugin->getLevel($player, $skill);
        $targetXp = $level * 100;
        if($plugin->database()->get($player->getXuid())["text"] == true) {
            $skillName = ucfirst($skill);
            $player->sendActionBarMessage("§6{$skillName} §aSkill\n§fLevel: §6{$level} §f(§3{$xp}§7/§b{$targetXp}§f) §e+50");
        }
        return true;
    }
}