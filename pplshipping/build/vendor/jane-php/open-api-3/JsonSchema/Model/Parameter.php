<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Parameter extends \ArrayObject
{
    /**
     * 
     *
     * @var string|null
     */
    protected $name;
    /**
     * 
     *
     * @var string|null
     */
    protected $in;
    /**
     * 
     *
     * @var string|null
     */
    protected $description;
    /**
     * 
     *
     * @var bool|null
     */
    protected $required = \false;
    /**
     * 
     *
     * @var bool|null
     */
    protected $deprecated = \false;
    /**
     * 
     *
     * @var bool|null
     */
    protected $allowEmptyValue = \false;
    /**
     * 
     *
     * @var string|null
     */
    protected $style;
    /**
     * 
     *
     * @var bool|null
     */
    protected $explode;
    /**
     * 
     *
     * @var bool|null
     */
    protected $allowReserved = \false;
    /**
     * 
     *
     * @var Schema|Reference|null
     */
    protected $schema;
    /**
     * 
     *
     * @var MediaType[]|null
     */
    protected $content;
    /**
     * 
     *
     * @var mixed|null
     */
    protected $example;
    /**
     * 
     *
     * @var Example[]|Reference[]|null
     */
    protected $examples;
    /**
     * 
     *
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * 
     *
     * @param string|null $name
     *
     * @return self
     */
    public function setName(?string $name) : self
    {
        $this->name = $name;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getIn() : ?string
    {
        return $this->in;
    }
    /**
     * 
     *
     * @param string|null $in
     *
     * @return self
     */
    public function setIn(?string $in) : self
    {
        $this->in = $in;
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
     * @return bool|null
     */
    public function getRequired() : ?bool
    {
        return $this->required;
    }
    /**
     * 
     *
     * @param bool|null $required
     *
     * @return self
     */
    public function setRequired(?bool $required) : self
    {
        $this->required = $required;
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
     * @return bool|null
     */
    public function getAllowEmptyValue() : ?bool
    {
        return $this->allowEmptyValue;
    }
    /**
     * 
     *
     * @param bool|null $allowEmptyValue
     *
     * @return self
     */
    public function setAllowEmptyValue(?bool $allowEmptyValue) : self
    {
        $this->allowEmptyValue = $allowEmptyValue;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getStyle() : ?string
    {
        return $this->style;
    }
    /**
     * 
     *
     * @param string|null $style
     *
     * @return self
     */
    public function setStyle(?string $style) : self
    {
        $this->style = $style;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getExplode() : ?bool
    {
        return $this->explode;
    }
    /**
     * 
     *
     * @param bool|null $explode
     *
     * @return self
     */
    public function setExplode(?bool $explode) : self
    {
        $this->explode = $explode;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getAllowReserved() : ?bool
    {
        return $this->allowReserved;
    }
    /**
     * 
     *
     * @param bool|null $allowReserved
     *
     * @return self
     */
    public function setAllowReserved(?bool $allowReserved) : self
    {
        $this->allowReserved = $allowReserved;
        return $this;
    }
    /**
     * 
     *
     * @return Schema|Reference|null
     */
    public function getSchema()
    {
        return $this->schema;
    }
    /**
     * 
     *
     * @param Schema|Reference|null $schema
     *
     * @return self
     */
    public function setSchema($schema) : self
    {
        $this->schema = $schema;
        return $this;
    }
    /**
     * 
     *
     * @return MediaType[]|null
     */
    public function getContent() : ?iterable
    {
        return $this->content;
    }
    /**
     * 
     *
     * @param MediaType[]|null $content
     *
     * @return self
     */
    public function setContent(?iterable $content) : self
    {
        $this->content = $content;
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
     * @return Example[]|Reference[]|null
     */
    public function getExamples() : ?iterable
    {
        return $this->examples;
    }
    /**
     * 
     *
     * @param Example[]|Reference[]|null $examples
     *
     * @return self
     */
    public function setExamples(?iterable $examples) : self
    {
        $this->examples = $examples;
        return $this;
    }
}
