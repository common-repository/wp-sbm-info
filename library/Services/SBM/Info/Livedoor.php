<?php
/**
 * SBM Service class for Livedoor
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
 * @version    SVN: $Id: Livedoor.php 287207 2010-09-09 10:35:17Z rewish $
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 */

require_once 'Services/SBM/Info/Abstract.php';

/**
 * SBM Service class for Livedoor
 *
 * @category   Services
 * @package    Services_SBM_Info
 * @version    SVN: $Id: Livedoor.php 287207 2010-09-09 10:35:17Z rewish $
 * @author     Hiroshi Hoaki <rewish.org@gmail.com>
 * @copyright  2010 Hiroshi Hoaki
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://openpear.org/package/Services_SBM_Info
 */
class Services_SBM_Info_Livedoor extends Services_SBM_Info_Abstract
{
    /**
     * API URL
     */
    const API_URL = 'http://clip.livedoor.com/api/json/comments?link=%s';

    /**
     * Entry URL
     */
    const ENTRY_URL = 'http://clip.livedoor.com/page/%s';

    /**
     * Add URL
     */
    const ADD_URL = 'http://clip.livedoor.com/clip/add?link=%s&amp;title=%s&amp;jump=ref';

    /**
     * Extract count from the API data
     *
     * @param  object $data API data
     * @return integer
     */
    protected function extractCount($data)
    {
        if (isset($data->total_clip_count)) {
            return (int)$data->total_clip_count;
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
        if (empty($data->Comments)) {
            return $comments;
        }
        foreach ($data->Comments as $c) {
            $comments[] = array(
                'user'    => (string) $c->livedoor_id,
                'tags'    => (array)  $c->tags,
                'comment' => (string) $c->notes,
                'time'    => (int)    $c->created_on
            );
        }
        return $comments;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->_count >= 3 ? 2 : 1;
    }
}