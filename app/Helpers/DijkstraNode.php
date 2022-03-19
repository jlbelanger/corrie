<?php

namespace App\Helpers;

class DijkstraNode
{
	public $id;
	public $state;
	public $parent;
	public $distance;

	public function __construct($id = null)
	{
		$this->id = $id;
		$this->state = null;
		$this->parent = null;
		$this->distance = INF;
	}
}
