<?php

namespace fabrizio\mcmmo\menu;

use pocketmine\player\Player;
use fabrizio\mcmmo\mcMMO;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\item\Item;
use fabrizio\mcmmo\closure\Closure;
use fabrizio\mcmmo\menu\{Menu, Profile, Setting};

class Setting {
    
    public function __construct(mcMMO $plugin, Player $player) {
        $form = new CustomForm(function(Player $player, $data) {
            if($data == null){
                new Menu($player->getServer()->getPluginManager()->getPlugin("mcMMO"), $player);
                return true;
            }
            $uid = $player->getXuid();
            $prefix = $player->getServer()->getPluginManager()->getPlugin("mcMMO")->prefix ?? "§a[§6mcMMO§a]§r";
            if($data[0] == true){
                $player->getServer()->getPluginManager()->getPlugin("mcMMO")->database()->setNested("$uid.text", true);
                $player->sendMessage("$prefix §aChange the setting to: text-bar: true");
                return true;
            }
            $player->getServer()->getPluginManager()->getPlugin("mcMMO")->database()->setNested("$uid.text", false);
            $player->sendMessage("$prefix §aChange the setting to: text-bar: false");
            return true;
        });
        $form->setTitle("§6mcMMO §aSettings");
        $uiid = $player->getXuid();
        $form->addToggle("Text Bar.", $plugin->database()->get($uiid)["text"]);
        $form->sendToPlayer($player);
        return $form;
    }
}