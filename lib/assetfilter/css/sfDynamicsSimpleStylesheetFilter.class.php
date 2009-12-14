<?php

/**
 * sfDynamicsSimpleStylesheetFilter
 *
 * Uses a simple regex to filter stylesheet content.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license MIT
 */

class sfDynamicsSimpleStylesheetFilter extends sfDynamicsBaseAssetFilter
{
  protected function doFilter($code)
  {
    return preg_replace('/\s\s+/m', ' ', str_replace(array("\n", "\t"), ' ', $code));
  }
}
