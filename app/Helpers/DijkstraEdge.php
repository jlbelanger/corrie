<?php

namespace App\Helpers;

class DijkstraEdge
{
	public $n1;
	public $n2;
	public $weight;
	public $relationship;

	public function __construct($n1, $n2, $weight = 1, $relationship = null)
	{
		$this->n1 = $n1;
		$this->n2 = $n2;
		$this->weight = $weight;
		$this->relationship = $relationship;
	}
}
