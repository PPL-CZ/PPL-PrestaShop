<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class MediaType extends \ArrayObject
{
    /**
     * 
     *
     * @var Schema|Reference|null
     */
    protected $schema;
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
     * @var Encoding[]|null
     */
    protected $encoding;
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
    /**
     * 
     *
     * @return Encoding[]|null
     */
    public function getEncoding() : ?iterable
    {
        return $this->encoding;
    }
    /**
     * 
     *
     * @param Encoding[]|null $encoding
     *
     * @return self
     */
    public function setEncoding(?iterable $encoding) : self
    {
        $this->encoding = $encoding;
        return $this;
    }
}
