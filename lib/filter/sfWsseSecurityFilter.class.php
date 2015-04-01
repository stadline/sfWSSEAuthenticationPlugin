<?php

/**
 * sfWsseSecurityFilter filter
 * @link       http://www.xml.com/pub/a/2003/12/17/dive.html
 *
 * @package    symfony
 * @subpackage filter
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfBasicSecurityFilter.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfWsseSecurityFilter extends sfFilter
{
    /**
     * Executes this filter.
     *
     * @param sfFilterChain $filterChain A sfFilterChain instance
     */
    public function execute($filterChain)
    {
        if ($this->isFirstCall()) {
            $handler = new WsseHandler();
            $handler->execute($this->context);
        }

        // execute next filter
        $filterChain->execute();
    }
}
