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

use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

final class AsyncChunkSet extends AsyncTask{

	/**@var string[] $chunks */
	private $chunks;
	/**@var Vector3[] $vectors */
	private $vectors;
	/**@var int $worldId */
	private $worldId;

	/**
	 * Async constructor.
	 *
	 * @param Vector3[] $vectors
	 * @param int       $worldId
	 */
	public function __construct(array $vectors, int $worldId){
		$this->chunks = self::serializeTouchedChunks($vectors, $worldId);
		$this->vectors = $vectors;
		$this->worldId = $worldId;
	}

	public function onRun() : void{
		$chunks = (array) $this->chunks;

		foreach($chunks as $hash => $chunkData){
			$chunks[$hash] = Chunk::fastDeserialize($chunkData);
		}
		/**@var Chunk[] $chunks */
		foreach($this->vectors as $vector){
			$index = Level::chunkHash((int) $vector->getX() >> 4, (int) $vector->getZ() >> 4);
			if(isset($chunks[$index])){
				$chunks[$index]->setBlock((int) $vector->getX() & 0x0f, $vector->getY(), (int) $vector->getZ() & 0x0f, 0, 0);
			}
		}
		$this->setResult($chunks);
	}

	public function onCompletion(Server $server) : void{
		$world = Server::getInstance()->getLevel($this->worldId);
		if($world !== null){
			/**@var Chunk[] $chunks */
			$chunks = $this->getResult();
			foreach($chunks as $chunk){
				$world->setChunk($chunk->getX(), $chunk->getZ(), $chunk, false);
			}
		}
	}

	/**
	 * @param Vector3[] $vectors
	 * @param int       $worldId
	 *
	 * @return string[]
	 */
	private static function serializeTouchedChunks(array $vectors, int $worldId) : array{
		$touchedChunks = [];
		$world = Server::getInstance()->getLevel($worldId);
		foreach($vectors as $vector){
			$x = $vector->getX() >> 4;
			$z = $vector->getZ() >> 4;
			$chunk = $world->getChunk($x, $z);
			if($chunk === null){
				continue;
			}
			$touchedChunks[Level::chunkHash($x, $z)] = $chunk->fastSerialize();
		}

		return $touchedChunks;
	}
}