<?php declare(strict_types=1);

namespace Shopware\Core\Framework\DataAbstractionLayer\Write;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;

/**
 * Defines the current state of an entity in relation to the parent-child inheritance and
 * existence in the storage or command queue.
 */
class EntityExistence
{
    /**
     * @var array
     */
    private $primaryKey;

    /**
     * @var bool
     */
    private $exists;

    /**
     * @var string|EntityDefinition
     */
    private $definition;

    /**
     * @var bool
     */
    private $isChild;

    /**
     * @var bool
     */
    private $wasChild;

    /**
     * @var array
     */
    private $state;

    public function __construct(
        string $definition,
        array $primaryKey,
        bool $exists,
        bool $isChild,
        bool $wasChild,
        array $state
    ) {
        $this->primaryKey = $primaryKey;
        $this->exists = $exists;
        $this->definition = $definition;
        $this->isChild = $isChild;
        $this->wasChild = $wasChild;
        $this->state = $state;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function getPrimaryKey(): array
    {
        return $this->primaryKey;
    }

    public function isChild(): bool
    {
        return $this->isChild;
    }

    public function wasChild(): bool
    {
        return $this->wasChild;
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    public function childChangedToParent(): bool
    {
        if (!$this->wasChild()) {
            return false;
        }

        return !$this->isChild();
    }

    public function getState(): array
    {
        return $this->state;
    }
}
