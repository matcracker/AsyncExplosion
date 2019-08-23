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
use pocketmine\block\BlockFactory;
use pocketmine\level\Position;

final class SerializableBlock{
	/**@var int $id */
	private $id;
	/**@var int $meta */
	private $meta;
	/**@var int $x */
	protected $x;
	/**@var int $y */
	protected $y;
	/**@var int $z */
	protected $z;

	public function __construct(int $id, int $meta, int $x, int $y, int $z){
		$this->id = $id;
		$this->meta = $meta;
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}

	/**
	 * @param Block $block
	 *
	 * @return SerializableBlock
	 */
	public static function toSerializableBlock(Block $block) : self{
		return new self($block->getId(), $block->getDamage(), $block->getX(), $block->getY(), $block->getZ());
	}

	public function toBlock() : Block{
		return BlockFactory::get($this->id, $this->meta, new Position($this->x, $this->y, $this->z));
	}

	/**
	 * @return int
	 */
	public function getId() : int{
		return $this->id;
	}

	/**
	 * @return int
	 */
	public function getMeta() : int{
		return $this->meta;
	}

	/**
	 * @return int
	 */
	public function getX() : int{
		return $this->x;
	}

	/**
	 * @return int
	 */
	public function getY() : int{
		return $this->y;
	}

	/**
	 * @return int
	 */
	public function getZ() : int{
		return $this->z;
	}

	public function __toString() : string{
		return "SerializableBlock({$this->id}:{$this->meta})";
	}
}