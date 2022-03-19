<?php

namespace App\Helpers;

use App\Helpers\DijkstraQueue;
use App\Models\Person;

class DijkstraGraph
{
	protected $nodes = [];
	protected $edges = [];

	protected $undiscovered = 0;
	protected $discovered = 1;
	protected $processed = 2;

	protected $list = [];

	public function search($from, $to)
	{
		foreach ($this->nodes as &$node) {
			$node->parent = null;
			$node->distance = INF;
			$node->state = $this->undiscovered;
		}

		$fromNode = $this->nodes[$from];

		$fromNode->state = $this->discovered;
		$fromNode->distance = 0;

		// Make a queue and put the node you are starting from in the queue.
		$q = new DijkstraQueue(false);
		$q->enqueue($fromNode);

		// Visit the first node in the queue and look at all its edges you have not yet looked at.
		// If one of its edges leads to the node you are looking for, you're done.
		// If not, add all the nodes connected to the first node to the queue, and repeat for every other node in the queue until you find the node you want.
		while (!empty($q->getNumNodes())) {
			$n1 = $q->dequeue();

			$neighbors = $this->getEdges($n1);

			foreach ($neighbors as &$edge) {
				$n2 = $this->getOtherNode($n1, $edge);

				if ($n2->state == $this->undiscovered) {
					$q->enqueue($n2);
				}

				$n2->state = $this->discovered;
				$newDist = $n1->distance + $edge->weight;

				if ($newDist < $n2->distance) {
					$n2->distance = $newDist;
					$n2->parent = $n1;
				}
			}
		}

		$path = [];
		$toNode = $this->nodes[$to];
		if (!empty($toNode->parent) || $from == $to) {
			do {
				$edge = $this->findEdge($toNode, $toNode->parent);

				$person = Person::find($toNode->id);
				$path[] = [
					'id'           => $toNode->id,
					'image'        => $person->filename,
					'status'       => empty($person->deathdate) ? 'alive' : 'dead',
					'name'         => $person->getNameAttribute(),
					'relationship' => !empty($edge) ? $edge->relationship : null,
				];

				$toNode = $toNode->parent;
			} while (!empty($toNode));
		}

		return $path;
	}

	protected function getEdges($node)
	{
		$edges = [];
		foreach ($this->edges as $edge) {
			if ($edge->n1->id == $node->id || $edge->n2->id == $node->id) {
				$edges[] = $edge;
			}
		}
		return $edges;
	}

	public function addNode($node)
	{
		$this->nodes[$node->id] = $node;
	}

	public function addEdge($edge)
	{
		$this->edges[] = $edge;
	}

	protected function getOtherNode($node, $edge)
	{
		return $edge->n1->id == $node->id ? $edge->n2 : $edge->n1;
	}

	protected function findEdge($n1, $n2)
	{
		if (empty($n1) || empty($n2)) {
			return null;
		}
		foreach ($this->edges as $edge) {
			if ($edge->n1->id == $n1->id && $edge->n2->id == $n2->id) {
				return $edge;
			}
		}
		return null;
	}
}
