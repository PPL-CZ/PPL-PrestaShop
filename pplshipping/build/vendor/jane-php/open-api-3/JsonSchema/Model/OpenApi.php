<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class OpenApi extends \ArrayObject
{
    /**
     * 
     *
     * @var string|null
     */
    protected $openapi;
    /**
     * 
     *
     * @var Info|null
     */
    protected $info;
    /**
     * 
     *
     * @var ExternalDocumentation|null
     */
    protected $externalDocs;
    /**
     * 
     *
     * @var Server[]|null
     */
    protected $servers;
    /**
     * 
     *
     * @var string[][][]|null
     */
    protected $security;
    /**
     * 
     *
     * @var Tag[]|null
     */
    protected $tags;
    /**
     * 
     *
     * @var PathItem[]|mixed[]|null
     */
    protected $paths;
    /**
     * 
     *
     * @var Components|null
     */
    protected $components;
    /**
     * 
     *
     * @return string|null
     */
    public function getOpenapi() : ?string
    {
        return $this->openapi;
    }
    /**
     * 
     *
     * @param string|null $openapi
     *
     * @return self
     */
    public function setOpenapi(?string $openapi) : self
    {
        $this->openapi = $openapi;
        return $this;
    }
    /**
     * 
     *
     * @return Info|null
     */
    public function getInfo() : ?Info
    {
        return $this->info;
    }
    /**
     * 
     *
     * @param Info|null $info
     *
     * @return self
     */
    public function setInfo(?Info $info) : self
    {
        $this->info = $info;
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
     * @return Tag[]|null
     */
    public function getTags() : ?array
    {
        return $this->tags;
    }
    /**
     * 
     *
     * @param Tag[]|null $tags
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
     * @return PathItem[]|mixed[]
     */
    public function getPaths()
    {
        return $this->paths;
    }
    /**
     * 
     *
     * @param PathItem[]|mixed[] $paths
     *
     * @return self
     */
    public function setPaths($paths) : self
    {
        $this->paths = $paths;
        return $this;
    }
    /**
     * 
     *
     * @return Components|null
     */
    public function getComponents() : ?Components
    {
        return $this->components;
    }
    /**
     * 
     *
     * @param Components|null $components
     *
     * @return self
     */
    public function setComponents(?Components $components) : self
    {
        $this->components = $components;
        return $this;
    }
}
