<?php

class WsseProvider
{
    private $cacheDir;
    private $lifetime;

    public function __construct($cacheDir, $lifetime = 300)
    {
        $this->cacheDir = $cacheDir;
        $this->lifetime = $lifetime;
    }

    public function handleRequest(sfWebRequest $request)
    {
        $headers = $request->getPathInfoArray();

        if (isset($headers['HTTP_X_WSSE'])) {

            $wsseRegex = '/UsernameToken Username="([^"]+)", PasswordDigest="([^"]+)", Nonce="([^"]+)", Created="([^"]+)"/';

            if (preg_match($wsseRegex, $headers['HTTP_X_WSSE'], $matches)) {
                $token = new WsseUserToken();
                $token->username = $matches[1];
                $token->digest = $matches[2];
                $token->nonce = $matches[3];
                $token->created = $matches[4];

                return $token;
            }
        }
    }

    public function validateDigest($digest, $nonce, $created, $secret)
    {
        // Expire timestamp after 5 minutes
        if (time() - strtotime($created) > $this->lifetime) {
            return false;
        }

        // Validate nonce is unique within 5 minutes
		// Nonce can contain '/' that is not allowed in filename
        // Nonce is md5 encrypted to prevent this problem
		$cacheKey = md5($nonce);
        if (file_exists($this->cacheDir . '/' . $cacheKey) && file_get_contents($this->cacheDir . '/' . $cacheKey) + $this->lifetime > time()) {
            throw new NonceExpiredException('Previously used nonce detected');
        }
        file_put_contents($this->cacheDir . '/' . $cacheKey, strtotime($created));

        // Validate Secret
        $expected = $this->generateDigest($nonce, $created, $secret);

        return $digest === $expected;
    }

    public function generateDigest($nonce, $created, $secret)
    {
        return base64_encode(sha1($nonce . $created . $secret, true));
    }
}
