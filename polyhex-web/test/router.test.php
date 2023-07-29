<?php

require __DIR__ . "/../vendor/autoload.php";

use Polyhex\Web\Routing\TreeRouter\Node;

$tree = new Node();

$tree->insert('/', 'root');
$tree->insert('/about', 'About us');
$tree->insert('/another', 'another router');
$tree->insert('/:another/nested', 'another nested route');
$tree->insert('/unrelated/:param', 'unrelated');

var_dump($tree->at('/hmm/nested'));