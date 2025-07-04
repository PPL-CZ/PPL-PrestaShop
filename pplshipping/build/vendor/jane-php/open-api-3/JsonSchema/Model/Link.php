<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Link extends \ArrayObject
{
    /**
     * 
     *
     * @var string|null
     */
    protected $operationId;
    /**
     * 
     *
     * @var string|null
     */
    protected $operationRef;
    /**
     * 
     *
     * @var mixed[]|null
     */
    protected $parameters;
    /**
     * 
     *
     * @var mixed|null
     */
    protected $requestBody;
    /**
     * 
     *
     * @var string|null
     */
    protected $description;
    /**
     * 
     *
     * @var Server|null
     */
    protected $server;
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
     * @return string|null
     */
    public function getOperationRef() : ?string
    {
        return $this->operationRef;
    }
    /**
     * 
     *
     * @param string|null $operationRef
     *
     * @return self
     */
    public function setOperationRef(?string $operationRef) : self
    {
        $this->operationRef = $operationRef;
        return $this;
    }
    /**
     * 
     *
     * @return mixed[]|null
     */
    public function getParameters() : ?iterable
    {
        return $this->parameters;
    }
    /**
     * 
     *
     * @param mixed[]|null $parameters
     *
     * @return self
     */
    public function setParameters(?iterable $parameters) : self
    {
        $this->parameters = $parameters;
        return $this;
    }
    /**
     * 
     *
     * @return mixed
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }
    /**
     * 
     *
     * @param mixed $requestBody
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
     * @return Server|null
     */
    public function getServer() : ?Server
    {
        return $this->server;
    }
    /**
     * 
     *
     * @param Server|null $server
     *
     * @return self
     */
    public function setServer(?Server $server) : self
    {
        $this->server = $server;
        return $this;
    }
}
