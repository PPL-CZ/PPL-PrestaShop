<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Example extends \ArrayObject
{
    /**
     * 
     *
     * @var string|null
     */
    protected $summary;
    /**
     * 
     *
     * @var string|null
     */
    protected $description;
    /**
     * 
     *
     * @var mixed|null
     */
    protected $value;
    /**
     * 
     *
     * @var string|null
     */
    protected $externalValue;
    /**
     * 
     *
     * @return string|null
     */
    public function getSummary() : ?string
    {
        return $this->summary;
    }
    /**
     * 
     *
     * @param string|null $summary
     *
     * @return self
     */
    public function setSummary(?string $summary) : self
    {
        $this->summary = $summary;
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
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * 
     *
     * @param mixed $value
     *
     * @return self
     */
    public function setValue($value) : self
    {
        $this->value = $value;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getExternalValue() : ?string
    {
        return $this->externalValue;
    }
    /**
     * 
     *
     * @param string|null $externalValue
     *
     * @return self
     */
    public function setExternalValue(?string $externalValue) : self
    {
        $this->externalValue = $externalValue;
        return $this;
    }
}
