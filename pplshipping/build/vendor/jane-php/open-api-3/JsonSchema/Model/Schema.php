<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Schema extends \ArrayObject
{
    /**
     * 
     *
     * @var string|null
     */
    protected $title;
    /**
     * 
     *
     * @var float|null
     */
    protected $multipleOf;
    /**
     * 
     *
     * @var float|null
     */
    protected $maximum;
    /**
     * 
     *
     * @var bool|null
     */
    protected $exclusiveMaximum = \false;
    /**
     * 
     *
     * @var float|null
     */
    protected $minimum;
    /**
     * 
     *
     * @var bool|null
     */
    protected $exclusiveMinimum = \false;
    /**
     * 
     *
     * @var int|null
     */
    protected $maxLength;
    /**
     * 
     *
     * @var int|null
     */
    protected $minLength = 0;
    /**
     * 
     *
     * @var string|null
     */
    protected $pattern;
    /**
     * 
     *
     * @var int|null
     */
    protected $maxItems;
    /**
     * 
     *
     * @var int|null
     */
    protected $minItems = 0;
    /**
     * 
     *
     * @var bool|null
     */
    protected $uniqueItems = \false;
    /**
     * 
     *
     * @var int|null
     */
    protected $maxProperties;
    /**
     * 
     *
     * @var int|null
     */
    protected $minProperties = 0;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $required;
    /**
     * 
     *
     * @var mixed[]|null
     */
    protected $enum;
    /**
     * 
     *
     * @var string|null
     */
    protected $type;
    /**
     * 
     *
     * @var Schema|Reference|null
     */
    protected $not;
    /**
     * 
     *
     * @var Schema[]|Reference[]|null
     */
    protected $allOf;
    /**
     * 
     *
     * @var Schema[]|Reference[]|null
     */
    protected $oneOf;
    /**
     * 
     *
     * @var Schema[]|Reference[]|null
     */
    protected $anyOf;
    /**
     * 
     *
     * @var Schema|Reference|null
     */
    protected $items;
    /**
     * 
     *
     * @var Schema[]|Reference[]|null
     */
    protected $properties;
    /**
     * 
     *
     * @var Schema|Reference|bool|null
     */
    protected $additionalProperties = \true;
    /**
     * 
     *
     * @var string|null
     */
    protected $description;
    /**
     * 
     *
     * @var string|null
     */
    protected $format;
    /**
     * 
     *
     * @var mixed|null
     */
    protected $default;
    /**
     * 
     *
     * @var bool|null
     */
    protected $nullable = \false;
    /**
     * 
     *
     * @var Discriminator|null
     */
    protected $discriminator;
    /**
     * 
     *
     * @var bool|null
     */
    protected $readOnly = \false;
    /**
     * 
     *
     * @var bool|null
     */
    protected $writeOnly = \false;
    /**
     * 
     *
     * @var mixed|null
     */
    protected $example;
    /**
     * 
     *
     * @var ExternalDocumentation|null
     */
    protected $externalDocs;
    /**
     * 
     *
     * @var bool|null
     */
    protected $deprecated = \false;
    /**
     * 
     *
     * @var XML|null
     */
    protected $xml;
    /**
     * 
     *
     * @return string|null
     */
    public function getTitle() : ?string
    {
        return $this->title;
    }
    /**
     * 
     *
     * @param string|null $title
     *
     * @return self
     */
    public function setTitle(?string $title) : self
    {
        $this->title = $title;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getMultipleOf() : ?float
    {
        return $this->multipleOf;
    }
    /**
     * 
     *
     * @param float|null $multipleOf
     *
     * @return self
     */
    public function setMultipleOf(?float $multipleOf) : self
    {
        $this->multipleOf = $multipleOf;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getMaximum() : ?float
    {
        return $this->maximum;
    }
    /**
     * 
     *
     * @param float|null $maximum
     *
     * @return self
     */
    public function setMaximum(?float $maximum) : self
    {
        $this->maximum = $maximum;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getExclusiveMaximum() : ?bool
    {
        return $this->exclusiveMaximum;
    }
    /**
     * 
     *
     * @param bool|null $exclusiveMaximum
     *
     * @return self
     */
    public function setExclusiveMaximum(?bool $exclusiveMaximum) : self
    {
        $this->exclusiveMaximum = $exclusiveMaximum;
        return $this;
    }
    /**
     * 
     *
     * @return float|null
     */
    public function getMinimum() : ?float
    {
        return $this->minimum;
    }
    /**
     * 
     *
     * @param float|null $minimum
     *
     * @return self
     */
    public function setMinimum(?float $minimum) : self
    {
        $this->minimum = $minimum;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getExclusiveMinimum() : ?bool
    {
        return $this->exclusiveMinimum;
    }
    /**
     * 
     *
     * @param bool|null $exclusiveMinimum
     *
     * @return self
     */
    public function setExclusiveMinimum(?bool $exclusiveMinimum) : self
    {
        $this->exclusiveMinimum = $exclusiveMinimum;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getMaxLength() : ?int
    {
        return $this->maxLength;
    }
    /**
     * 
     *
     * @param int|null $maxLength
     *
     * @return self
     */
    public function setMaxLength(?int $maxLength) : self
    {
        $this->maxLength = $maxLength;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getMinLength() : ?int
    {
        return $this->minLength;
    }
    /**
     * 
     *
     * @param int|null $minLength
     *
     * @return self
     */
    public function setMinLength(?int $minLength) : self
    {
        $this->minLength = $minLength;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getPattern() : ?string
    {
        return $this->pattern;
    }
    /**
     * 
     *
     * @param string|null $pattern
     *
     * @return self
     */
    public function setPattern(?string $pattern) : self
    {
        $this->pattern = $pattern;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getMaxItems() : ?int
    {
        return $this->maxItems;
    }
    /**
     * 
     *
     * @param int|null $maxItems
     *
     * @return self
     */
    public function setMaxItems(?int $maxItems) : self
    {
        $this->maxItems = $maxItems;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getMinItems() : ?int
    {
        return $this->minItems;
    }
    /**
     * 
     *
     * @param int|null $minItems
     *
     * @return self
     */
    public function setMinItems(?int $minItems) : self
    {
        $this->minItems = $minItems;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getUniqueItems() : ?bool
    {
        return $this->uniqueItems;
    }
    /**
     * 
     *
     * @param bool|null $uniqueItems
     *
     * @return self
     */
    public function setUniqueItems(?bool $uniqueItems) : self
    {
        $this->uniqueItems = $uniqueItems;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getMaxProperties() : ?int
    {
        return $this->maxProperties;
    }
    /**
     * 
     *
     * @param int|null $maxProperties
     *
     * @return self
     */
    public function setMaxProperties(?int $maxProperties) : self
    {
        $this->maxProperties = $maxProperties;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getMinProperties() : ?int
    {
        return $this->minProperties;
    }
    /**
     * 
     *
     * @param int|null $minProperties
     *
     * @return self
     */
    public function setMinProperties(?int $minProperties) : self
    {
        $this->minProperties = $minProperties;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getRequired() : ?array
    {
        return $this->required;
    }
    /**
     * 
     *
     * @param string[]|null $required
     *
     * @return self
     */
    public function setRequired(?array $required) : self
    {
        $this->required = $required;
        return $this;
    }
    /**
     * 
     *
     * @return mixed[]|null
     */
    public function getEnum() : ?array
    {
        return $this->enum;
    }
    /**
     * 
     *
     * @param mixed[]|null $enum
     *
     * @return self
     */
    public function setEnum(?array $enum) : self
    {
        $this->enum = $enum;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getType() : ?string
    {
        return $this->type;
    }
    /**
     * 
     *
     * @param string|null $type
     *
     * @return self
     */
    public function setType(?string $type) : self
    {
        $this->type = $type;
        return $this;
    }
    /**
     * 
     *
     * @return Schema|Reference|null
     */
    public function getNot()
    {
        return $this->not;
    }
    /**
     * 
     *
     * @param Schema|Reference|null $not
     *
     * @return self
     */
    public function setNot($not) : self
    {
        $this->not = $not;
        return $this;
    }
    /**
     * 
     *
     * @return Schema[]|Reference[]|null
     */
    public function getAllOf() : ?array
    {
        return $this->allOf;
    }
    /**
     * 
     *
     * @param Schema[]|Reference[]|null $allOf
     *
     * @return self
     */
    public function setAllOf(?array $allOf) : self
    {
        $this->allOf = $allOf;
        return $this;
    }
    /**
     * 
     *
     * @return Schema[]|Reference[]|null
     */
    public function getOneOf() : ?array
    {
        return $this->oneOf;
    }
    /**
     * 
     *
     * @param Schema[]|Reference[]|null $oneOf
     *
     * @return self
     */
    public function setOneOf(?array $oneOf) : self
    {
        $this->oneOf = $oneOf;
        return $this;
    }
    /**
     * 
     *
     * @return Schema[]|Reference[]|null
     */
    public function getAnyOf() : ?array
    {
        return $this->anyOf;
    }
    /**
     * 
     *
     * @param Schema[]|Reference[]|null $anyOf
     *
     * @return self
     */
    public function setAnyOf(?array $anyOf) : self
    {
        $this->anyOf = $anyOf;
        return $this;
    }
    /**
     * 
     *
     * @return Schema|Reference|null
     */
    public function getItems()
    {
        return $this->items;
    }
    /**
     * 
     *
     * @param Schema|Reference|null $items
     *
     * @return self
     */
    public function setItems($items) : self
    {
        $this->items = $items;
        return $this;
    }
    /**
     * 
     *
     * @return Schema[]|Reference[]|null
     */
    public function getProperties() : ?iterable
    {
        return $this->properties;
    }
    /**
     * 
     *
     * @param Schema[]|Reference[]|null $properties
     *
     * @return self
     */
    public function setProperties(?iterable $properties) : self
    {
        $this->properties = $properties;
        return $this;
    }
    /**
     * 
     *
     * @return Schema|Reference|bool|null
     */
    public function getAdditionalProperties()
    {
        return $this->additionalProperties;
    }
    /**
     * 
     *
     * @param Schema|Reference|bool|null $additionalProperties
     *
     * @return self
     */
    public function setAdditionalProperties($additionalProperties) : self
    {
        $this->additionalProperties = $additionalProperties;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }
    /**
     * 
     *
     * @param string|null $description
     *
     * @return self
     */
    public function setDescription(?string $description) : self
    {
        $this->description = $description;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getFormat() : ?string
    {
        return $this->format;
    }
    /**
     * 
     *
     * @param string|null $format
     *
     * @return self
     */
    public function setFormat(?string $format) : self
    {
        $this->format = $format;
        return $this;
    }
    /**
     * 
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }
    /**
     * 
     *
     * @param mixed $default
     *
     * @return self
     */
    public function setDefault($default) : self
    {
        $this->default = $default;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getNullable() : ?bool
    {
        return $this->nullable;
    }
    /**
     * 
     *
     * @param bool|null $nullable
     *
     * @return self
     */
    public function setNullable(?bool $nullable) : self
    {
        $this->nullable = $nullable;
        return $this;
    }
    /**
     * 
     *
     * @return Discriminator|null
     */
    public function getDiscriminator() : ?Discriminator
    {
        return $this->discriminator;
    }
    /**
     * 
     *
     * @param Discriminator|null $discriminator
     *
     * @return self
     */
    public function setDiscriminator(?Discriminator $discriminator) : self
    {
        $this->discriminator = $discriminator;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getReadOnly() : ?bool
    {
        return $this->readOnly;
    }
    /**
     * 
     *
     * @param bool|null $readOnly
     *
     * @return self
     */
    public function setReadOnly(?bool $readOnly) : self
    {
        $this->readOnly = $readOnly;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getWriteOnly() : ?bool
    {
        return $this->writeOnly;
    }
    /**
     * 
     *
     * @param bool|null $writeOnly
     *
     * @return self
     */
    public function setWriteOnly(?bool $writeOnly) : self
    {
        $this->writeOnly = $writeOnly;
        return $this;
    }
    /**
     * 
     *
     * @return mixed
     */
    public function getExample()
    {
        return $this->example;
    }
    /**
     * 
     *
     * @param mixed $example
     *
     * @return self
     */
    public function setExample($example) : self
    {
        $this->example = $example;
        return $this;
    }
    /**
     * 
     *
     * @return ExternalDocumentation|null
     */
    public function getExternalDocs() : ?ExternalDocumentation
    {
        return $this->externalDocs;
    }
    /**
     * 
     *
     * @param ExternalDocumentation|null $externalDocs
     *
     * @return self
     */
    public function setExternalDocs(?ExternalDocumentation $externalDocs) : self
    {
        $this->externalDocs = $externalDocs;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getDeprecated() : ?bool
    {
        return $this->deprecated;
    }
    /**
     * 
     *
     * @param bool|null $deprecated
     *
     * @return self
     */
    public function setDeprecated(?bool $deprecated) : self
    {
        $this->deprecated = $deprecated;
        return $this;
    }
    /**
     * 
     *
     * @return XML|null
     */
    public function getXml() : ?XML
    {
        return $this->xml;
    }
    /**
     * 
     *
     * @param XML|null $xml
     *
     * @return self
     */
    public function setXml(?XML $xml) : self
    {
        $this->xml = $xml;
        return $this;
    }
}
