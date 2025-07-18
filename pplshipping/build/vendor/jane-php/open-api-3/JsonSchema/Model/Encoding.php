<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class Encoding
{
    /**
     * 
     *
     * @var string|null
     */
    protected $contentType;
    /**
     * 
     *
     * @var Header[]|null
     */
    protected $headers;
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
     * @return string|null
     */
    public function getContentType() : ?string
    {
        return $this->contentType;
    }
    /**
     * 
     *
     * @param string|null $contentType
     *
     * @return self
     */
    public function setContentType(?string $contentType) : self
    {
        $this->contentType = $contentType;
        return $this;
    }
    /**
     * 
     *
     * @return Header[]|null
     */
    public function getHeaders() : ?iterable
    {
        return $this->headers;
    }
    /**
     * 
     *
     * @param Header[]|null $headers
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
}
