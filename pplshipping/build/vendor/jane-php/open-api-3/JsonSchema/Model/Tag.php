<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Tag extends \ArrayObject
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
    protected $description;
    /**
     * 
     *
     * @var ExternalDocumentation|null
     */
    protected $externalDocs;
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
}
