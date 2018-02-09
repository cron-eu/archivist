<?php

namespace PunktDe\Archivist;

/*
 * This file is part of the PunktDe.Archivist package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class NodeSignalInterceptor
{
    /**
     * @Flow\InjectConfiguration(path="sortingInstructions")
     * @var array
     */
    protected $sortingInstructions = [];

    /**
     * @var Archivist
     */
    protected $archivist = null;

    /**
     * @param NodeInterface $node
     */
    public function nodeAdded(NodeInterface $node)
    {
        if (!array_key_exists($node->getNodeType()->getName(), $this->sortingInstructions)) {
            return;
        }

        $this->createArchivist()->organizeNode($node, $this->sortingInstructions[$node->getNodeType()->getName()]);
    }

    /**
     * @param NodeInterface $node
     */
    public function nodeUpdated(NodeInterface $node)
    {
        if($this->createArchivist()->restorePathIfOrganizedDuringThisRequest($node)) {
            return;
        }

        if (array_key_exists($node->getNodeType()->getName(), $this->sortingInstructions)) {
            $this->createArchivist()->organizeNode($node, $this->sortingInstructions[$node->getNodeType()->getName()]);
        }
    }

    /**
     * @return Archivist
     */
    protected function createArchivist(): Archivist
    {
        if ($this->archivist === null) {
            $this->archivist = new Archivist();
        }
        return $this->archivist;
    }
}
