<?php

namespace PPLShipping\Jane\Component\OpenApi3\JsonSchema\Model;

class OAuthFlows extends \ArrayObject
{
    /**
     * 
     *
     * @var ImplicitOAuthFlow|null
     */
    protected $implicit;
    /**
     * 
     *
     * @var PasswordOAuthFlow|null
     */
    protected $password;
    /**
     * 
     *
     * @var ClientCredentialsFlow|null
     */
    protected $clientCredentials;
    /**
     * 
     *
     * @var AuthorizationCodeOAuthFlow|null
     */
    protected $authorizationCode;
    /**
     * 
     *
     * @return ImplicitOAuthFlow|null
     */
    public function getImplicit() : ?ImplicitOAuthFlow
    {
        return $this->implicit;
    }
    /**
     * 
     *
     * @param ImplicitOAuthFlow|null $implicit
     *
     * @return self
     */
    public function setImplicit(?ImplicitOAuthFlow $implicit) : self
    {
        $this->implicit = $implicit;
        return $this;
    }
    /**
     * 
     *
     * @return PasswordOAuthFlow|null
     */
    public function getPassword() : ?PasswordOAuthFlow
    {
        return $this->password;
    }
    /**
     * 
     *
     * @param PasswordOAuthFlow|null $password
     *
     * @return self
     */
    public function setPassword(?PasswordOAuthFlow $password) : self
    {
        $this->password = $password;
        return $this;
    }
    /**
     * 
     *
     * @return ClientCredentialsFlow|null
     */
    public function getClientCredentials() : ?ClientCredentialsFlow
    {
        return $this->clientCredentials;
    }
    /**
     * 
     *
     * @param ClientCredentialsFlow|null $clientCredentials
     *
     * @return self
     */
    public function setClientCredentials(?ClientCredentialsFlow $clientCredentials) : self
    {
        $this->clientCredentials = $clientCredentials;
        return $this;
    }
    /**
     * 
     *
     * @return AuthorizationCodeOAuthFlow|null
     */
    public function getAuthorizationCode() : ?AuthorizationCodeOAuthFlow
    {
        return $this->authorizationCode;
    }
    /**
     * 
     *
     * @param AuthorizationCodeOAuthFlow|null $authorizationCode
     *
     * @return self
     */
    public function setAuthorizationCode(?AuthorizationCodeOAuthFlow $authorizationCode) : self
    {
        $this->authorizationCode = $authorizationCode;
        return $this;
    }
}
