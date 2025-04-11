<?php

namespace Src\Classes;

use Psr\Container\ContainerInterface;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupInterface;
use Symfony\WebpackEncoreBundle\Exception\UndefinedBuildException;
use Symfony\WebpackEncoreBundle\Asset\EntrypointLookupCollectionInterface;

/**
 * Aggregate the different entry points configured in the container.
 *
 * Retrieve the EntrypointLookup instance from the given key.
 *
 * @final
 */
class EntrypointLookupCollection implements EntrypointLookupCollectionInterface
{
    private $buildEntrypoints;

    private $defaultBuildName;

    public function __construct(ContainerInterface $buildEntrypoints, ?string $defaultBuildName = null)
    {
        $this->buildEntrypoints = $buildEntrypoints;
        $this->defaultBuildName = $defaultBuildName;
    }

    public function getEntrypointLookup(?string $buildName = null): EntrypointLookupInterface
    {
        if (null === $buildName) {
            if (null === $this->defaultBuildName) {
                throw new UndefinedBuildException('There is no default build configured: please pass an argument to getEntrypointLookup().');
            }

            $buildName = $this->defaultBuildName;
        }
        $buildName = $this->defaultBuildName;

        if (!$this->buildEntrypoints->has($buildName)) {
            throw new UndefinedBuildException(\sprintf('The build "%s" is not configured', $buildName));
        }

        return $this->buildEntrypoints->get($buildName);
    }
}
