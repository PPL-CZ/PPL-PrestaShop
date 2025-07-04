<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class RequestBody extends \ArrayObject
{
    /**
     * 
     *
     * @var string|null
     */
    protected $description;
    /**
     * 
     *
     * @var MediaType[]|null
     */
    protected $content;
    /**
     * 
     *
     * @var bool|null
     */
    protected $required = \false;
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
}
