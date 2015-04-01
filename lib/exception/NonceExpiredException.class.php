<?php

/**
 * NonceExpiredException is thrown when an authentication is rejected because
 * the digest nonce has expired.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Alexander <iam.asm89@gmail.com>
 */
class NonceExpiredException extends AuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Digest nonce has expired.';
    }
}
