<?php
/**
 * @package     Bluecoder.JFilters
 *
 * @copyright   Copyright © 2021-2026 Blue-Coder.com. All rights reserved.
 * @license     GNU General Public License 2 or later, see COPYING.txt for license details.
 */

namespace Bluecoder\Component\Jfilters\Administrator;

\defined('_JEXEC') or die();


/**
 * Class BaseObject
 *
 * A base object used in our application.
 *
 * @package Bluecoder\Component\Jfilters\Administrator
 */
class BaseObject extends \stdClass
{
    /**
     * @var ObjectManager
     * @since 1.0.0
     */
    protected $objectManager;

    /**
     * Stores raw the data of the objects
     *
     * @var \stdClass
     * @since 1.0.0
     */
    protected $data;

    /**
     * BaseObject constructor.
     * @since 1.0.0
     */
    public function __construct($properties = null)
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    /**
     * Set data to a class from a given assoc. array
     *
     * @param $data
     * @return $this
     * @since 1.0.0
     */
    public function setData($data)
    {
        if (is_array($data) || is_object($data))
        {
            $data = (object)$data;
            $this->data =  $data;
            foreach ((array) $data as $k => $v){
                $parts = explode('_',$k);
                $key = '';
                foreach ($parts as $part) {
                    $key.= ucfirst($part);
                }
                $functionName = 'set'.$key;
                if(method_exists($this, $functionName)) {
                    $this->{$functionName}($v);
                }
            }
        }
        return $this;
    }

    /**
     * Get the raw data
     *
     * @return \stdClass
     * @since 1.0.0
     */
    public function getData()
    {
        return $this->data;
    }
}
