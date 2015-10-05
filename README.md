sfWSSEAuthenticationPlugin
==========================

This plugin aims to implement WSSE authentication for Symfony 1. **It uses sfGuardUser** to validate authentication username and password. In other words: you must create a sfGuardUser and use its username and encrypted password to generate WSSE credentials.

----------------------------------------------------------------

Installation
------------

  * Go to the plugins root (usually: ```/plugins```)

  * Install the plugin:

  ```shell
  git clone https://github.com/stadline/sfWSSEAuthenticationPlugin.git sfWSSEAuthenticationPlugin
  ```
        

  * Edit ProjectConfiguration.class.php to activate the plugin:

  ```php
  $this->enablePlugins(array(
      ...
      'sfWSSEAuthenticationPlugin',
  ));
  ```

  * Clear the cache:

  ```shell
  ./symfony cc
  ```

Configuration
------------

To activate the plugin, you need to change the filter responsible of the security.

  * Go to ```/apps/<yourApp>/config``` and edit filters.yml
 
  * Modify the class under security filter:
  ```yaml
  security:
    class: sfWsseSecurityFilter
  ```

  * Additionally, you might turn of session storage to allow stateless operations. To do that you need to change the storage class in factories.yml.

  ```yaml
  all:
    storage:
      class: sfNoStorage
  ```

  * You can also turn off the filter using environment parameters. To do that you need to edit the app.yml file.

  ```yaml
  test:
    security:
      enabled: false
  ```

----------------------------------------------------------------

Helper task
------------

You can use the ```wsse:generate-credentials``` task to generate a valid WSSE UsernameToken and get the user encrypted password
