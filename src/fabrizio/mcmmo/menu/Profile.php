<?php

namespace fabrizio\mcmmo\menu;

use pocketmine\player\Player;
use fabrizio\mcmmo\mcMMO;
use jojoe77777\FormAPI\SimpleForm;
use fabrizio\mcmmo\closure\Closure;
use fabrizio\mcmmo\menu\Menu;

class Profile {

    public function __construct(mcMMO $plugin, Player $player, Player $target = null) {
        if($target == null){
            $target = $player;
        }
        $form = new SimpleForm(function(Player $player, int $data = null) {
            if($data == null){
                new Menu($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                return true;
            }
            switch($data){
                case 0:
                    new Menu($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                    return true;
                break;
            }
        });
        $form->setTitle("§6mcMMO §aProfile");
        $array = [];
        foreach($plugin->skills as $skill){
            $skillName = ucfirst($skill);
            $level = $plugin->getLevel($target, $skill);
            $xp = $plugin->getXp($target, $skill);
            $targetXp = $level * 100;
            $string = ["§7» §6{$skillName}.\n§7» §6Level§f: §a{$level} §f(§3{$xp}§7/§b{$targetXp}§f)"];
            $array = array_merge($array, $string);
        }
        $name = $target->getName();
        $form->setContent("\n§1{$name} §bProfile.\n\n".implode("\n\n", $array));
        $form->addButton("§lBack \n§r§eClick to back. ");
        $form->sendToPlayer($player);
        return $form;
    }
}