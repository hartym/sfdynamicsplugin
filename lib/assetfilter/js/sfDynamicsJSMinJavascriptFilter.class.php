<?php

/**
 * sfDynamicsJSMinJavascriptFilter
 *
 * Minifies javascript with vendor library JSMin.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license MIT
 */

class sfDynamicsJSMinJavascriptFilter extends sfDynamicsBaseAssetFilter
{
  protected function doFilter($code)
  {
    return JSMin::minify($code);
  }
}

