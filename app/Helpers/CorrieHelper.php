<?php

namespace App\Helpers;

use App\Helpers\DijkstraEdge;
use App\Helpers\DijkstraGraph;
use App\Helpers\DijkstraNode;
use App\Models\Person;
use DB;

class CorrieHelper
{
	public static function findRelationship(int $start, int $end) : array
	{
		// Prepare the output.
		$output = [
			'path'    => [],
			'message' => '',
		];
		if (!$start || !$end) {
			$output['message'] = 'Please select two people.';
			return $output;
		}

		// Create the graph.
		$graph = self::createGraph();

		// Find the path.
		$path = $graph->search($start, $end);

		// No path found.
		if (empty($path)) {
			$output['message'] = 'These people are not related... yet!';
			return $output;
		}

		// Build the message.
		$person1 = Person::find($end);
		$person2 = Person::find($start);
		$output['message'] = $person2->first_name . ' is ' . $person1->first_name;
		if ($start != $end) {
			$output['message'] .= '\'s ';
		}
		$message = [];
		foreach ($path as &$edge) {
			if (!empty($edge['relationship'])) {
				$message[] = $edge['relationship'];
			}
			unset($edge['id']);
			unset($edge['relationship']);
		}
		$output['message'] .= implode('\'s ', $message) . '.';
		$output['message'] = self::condenseRelationships($output['message']);

		// Set other output.
		$output['path'] = array_reverse($path);

		return $output;
	}

	private static function createGraph() : DijkstraGraph
	{
		$nodes = self::getNodes();
		$graph = new DijkstraGraph();
		$graph = self::addNodes($graph, $nodes);
		$graph = self::addBasicRelationships($graph, $nodes);
		$graph = self::addSiblingRelationships($graph, $nodes);
		return $graph;
	}

	private static function getNodes() : array
	{
		$people = DB::table('people')
			->select('id')
			->get();
		$nodes = [];
		foreach ($people as $person) {
			$nodes[$person->id] = new DijkstraNode($person->id);
		}
		return $nodes;
	}

	private static function addNodes(DijkstraGraph $graph, array $nodes) : DijkstraGraph
	{
		foreach ($nodes as $node) {
			$graph->addNode($node);
		}
		return $graph;
	}

	private static function addBasicRelationships(DijkstraGraph $graph, array $nodes) : DijkstraGraph
	{
		$rows = DB::table('person_person')
			->select('person_1_id', 'person_2_id', 'relationship', 'end_date', 'end_reason', 'p1.gender as p1_gender', 'p2.gender as p2_gender')
			->join('people as p1', 'p1.id', '=', 'person_person.person_1_id')
			->join('people as p2', 'p2.id', '=', 'person_person.person_2_id')
			->get();

		foreach ($rows as $row) {
			// Get the relationship names (eg. parent, child, spouse, sibling).
			$relationship = $row->relationship;
			$reverseRelationship = self::getReverseRelationship($row->relationship);
			$weight = 1;

			// Get the gendered relationship names (eg. mother/father, son/daughter, wife/husband, brother/sister).
			$relationship = self::getGenderedRelationship((string) $row->p1_gender, $relationship);
			$reverseRelationship = self::getGenderedRelationship((string) $row->p2_gender, $reverseRelationship);

			// Check if the relationship is over.
			if (!empty($row->end_date) || (!empty($row->end_reason) && $row->end_reason !== 'current')) {
				$relationship = 'ex-' . $relationship;
				$reverseRelationship = 'ex-' . $reverseRelationship;

				// Favour current relationships if they were previously divorced and then remarried.
				$weight = 2;
			}

			// Avoid using distant relationships if possible.
			$distantRelationships = [
				'fiance',
				'foster parent',
				'legal guardian',
				'surrogate parent',
				'distant relative',
			];
			if (in_array($row->relationship, $distantRelationships)) {
				$weight += 10000;
			}

			// Get the nodes.
			$n1 = $nodes[$row->person_1_id];
			$n2 = $nodes[$row->person_2_id];

			// Add the first relationship (eg. parent).
			$edge = new DijkstraEdge($n1, $n2, $weight, $reverseRelationship);
			$graph->addEdge($edge);

			// And the reverse relationship (eg. child).
			$edge = new DijkstraEdge($n2, $n1, $weight, $relationship);
			$graph->addEdge($edge);
		}

		return $graph;
	}

	private static function addSiblingRelationships(DijkstraGraph $graph, array $nodes) : DijkstraGraph
	{
		$rows = Person::all();

		foreach ($rows as $row) {
			$siblings = $row->getFullSiblingsAttribute();
			if ($siblings->count() > 0) {
				$siblings = $siblings->get();
				foreach ($siblings as $s) {
					$relationship = self::getGenderedRelationship((string) $row->gender, 'sibling');

					// Get the nodes.
					$n1 = $nodes[$row->id];
					$n2 = $nodes[$s->id];

					// Add the edge.
					$edge = new DijkstraEdge($n1, $n2, 1, $relationship);
					$graph->addEdge($edge);
				}
			}
		}

		return $graph;
	}

