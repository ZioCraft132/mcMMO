# Description
A plugin that can make survival a semi mmo rpg.

I want to make minecraft to get extra features like mmo rpg. that's why I made this plugin.

But here I can only add a few features, because I don't know the flexibility of the [Pocketmine-MP](https://github.com/pmmp/PocketMine-MP) code.

# Skills
The following is a list of skills contained in this plugin.

|Skill Name|Skill Info|
|----------|----------|
|Acrobatic|Acrobatic skills increase xp if you fall, swim, and take damage from fire.|
|Archery|Archery skill increases xp when you shoot an arrow from an arrow, if it hits an entity xp +10|
|Builder|Skill builder earns xp when you place solid block.|
|Farmer|Farmer skills get xp when you Farm.|
|Fighter|Fighter skills gain xp when you hit a player. if killing xp player +50.|
|Fisher|Fisher skill, you can get xp from fishing.|
|Lumberjack|Lumberjack skill gains XP when cutting trees.|
|Miner|Mining skills gain XP when destroying mineral blocks or stones.|

# API
API. </br>
```php
$api = $this->getServer()->getPluginManager()->getPlugin("mcMMO");
```
To get skill level information. </br>
```php
$api->getLevel(string $skillId, Player $player) : string ;
```
To get skill xp level information. </br>
```php
$api->getXp(string $skillId, Player $player) : string ;
```
To add skill xp or level to player. </br>
```php
$api->addXp(string $skillId, int $count, string/Player $player) : bool ;
$api->addXp(string $skillId, int $count, string/Player $player) : bool ;
```

# Permission
|Permission|Status|
|----------|------|
|mcmmo.command.mcmmo|```default```|
|mcmmo.command.help|```op```|
|mcmmo.command.addlevel|```op```|
|mcmmo.command.addxp|```op```|
|mcmmo.command.reducelevel|```op```|
|mcmmo.command.reducexp|```op```|
|mcmmo.command.version|```op```|

# Download
[here](https://poggit.pmmp.io/ci/ZioCraft132/mcMMO/~)
