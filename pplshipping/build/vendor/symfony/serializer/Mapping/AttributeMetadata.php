<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PPLShipping\Symfony\Component\Serializer\Mapping;

/**
 * {@inheritdoc}
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class AttributeMetadata implements AttributeMetadataInterface
{
    /**
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getName()} instead.
     */
    public $name;
    /**
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getGroups()} instead.
     */
    public $groups = [];
    /**
     * @var int|null
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getMaxDepth()} instead.
     */
    public $maxDepth;
    /**
     * @var string|null
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getSerializedName()} instead.
     */
    public $serializedName;
    /**
     * @var bool
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link isIgnored()} instead.
     */
    public $ignore = \false;
    /**
     * @var array[] Normalization contexts per group name ("*" applies to all groups)
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getNormalizationContexts()} instead.
     */
    public $normalizationContexts = [];
    /**
     * @var array[] Denormalization contexts per group name ("*" applies to all groups)
     *
     * @internal This property is public in order to reduce the size of the
     *           class' serialized representation. Do not access it. Use
     *           {@link getDenormalizationContexts()} instead.
     */
    public $denormalizationContexts = [];
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * {@inheritdoc}
     */
    public function addGroup(string $group)
    {
        if (!\in_array($group, $this->groups)) {
            $this->groups[] = $group;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getGroups() : array
    {
        return $this->groups;
    }
    /**
     * {@inheritdoc}
     */
    public function setMaxDepth(?int $maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }
    /**
     * {@inheritdoc}
     */
    public function getMaxDepth()
    {
        return $this->maxDepth;
    }
    /**
     * {@inheritdoc}
     */
    public function setSerializedName(?string $serializedName = null)
    {
        $this->serializedName = $serializedName;
    }
    /**
     * {@inheritdoc}
     */
    public function getSerializedName() : ?string
    {
        return $this->serializedName;
    }
    /**
     * {@inheritdoc}
     */
    public function setIgnore(bool $ignore)
    {
        $this->ignore = $ignore;
    }
    /**
     * {@inheritdoc}
     */
    public function isIgnored() : bool
    {
        return $this->ignore;
    }
    /**
     * {@inheritdoc}
     */
    public function getNormalizationContexts() : array
    {
        return $this->normalizationContexts;
    }
    /**
     * {@inheritdoc}
     */
    public function getNormalizationContextForGroups(array $groups) : array
    {
        $contexts = [];
        foreach ($groups as $group) {
            $contexts[] = $this->normalizationContexts[$group] ?? [];
        }
        return \array_merge($this->normalizationContexts['*'] ?? [], ...$contexts);
    }
    /**
     * {@inheritdoc}
     */
    public function setNormalizationContextForGroups(array $context, array $groups = []) : void
    {
        if (!$groups) {
            $this->normalizationContexts['*'] = $context;
        }
        foreach ($groups as $group) {
            $this->normalizationContexts[$group] = $context;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDenormalizationContexts() : array
    {
        return $this->denormalizationContexts;
    }
    /**
     * {@inheritdoc}
     */
    public function getDenormalizationContextForGroups(array $groups) : array
    {
        $contexts = [];
        foreach ($groups as $group) {
            $contexts[] = $this->denormalizationContexts[$group] ?? [];
        }
        return \array_merge($this->denormalizationContexts['*'] ?? [], ...$contexts);
    }
    /**
     * {@inheritdoc}
     */
    public function setDenormalizationContextForGroups(array $context, array $groups = []) : void
    {
        if (!$groups) {
            $this->denormalizationContexts['*'] = $context;
        }
        foreach ($groups as $group) {
            $this->denormalizationContexts[$group] = $context;
        }
    }
    /**
     * {@inheritdoc}
     */
    public function merge(AttributeMetadataInterface $attributeMetadata)
    {
        foreach ($attributeMetadata->getGroups() as $group) {
            $this->addGroup($group);
        }
        // Overwrite only if not defined
        if (null === $this->maxDepth) {
            $this->maxDepth = $attributeMetadata->getMaxDepth();
        }
        // Overwrite only if not defined
        if (null === $this->serializedName) {
            $this->serializedName = $attributeMetadata->getSerializedName();
        }
        // Overwrite only if both contexts are empty
        if (!$this->normalizationContexts && !$this->denormalizationContexts) {
            $this->normalizationContexts = $attributeMetadata->getNormalizationContexts();
            $this->denormalizationContexts = $attributeMetadata->getDenormalizationContexts();
        }
        if ($ignore = $attributeMetadata->isIgnored()) {
            $this->ignore = $ignore;
        }
    }
    /**
     * Returns the names of the properties that should be serialized.
     *
     * @return string[]
     */
    public function __sleep()
    {
        return ['name', 'groups', 'maxDepth', 'serializedName', 'ignore', 'normalizationContexts', 'denormalizationContexts'];
    }
}
