<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Operation extends \ArrayObject
{
    /**
     * 
     *
     * @var string[]|null
     */
    protected $tags;
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
     * @var ExternalDocumentation|null
     */
    protected $externalDocs;
    /**
     * 
     *
     * @var string|null
     */
    protected $operationId;
    /**
     * 
     *
     * @var Parameter[]|Reference[]|null
     */
    protected $parameters;
    /**
     * 
     *
     * @var RequestBody|Reference|null
     */
    protected $requestBody;
    /**
     * 
     *
     * @var Responses|null
     */
    protected $responses;
    /**
     * 
     *
     * @var mixed[][]|Reference[]|null
     */
    protected $callbacks;
    /**
     * 
     *
     * @var bool|null
     */
    protected $deprecated = \false;
    /**
     * 
     *
     * @var string[][][]|null
     */
    protected $security;
    /**
     * 
     *
     * @var Server[]|null
     */
    protected $servers;
    /**
     * 
     *
     * @return string[]|null
     */
    public function getTags() : ?array
    {
        return $this->tags;
    }
    /**
     * 
     *
     * @param string[]|null $tags
     *
     * @return self
     */
    public function setTags(?array $tags) : self
    {
        $this->tags = $tags;
        return $this;
    }
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
     * @return string|null
     */
    public function getOperationId() : ?string
    {
        return $this->operationId;
    }
    /**
     * 
     *
     * @param string|null $operationId
     *
     * @return self
     */
    public function setOperationId(?string $operationId) : self
    {
        $this->operationId = $operationId;
        return $this;
    }
    /**
     * 
     *
     * @return Parameter[]|Reference[]|null
     */
    public function getParameters() : ?array
    {
        return $this->parameters;
    }
    /**
     * 
     *
     * @param Parameter[]|Reference[]|null $parameters
     *
     * @return self
     */
    public function setParameters(?array $parameters) : self
    {
        $this->parameters = $parameters;
        return $this;
    }
    /**
     * 
     *
     * @return RequestBody|Reference|null
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }
    /**
     * 
     *
     * @param RequestBody|Reference|null $requestBody
     *
     * @return self
     */
    public function setRequestBody($requestBody) : self
    {
        $this->requestBody = $requestBody;
        return $this;
    }
    /**
     * 
     *
     * @return Responses|null
     */
    public function getResponses() : ?Responses
    {
        return $this->responses;
    }
    /**
     * 
     *
     * @param Responses|null $responses
     *
     * @return self
     */
    public function setResponses(?Responses $responses) : self
    {
        $this->responses = $responses;
        return $this;
    }
    /**
     * 
     *
     * @return mixed[][]|Reference[]|null
     */
    public function getCallbacks() : ?iterable
    {
        return $this->callbacks;
    }
    /**
     * 
     *
     * @param mixed[][]|Reference[]|null $callbacks
     *
     * @return self
     */
    public function setCallbacks(?iterable $callbacks) : self
    {
        $this->callbacks = $callbacks;
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
     * @return string[][][]|null
     */
    public function getSecurity() : ?array
    {
        return $this->security;
    }
    /**
     * 
     *
     * @param string[][][]|null $security
     *
     * @return self
     */
    public function setSecurity(?array $security) : self
    {
        $this->security = $security;
        return $this;
    }
    /**
     * 
     *
     * @return Server[]|null
     */
    public function getServers() : ?array
    {
        return $this->servers;
    }
    /**
     * 
     *
     * @param Server[]|null $servers
     *
     * @return self
     */
    public function setServers(?array $servers) : self
    {
        $this->servers = $servers;
        return $this;
    }
}
