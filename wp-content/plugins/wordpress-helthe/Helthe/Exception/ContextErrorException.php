<?php

/*
 * This file is part of the WordPress Helthe plugin.
 *
 * (c) Helthe
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Error exception with variable context.
 *
 * @author Carl Alexander
 */
Class Helthe_Exception_ContextErrorException extends ErrorException
{
    /**
     * @var array
     */
    private $context = array();

    /**
     * Constructor.
     *
     * @param string  $message
     * @param integer $code
     * @param integer $severity
     * @param string  $filename
     * @param integer $lineno
     * @param array   $context
     */
    public function __construct($message, $code, $severity, $filename, $lineno, array $context = array())
    {
        parent::__construct($message, $code, $severity, $filename, $lineno);

        $this->context = $context;
    }

    /**
     * Get the variable context.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }
}
