<?php

class WsseGenerateCredentialsTask extends sfBaseTask
{
    /**
     * Configure task
     */
    protected function configure()
    {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
            // add your own options here
            new sfCommandOption('username', 'u', sfCommandOption::PARAMETER_REQUIRED, 'The WSSE username (should match a valid sfGuardUser)'),
            new sfCommandOption('password', 'p', sfCommandOption::PARAMETER_REQUIRED, 'The WSSE password (should match a valid sfGuardUser)', null),
        ));

        $this->namespace = 'wsse';
        $this->name = 'generate-credentials';
        $this->briefDescription = 'Generate authentication credentials: username, password digest, nonce, and creation date.';
    }

    /**
     * Execute task
     */
    protected function execute($arguments = array(), $options = array())
    {
        // init database connection
        $databaseManager = new sfDatabaseManager($this->configuration);

        // check username
        $user = Doctrine_Core::getTable('sfGuardUser')->retrieveByUsername($options['username']);

        if (!$user instanceof sfGuardUser) {
            return $this->logSection('sfGuardUser', 'No user found matching username="' . $options['username'] . '"', null, 'ERROR');
        }

        // check password
        if (null === $options['password']) {
            $options['password'] = $user->getPassword();
        }

        if ($user->getPassword() != $options['password']) {
            return $this->logSection('sfGuardUser', 'No user found matching password="' . $options['password'] . '"', null, 'ERROR');
        }

        // generate authentication credentials
        $nonce = sha1(openssl_random_pseudo_bytes(128));
        $created = date_create('NOW')->format(DateTime::ISO8601);
        $secret = $options['password'];

        $provider = new WsseProvider(null);
        $digest = $provider->generateDigest($nonce, $created, $secret);

        echo sprintf(
            'UsernameToken Username="%s", PasswordDigest="%s", Nonce="%s", Created="%s"' . "\n",
            $options['username'],
            $digest,
            $nonce,
            $created
        );
    }
}
