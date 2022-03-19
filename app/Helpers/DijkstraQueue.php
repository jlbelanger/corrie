<?php

namespace App\Helpers;

class DijkstraQueue
{
	protected $nodes;
	protected $numNodes;

	public function __construct()
	{
		$this->nodes = [];
		$this->numNodes = 0;
	}

	public function enqueue($node)
	{
		$this->nodes[] = $node;
		$this->numNodes++;
	}

	public function dequeue()
	{
		if ($this->numNodes <= 0) {
			return;
		}
		$n = $this->nodes[0];
		unset($this->nodes[0]);
		$this->nodes = array_values($this->nodes);
		$this->numNodes--;
		return $n;
	}

	public function getNodes()
	{
		return $this->nodes;
	}

	public function getNumNodes()
	{
		return $this->numNodes;
	}
}
