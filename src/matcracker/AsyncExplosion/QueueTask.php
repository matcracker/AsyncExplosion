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

use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;

final class QueueTask extends Task{

	private $queue = [];

	/**
	 * @param Vector3[] $vectors
	 * @param int       $worldId
	 */
	public function addInQueue(array $vectors, int $worldId) : void{
		if(!isset($this->queue[$worldId])){
			$this->initQueue($worldId);
		}

		$this->queue[$worldId]["vectors"] = array_merge($this->queue[$worldId]["vectors"], $vectors);
	}

	private function initQueue(int $worldId) : void{
		$this->queue[$worldId]["vectors"] = [];
	}

	public function onRun(int $currentTick) : void{
		/**@var int $worldId */
		foreach($this->queue as $worldId => $data){
			Server::getInstance()->getAsyncPool()->submitTask(new AsyncChunkSet($this->queue[$worldId]["vectors"], $worldId));
			$this->initQueue($worldId);
		}
	}
}