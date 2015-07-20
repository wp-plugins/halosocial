<?php
/*
 * Plugin Name: HaloSocial
 * Plugin URL: https://halo.social
 * Description: Social Networking Plugin for WordPress
 * Author: HaloSocial
 * Author URL: https://halo.social
 * Version: 1.0
 * Copyright: (c) 2015 HaloSocial, Inc. All Rights Reserved.
 * License: GPLv3 or later
 * License URL: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: halosocial
 * Domain Path: /language
 *
 * HaloSocial is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * HaloSocial is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY. See the
 * GNU General Public License for more details.
 */

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
/**
 * Stores to any stream resource
 *
 * Can be used to store into php://stderr, remote and local files, etc.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */

class HALOStreamHandler extends AbstractProcessingHandler
{
    protected $stream;
    protected $url;
    private $errorMessage;

    protected $lastRecord;
    /**
     * 
     * @param string  $stream
     * @param int $level  The minimum logging level at which this handler will be triggered
     * @param Bool $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($stream, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        if (is_resource($stream)) {
            $this->stream = $stream;
        } else {
            $this->url = $stream;
        }
    }

    /**
     * {@inheritdoc}
     * 
     * @return bool
     */
    public function close()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }

    /**
     *  {@inheritdoc} write file
     * 
     * @param  array  $record [description]
     * @return int
     */
    protected function write(array $record)
    {
        //calculate difference time
        $record['trigtime'] = microtime();
        $diff_time = 0;
        if (isset($this->lastRecord['trigtime'])) {
            $diff_time = $record['trigtime']-$this->lastRecord['trigtime'];
        }
        $this->lastRecord = $record;
        if (null === $this->stream) {
            if (!$this->url) {
                throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
            }
            $this->errorMessage = null;
            set_error_handler(array($this, 'customErrorHandler'));
            $this->stream = fopen($this->url, 'a');
            restore_error_handler();
            if (!is_resource($this->stream)) {
                $this->stream = null;
                throw new \UnexpectedValueException(sprintf('The stream or file "%s" could not be opened: ' . $this->errorMessage, $this->url));
            }
        }
        fwrite($this->stream, ((string) $record['formatted']) . ' lapsed:' . $diff_time . '( ' . $record['trigtime'] . ' ) ');
    }
    /**
     * custom Error Handler
     * 
     * @param  mixed $code
     * @param  string $msg
     * @return string
     */
    private function customErrorHandler($code, $msg)
    {
        $this->errorMessage = preg_replace('{^fopen\(.*?\): }', '', $msg);
    }
}
