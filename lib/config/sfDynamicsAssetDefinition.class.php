<?php

/**
 * sfDynamicsAssetCollectionDefinition
 *
 * @package    sfDynamicsPlugin
 * @subpackage configuration
 * @version    SVN: $Id: $
 * @author     Geoffrey Bachelet <geoffrey.bachelet@gmail.com>
 * @license    MIT License
 */
abstract class sfDynamicsAssetDefinition extends sfDynamicsBaseDefinition
{
  protected
    $resource,
    $options = array(),
    $path;

  abstract public function getExtension();

  public function getFilteredContent(sfDynamicsAssetCollectionDefinition $package)
  {
    if (sfConfig::get('sf_debug'))
    {
      $code = sprintf("/* \n * sfDynamicsPlugin include: %s\n */", $this->path)."\n\n";
    }

    $code .= file_get_contents($this->path);

    return $code;
  }

  public function parseXml($xml)
  {
    $this->resource = (string) $xml;

    if (!$this->resource)
    {
      throw new sfDynamicsConfigurationException();
    }

    if (isset($xml['image_path_prefix']))
    {
      $this->options['image_path_prefix'] = (string) $xml['image_path_prefix'];
    }

    $this->options['type'] = self::getElementName($xml);
  }

  public function getPath()
  {
    if (is_null($this->path))
    {
      throw new Exception('You have to call "computePath" first');
    }

    return $this->path;
  }

  public function computePath(array $paths)
  {
    if (is_null($this->path))
    {
      foreach ($paths as $path)
      {
        $file = $path.'/'.$this->resource.'.'.$this->getExtension();

        if (file_exists($file) && is_readable($file) && is_file($file))
        {
          $this->path = $file;
          break;
        }

        $attempts[] = $file;
        $file = null;
      }

      if (is_null($file))
      {
        throw new sfDynamicsUnreadableAssetException(sprintf('Unreadable asset file for package «%s».%sAttempts in order: %s%s', $packageName, "\n\n", "\n - ", implode("\n - ", $attempts)));
      }
    }
  }

  public function __toString()
  {
    return $this->resource;
  }

  /**
   * @todo in php 5.3 use static keyword instead of self
   */
  static public function __set_state($state)
  {
    return self::build(new self(), array('resource', 'options', 'path'), $state);
  }
}
