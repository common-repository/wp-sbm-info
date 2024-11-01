<?php
/**
 * Abstract class for SBM Service class
 *
 * PHP version 5.2
 *
 * Copyright (c) 2010 Hiroshi Hoaki, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Hiroshi Hoaki nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Services
 * @package    Services_SBM_Info
 * @version    SVN: $Id: Abstract.php 288313 2010-09-12 07:31:18Z rewish $
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 */

require_once 'Services/SBM/Info/Exception.php';

/**
 * Abstract class for SBM Service class
 *
 * @category   Services
 * @package    Services_SBM_Info
 * @version    SVN: $Id: Abstract.php 288313 2010-09-12 07:31:18Z rewish $
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 */
abstract class Services_SBM_Info_Abstract
{
    /**
     * API URL
     */
    const API_URL = '';

    /**
     * Entry URL
     */
    const ENTRY_URL = '';

    /**
     * Add URL
     */
    const ADD_URL = '';

    /**
     * Target URL
     * @var string
     */
    protected $_url;

    /**
     * Page title
     * @var string
     */
    protected $_title;

    /**
     * Executed URL
     * @var string
     */
    protected $_executedUrl;

    /**
     * API data
     * @var object
     */
    protected $_apiData;

    /**
     * Fetch function
     * @var sting|array
     */
    protected $_fetchFunction;

    /**
     * Convert object function
     * @var string|array
     */
    protected $_toObjectFunction;

    /**
     * Count
     * @var integer
     */
    protected $_count = 0;

    /**
     * Count extract flag
     * @var boolean
     */
    protected $_countExtracted = false;

    /**
     * Comments
     * @var array
     */
    protected $_comments = array();

    /**
     * Comments extract flag
     * @var boolean
     */
    protected $_commentsExtracted = false;

    /**
     * Proxy host
     * @var string
     */
    protected $_proxyHost;

    /**
     * Proxy port
     * @var integer
     */
    protected $_proxyPort;

    /**
     * Constructor
     *
     * @param  string $url Target URL
     * @param  string $title Page title
     */
    public function __construct($url = null, $title = null)
    {
        $this->setUrl($url)
             ->setTitle($title);
        $this->_fetchFunction = array($this, 'fetch');
        $this->_toObjectFunction = array($this, 'toObject');
    }

    /**
     * Fetch API data and API data to Object
     *
     * @return void
     */
    public function execute()
    {
        if ($this->_executedUrl === $this->_url) return;
        $url = constant(get_class($this) . '::API_URL');
        $this->_apiData = call_user_func($this->_fetchFunction, sprintf($url, $this->_url));
        $this->_apiData = call_user_func($this->_toObjectFunction, $this->_apiData);
        $this->_executedUrl = $this->_url;
        $this->_countExtracted = false;
        $this->_commentsExtracted = false;
    }

    /**
     * Fetch API data
     *
     * @return string $url API URL
     * @todo HTTP_Request2 or cURL
     */
    protected function fetch($url)
    {
        if ($this->_proxyHost && $this->_proxyPort) {
            return file_get_contents($url, false, stream_context_create(array(
                'http' => array(
                    'proxy' => "tcp://$this->_proxyHost:$this->_proxyPort",
                    'request_fulluri' => true,
					'timeout' => 3
                )
            )));
        }
        return file_get_contents($url);
    }

    /**
     * String to Object
     *
     * @param  string $string
     * @return object
     */
    protected function toObject($string)
    {
        return json_decode($string);
    }

    /**
     * Extract count from the API data
     *
     * @param  object API data
     * @return integer
     */
    abstract protected function extractCount($data);

    /**
     * Extract comments from the API data
     *
     * @param  object API data
     * @return array
     */
    abstract protected function extractComments($data);

    /**
     * Set target URL
     *
     * @param  string $url Target URL
     * @return $this Services_SBM_Info_{ServiceName} object
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Set page title
     *
     * @param  string $title Page title
     * @return $this Services_SBM_Info_{ServiceName} object
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Set fetch callback
     *
     * @param  string|array $func User function
     * @return $this Services_SBM_Info_{ServiceName} object
     */
    public function setFetchFunction($func)
    {
        $this->_fetchFunction = $func;
        return $this;
    }

    /**
     * Set convert object callback
     *
     * @param  string|array $func User function
     * @return $this Services_SBM_Info_{ServiceName} object
     */
    public function setToObjectFunction($func)
    {
        $this->_toObjectFunction = $func;
        return $this;
    }

    /**
     * Set proxy
     *
     * @param string  $host Host | IP
     * @param integer $port Port
     * @return $this Services_SBM_Info_{ServiceName} object
     */
    public function setProxy($host, $port)
    {
        $this->_proxyHost = $host;
        $this->_proxyPort = $port;
        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        if (!$this->_countExtracted) {
            $this->_count = $this->extractCount($this->_apiData);
            $this->_countExtracted = true;
        }
        return $this->_count;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->_count > 1 ? 'users' : 'user';
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->_count >= 10 ? 2 : 1;
    }

    /**
     * Get comments
     *
     * @return array
     */
    public function getComments()
    {
        if (!$this->_commentsExtracted) {
            $this->_comments = $this->extractComments($this->_apiData);
            $this->_commentsExtracted = true;
        }
        return $this->_comments;
    }

    /**
     * Get entry URL
     *
     * @return string
     */
    public function getEntryUrl()
    {
        $className = get_class($this);
        return sprintf(constant("$className::ENTRY_URL"), $this->_url);
    }

    /**
     * Get add page URL
     *
     * @return string
     */
    public function getAddUrl()
    {
        $className = get_class($this);
        return sprintf(constant("$className::ADD_URL"), urlencode($this->_url), urlencode($this->_title));
    }
}