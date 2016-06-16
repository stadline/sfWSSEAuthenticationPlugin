<?php


class WsseHeaderGenerator
{
    /**
     * @param string $username
     * @param string $secret
     * @return string
     */
    public static function generateHeader($username, $secret){
        $nonce = sha1(openssl_random_pseudo_bytes(128));
        $created = date_create('NOW')->format(DateTime::ISO8601);


        $provider = new WsseProvider(null);
        $digest = $provider->generateDigest($nonce, $created, $secret);

        return sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"' . "\n",
            $username,
            $digest,
            $nonce,
            $created
        );
    }
}