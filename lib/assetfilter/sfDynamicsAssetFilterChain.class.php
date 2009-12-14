<?php

/**
 * sfDynamicsAssetFilterChain
 *
 * Chain implementation of asset filters.
 *
 * @package sfDynamicsPlugin
 * @version SVN: $Id: $
 * @author  Romain Dorgueil <romain.dorgueil@symfony-project.com>
 * @license MIT
 */

class sfDynamicsAssetFilterChain extends sfDynamicsBaseAssetFilter
{
  /**
   * Filter chain
   *
   * @var array
   */
  protected $chain = array();

  /**
   * Instances of filter classes, to avoid instancing multiple times stateless
   * classes.
   */
  static protected $filterInstanceCache = array();

  /**
   * Appends a filter to the chain.
   *
   * @param  sfDynamicsBaseAssetFilter $filter
   * @return void
   */
  public function add(sfDynamicsBaseAssetFilter $filter)
  {
    $this->chain[] = $filter;
  }

  /**
   * Appends a filter to the chain, given its classname.
   *
   * @param  string $filterClassName
   * @return void
   */
  public function addByName($filterClassName)
  {
    if (!isset(self::$filterInstanceCache[$filterClassName]))
    {
      self::$filterInstanceCache[$filterClassName] = new $filterClassName();
    }

    $this->add(self::$filterInstanceCache[$filterClassName]);
  }

  protected function doFilter($code)
  {
    foreach ($this->chain as $filter)
    {
      $code = $filter->filter($code);
    }

    return $code;
  }
}

