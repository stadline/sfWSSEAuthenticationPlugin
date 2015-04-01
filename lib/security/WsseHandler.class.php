<?php

class WsseHandler
{
    private $wsseProvider;

    public function __construct()
    {
        $this->wsseProvider = new WsseProvider(sfConfig::get('sf_app_cache_dir'));
    }

    /**
     * Execute handler.
     *
     * @param sfContext $context
     */
    public function execute(sfContext $context)
    {
        // parse WSSE header
        $userToken = $this->wsseProvider->handleRequest($context->getRequest());

        if (!$userToken instanceof WsseUserToken) {
            $context->getUser()->setAuthenticated(false);
            $this->forwardToLoginAction($context->getResponse());
        }

        // check if WSSE is valid
        try {
            $guardUser = $this->authenticate($userToken);
        } catch (AuthenticationException $failed) {
            $context->getLogger()->err($failed->getMessage());
            $context->getUser()->setAuthenticated(false);
            $this->forwardToSecureAction($context->getResponse());
        }

        // authenticate user
        if ($context->getUser() instanceof sfGuardSecurityUser) {
            $context->getUser()->signIn($guardUser);
        } else {
            $context->getUser()->setAuthenticated(true);
        }
    }

    /**
     * Find user matching WSSE token.
     *
     * @param WsseUserToken $token
     * @return sfGuardUser
     */
    protected function authenticate(WsseUserToken $token)
    {
        $user = Doctrine_Core::getTable('sfGuardUser')->retrieveByUsername($token->username);

        if ($user && $this->wsseProvider->validateDigest($token->digest, $token->nonce, $token->created, $user->getPassword())) {
            return $user;
        }

        throw new AuthenticationException('The WSSE authentication failed.');
    }

    /**
     * Forwards the current request to the secure action.
     *
     * @throws sfStopException
     */
    protected function forwardToSecureAction(sfWebResponse $response)
    {
        sfConfig::set('sf_web_debug', false);

        $response->setStatusCode(403);
        $response->send();

        throw new sfStopException();
    }

    /**
     * Forwards the current request to the login action.
     *
     * @throws sfStopException
     */
    protected function forwardToLoginAction(sfWebResponse $response)
    {
        sfConfig::set('sf_web_debug', false);

        $response->setStatusCode(401);
        $response->setHttpHeader('WWW-Authenticate', 'WSSE realm="Secured with WSSE", profile="UsernameToken"');
        $response->send();

        throw new sfStopException();
    }
}
