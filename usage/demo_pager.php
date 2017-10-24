<?php

require '../vendor/autoload.php';

use Libido\Pager\Pager;

$p = new Pager(1, 100, 20);
echo 'Page: ' . $p->getPage() . PHP_EOL;
echo 'Nb Entries: ' . $p->getNbEntries() . PHP_EOL;
echo 'Items Per Page: ' . $p->getItemsPerPage() . PHP_EOL;
echo 'Has Previous Page: ' . (int) $p->hasPreviousPage() . PHP_EOL;
echo 'Previous Page: ' . $p->getPreviousPage() . PHP_EOL;
echo 'Has Next Page: ' . (int) $p->hasNextPage() . PHP_EOL;
echo 'Next Page: ' . $p->getNextPage() . PHP_EOL;
echo 'Min Entry: ' . $p->getMinEntry() . PHP_EOL;
echo 'Max Entry: ' . $p->getMaxEntry() . PHP_EOL;
echo 'First Page: ' . $p->getFirstPage() . PHP_EOL;
echo 'Last Page: ' . $p->getLastPage() . PHP_EOL;
echo 'Offset: ' . $p->getOffset() . PHP_EOL;