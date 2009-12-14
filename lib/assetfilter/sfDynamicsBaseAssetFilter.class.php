<?php

/**
 * sfDynamicsBaseAssetFilter
 *
 * Defines base requirements (interface) for an asset filter, that is used in
 * an asset filtering filter chain.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license MIT
 */

abstract class sfDynamicsBaseAssetFilter
{
  /**
   * Wrapper for doFilter method that tries to hide problems if we're not
   * debugging the application.
   *
   * @param  string $code -- unfiltered code
   * @return string       -- filtered code
   */
  public function filter($code)
  {
    try
    {
      return $this->doFilter($code);
    }
    catch (Exception $e)
    {
      if (sfConfig::get('sf_debug'))
      {
        throw $e;
      }
      else
      {
        return $code;
      }
    }
  }

  /**
   * Abstract code filtering method. Must be implemented in child classes.
   *
   * @param  string $code -- unfiltered code
   * @return string       -- filtered code
   */
  abstract protected function doFilter($code);
}