	private static function getReverseRelationship(string $relationship) : string
	{
		switch ($relationship) {
			case 'parent':
				return 'child';

			case 'adoptive parent':
				return 'adoptive child';

			case 'surrogate parent':
				return 'surrogate child';

			case 'foster parent':
				return 'foster child';

			default:
				return $relationship;
		}
		return $relationship;
	}

	// phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded
	private static function getGenderedRelationship(string $person1Gender, string $relationship) : string
	{
		switch ($relationship) {
			case 'parent':
				if ($person1Gender === 'M') {
					return 'father';
				} elseif ($person1Gender === 'F') {
					return 'mother';
				}
				break;

			case 'child':
				if ($person1Gender === 'M') {
					return 'son';
				} elseif ($person1Gender === 'F') {
					return 'daughter';
				}
				break;

			case 'spouse':
				if ($person1Gender === 'M') {
					return 'husband';
				} elseif ($person1Gender === 'F') {
					return 'wife';
				}
				break;

			case 'sibling':
				if ($person1Gender === 'M') {
					return 'brother';
				} elseif ($person1Gender === 'F') {
					return 'sister';
				}
				break;

			case 'ex-spouse':
				if ($person1Gender === 'M') {
					return 'ex-husband';
				} elseif ($person1Gender === 'F') {
					return 'ex-wife';
				}
				break;

			case 'adoptive child':
				if ($person1Gender === 'M') {
					return 'adoptive son';
				} elseif ($person1Gender === 'F') {
					return 'adoptive daughter';
				}
				break;

			case 'adoptive parent':
				if ($person1Gender === 'M') {
					return 'adoptive father';
				} elseif ($person1Gender === 'F') {
					return 'adoptive mother';
				}
				break;

			case 'foster child':
				if ($person1Gender === 'M') {
					return 'foster son';
				} elseif ($person1Gender === 'F') {
					return 'foster daughter';
				}
				break;

			case 'surrogate child':
				if ($person1Gender === 'M') {
					return 'surrogate son';
				} elseif ($person1Gender === 'F') {
					return 'surrogate daughter';
				}
				break;

			case 'fiance':
				if ($person1Gender === 'F') {
					return 'fiancee';
				}
				break;

			default:
				return $relationship;
		}

		return $relationship;
	}

	private static function condenseRelationships(string $output) : string
	{
		$replace = [
			'brother\'s wife'        => 'sister-in-law',
			'sister\'s wife'         => 'sister-in-law',
			'brother\'s ex-wife'     => 'ex-sister-in-law',
			'sister\'s ex-wife'      => 'ex-sister-in-law',

			'sister\'s husband'      => 'brother-in-law',
			'brother\'s husband'     => 'brother-in-law',
			'sister\'s ex-husband'   => 'ex-brother-in-law',
			'brother\'s ex-husband'  => 'ex-brother-in-law',

			'husband\'s mother'      => 'mother-in-law',
			'wife\'s mother'         => 'mother-in-law',
			'husband\'s father'      => 'father-in-law',
			'wife\'s father'         => 'father-in-law',

			'ex-husband\'s daughter' => 'stepdaughter',
			'ex-wife\'s daughter'    => 'stepdaughter',
			'ex-husband\'s son'      => 'stepson',
			'ex-wife\'s son'         => 'stepson',

			'husband\'s daughter'    => 'stepdaughter',
			'wife\'s daughter'       => 'stepdaughter',
			'husband\'s son'         => 'stepson',
			'wife\'s son'            => 'stepson',

			'mother\'s sister'       => 'aunt',
			'father\'s sister'       => 'aunt',
			'mother\'s brother'      => 'uncle',
			'father\'s brother'      => 'uncle',

			'father\'s mother'       => 'grandmother',
			'mother\'s mother'       => 'grandmother',
			'father\'s father'       => 'grandfather',
			'mother\'s father'       => 'grandfather',

			'mother\'s adoptive daughter' => 'adoptive sister',
			'father\'s adoptive daughter' => 'adoptive sister',
			'mother\'s adoptive son' => 'adoptive brother',
			'father\'s adoptive son' => 'adoptive brother',

			'\'s son\'s wife'            => '\'s daughter-in-law',
			'\'s daughter\'s wife'       => '\'s daughter-in-law',
			'\'s son\'s ex-wife'         => '\'s ex-daughter-in-law',
			'\'s daughter\'s ex-wife'    => '\'s ex-daughter-in-law',

			'\'s son\'s husband'         => '\'s son-in-law',
			'\'s daughter\'s husband'    => '\'s son-in-law',
			'\'s son\'s ex-husband'      => '\'s ex-son-in-law',
			'\'s daughter\'s ex-husband' => '\'s ex-son-in-law',

			'\'s daughter\'s daughter'   => '\'s granddaughter',
			'\'s son\'s daughter'        => '\'s granddaughter',
			'\'s daughter\'s son'        => '\'s grandson',
			'\'s son\'s son'             => '\'s grandson',
		];
		$output = strtr($output, $replace);

		return $output;
	}
}
