<?php
/**
 * SBM Service class for Twitter
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
 * @version    SVN: $Id: Twitter.php 389588 2011-05-27 01:21:53Z rewish $
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 * @since      Version 0.2.1
 */

require_once 'Services/SBM/Info/Abstract.php';

/**
 * SBM Service class for Twitter
 *
 * @category   Services
 * @package    Services_SBM_Info
 * @version    SVN: $Id: Twitter.php 389588 2011-05-27 01:21:53Z rewish $
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 * @since      Version 0.2.1
 */
class Services_SBM_Info_Twitter extends Services_SBM_Info_Abstract
{
    /**
     * API URL
     */
    const API_URL = 'http://otter.topsy.com/trackbacks.json?url=%s';

    /**
     * Entry URL
     */
    const ENTRY_URL = 'http://topsy.com/trackback?url=%s';

    /**
     * Add URL
     */
    const ADD_URL = 'http://twitter.com/?status=%s %s';

    /**
     * Extract count from the API data
     *
     * @param  object $data API data
     * @return integer
     */
    protected function extractCount($data)
    {
        if (isset($data->response->trackback_total)) {
            return (int)$data->response->trackback_total;
        }
        return $this->_count;
    }

    /**
     * Extract comments from the API data
     *
     * @param  object $data API data
     * @return array
     */
    protected function extractComments($data)
    {
        $comments = array();
        if (empty($data->response->list)) {
            return $comments;
        }
        foreach ($data->response->list as $c) {
            $comments[] = array(
                'user'    => (string) $c->author->nick,
                'tags'    => array(),
                'comment' => (string) $c->content,
                'time'    => (int)    $c->date,
                'url'     => (string) $c->permalink_url
            );
        }
        return $comments;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->_count > 1 ? 'tweets' : 'tweet';
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return 1;
    }

    /**
     * Get add page URL
     *
     * @return string
     */
    public function getAddUrl()
    {
        return sprintf(self::ADD_URL, $this->_title, $this->_url);
    }
}
