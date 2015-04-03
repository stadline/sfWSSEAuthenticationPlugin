sfWSSEAuthenticationPlugin
==========================

This plugin aims to implement WSSE authentication for Symfony 1.

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

  * Additionally, you might turn of session storage to allow stateless operation. To do that you need to change the storage class in factories.yml.

  ```yaml
  all:
    storage:
      class: sfNoStorage
  ```

