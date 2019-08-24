<?php

/*
 * AsyncExplosion
 *
 * Copyright (C) 2019
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author matcracker
 * @link https://www.github.com/matcracker/AsyncExplosion
 *
*/

declare(strict_types=1);

namespace matcracker\AsyncExplosion;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

final class Main extends PluginBase implements Listener{

	/**@var QueueTask $queueTask */
	private $queueTask;

	public function onEnable() : void{
		$this->queueTask = new QueueTask();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getScheduler()->scheduleRepeatingTask($this->queueTask, 20);
	}

	public function onEntityExplode(EntityExplodeEvent $event) : void{
		$blockList = $event->getBlockList();
		/**@var Vector3[] $vectors */
		$vectors = array_map(static function(Block $block) : Vector3{
			return $block->asVector3()->floor();
		}, $blockList);

		$this->queueTask->addInQueue($vectors, $event->getPosition()->getLevel()->getId());
		$event->setBlockList(array_filter($blockList, static function(Block $block) : bool{ //Allow ignites of other TNTs
			return $block->getId() === BlockIds::TNT;
		}));
	}
}