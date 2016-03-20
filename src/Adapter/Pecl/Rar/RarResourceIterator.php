<?php

namespace Alchemy\Zippy\Adapter\Pecl\Rar;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIterator;
use Alchemy\Zippy\Resource\ResourceUri;

class RarResourceIterator implements PackagedResourceIterator
{
    /**
     * @var \RarArchive
     */
    private $archive;

    /**
     * @var PackagedResource
     */
    private $container;

    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param PackagedResource $container
     */
    public function __construct(PackagedResource $container)
    {
        $this->container = $container;
        $this->archive = \RarArchive::open($container->getAbsoluteUri()->getResource());

        $this->readerResolver = new RarResourceReaderResolver($this->archive);
    }

    /**
     * @return \ArrayIterator|\Iterator
     */
    private function getIterator()
    {
        if (! $this->iterator) {
            $this->iterator = new \ArrayIterator($this->archive->getEntries());
        }

        return $this->iterator;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->getIterator()->next();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->getIterator()->current();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->getIterator()->valid();
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->getIterator()->rewind();
    }

    /**
     * @return PackagedResource
     */
    public function current()
    {
        $current = $this->getIterator()->current();

        return new PackagedResource(
            ResourceUri::fromString($current->getName()),
            $this->readerResolver,
            $this->container->getWriterResolver(),
            $this->container
        );
    }
}
