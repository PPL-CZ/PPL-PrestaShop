<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Response extends \ArrayObject
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
     * @var Header[]|Reference[]|null
     */
    protected $headers;
    /**
     * 
     *
     * @var MediaType[]|null
     */
    protected $content;
    /**
     * 
     *
     * @var Link[]|Reference[]|null
     */
    protected $links;
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
     * @return Header[]|Reference[]|null
     */
    public function getHeaders() : ?iterable
    {
        return $this->headers;
    }
    /**
     * 
     *
     * @param Header[]|Reference[]|null $headers
     *
     * @return self
     */
    public function setHeaders(?iterable $headers) : self
    {
        $this->headers = $headers;
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
     * @return Link[]|Reference[]|null
     */
    public function getLinks() : ?iterable
    {
        return $this->links;
    }
    /**
     * 
     *
     * @param Link[]|Reference[]|null $links
     *
     * @return self
     */
    public function setLinks(?iterable $links) : self
    {
        $this->links = $links;
        return $this;
    }
}
