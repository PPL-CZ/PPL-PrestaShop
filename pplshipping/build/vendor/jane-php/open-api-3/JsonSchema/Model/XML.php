<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class XML extends \ArrayObject
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
    protected $namespace;
    /**
     * 
     *
     * @var string|null
     */
    protected $prefix;
    /**
     * 
     *
     * @var bool|null
     */
    protected $attribute = \false;
    /**
     * 
     *
     * @var bool|null
     */
    protected $wrapped = \false;
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
    public function getNamespace() : ?string
    {
        return $this->namespace;
    }
    /**
     * 
     *
     * @param string|null $namespace
     *
     * @return self
     */
    public function setNamespace(?string $namespace) : self
    {
        $this->namespace = $namespace;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getPrefix() : ?string
    {
        return $this->prefix;
    }
    /**
     * 
     *
     * @param string|null $prefix
     *
     * @return self
     */
    public function setPrefix(?string $prefix) : self
    {
        $this->prefix = $prefix;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getAttribute() : ?bool
    {
        return $this->attribute;
    }
    /**
     * 
     *
     * @param bool|null $attribute
     *
     * @return self
     */
    public function setAttribute(?bool $attribute) : self
    {
        $this->attribute = $attribute;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getWrapped() : ?bool
    {
        return $this->wrapped;
    }
    /**
     * 
     *
     * @param bool|null $wrapped
     *
     * @return self
     */
    public function setWrapped(?bool $wrapped) : self
    {
        $this->wrapped = $wrapped;
        return $this;
    }
}
