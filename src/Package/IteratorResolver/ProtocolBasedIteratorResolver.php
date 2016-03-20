<?php

namespace Alchemy\Zippy\Package\IteratorResolver;

use Alchemy\Zippy\Package\PackagedResource;
use Alchemy\Zippy\Package\PackagedResourceIteratorResolver;

class ProtocolBasedIteratorResolver implements PackagedResourceIteratorResolver
{

    private $factories = [];

    /**
     * @param string $protocol
     * @param callable $factory
     */
    public function addFactory($protocol, callable $factory)
    {
        $this->factories[$protocol] = $factory;
    }

    public function getFactory($protocol)
    {
        if (! isset($this->factories[$protocol])) {
            throw new \RuntimeException('Unsupported protocol: ' . $protocol);
        }

        return $this->factories[$protocol];

    }

    public function resolveIterator(PackagedResource $container)
    {
        $protocol = $container->getAbsoluteUri()->getProtocol();
        $factory = $this->getFactory($protocol);

        $iterator = $factory($container);

        if (! $iterator) {
            throw new \RuntimeException('Unsupported protocol: ' . $protocol);
        }

        return $iterator;

    }
}
