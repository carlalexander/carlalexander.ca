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
 * Helthe error handler. Handles both errors and exceptions so that errors can be handled silently.
 *
 * @author Carl Alexander
 */
class Helthe_ErrorHandler
{
    /**
     * @var string
     */
    private $reservedMemory;

    /**
     * Register the error handler.
     *
     * @param integer $level
     *
     * @return Helthe_ErrorHandler
     */
    public static function register($level = null)
    {
        $handler = new self();

        if (null !== $level) {
            error_reporting($level);
        }

        // Disable xdebug stack traces
        if (extension_loaded('xdebug')) {
            xdebug_disable();
        }

        set_error_handler(array($handler, 'handleError'));
        set_exception_handler(array($handler, 'handleException'));
        register_shutdown_function(array($handler, 'handleFatal'));
        $handler->reservedMemory = str_repeat('x', 10240);

        return $handler;
    }

    /**
     * Handles errors.
     *
     * @param integer $level
     * @param string  $message
     * @param string  $file
     * @param integer $line
     * @param array   $context
     *
     * @return boolean
     */
    public function handleError($level, $message, $file = 'unknown', $line = 0, array $context = array())
    {
        do_action('helthe_handle_error', $message);

        $this->handleException(new Helthe_Exception_ContextErrorException($this->buildErrorMessage($level, $message, $file, $line), 0, $level, $file, $line, $context));

        return false;
    }

    /**
     * Handles exceptions.
     *
     * @param Exception $exception
     */
    public function handleException(Exception $exception)
    {
        $level = E_RECOVERABLE_ERROR;

        if ($exception instanceof ErrorException) {
            $level = $exception->getSeverity();
        }
    }

    /**
     * Handles fatal errors.
     */
    public function handleFatal()
    {
        if (null === $error = error_get_last()) {
            return;
        }

        unset($this->reservedMemory);
        $level = $error['type'];

        // Only handle PHP fatal errors
        if (!in_array($level, array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE))) {
            return;
        }

        $this->handleException(new ErrorException($this->buildErrorMessage($level, $error['message'], $error['file'], $error['line']), 0, $level, $error['file'], $error['line']));
    }

    /**
     * Builds the error message.
     *
     * @param integer $level
     * @param string  $message
     * @param string  $file
     * @param integer $line
     *
     * @return string
     */
    private function buildErrorMessage($level, $message, $file, $line)
    {
        $levels = array(
            E_WARNING           => 'Warning',
            E_NOTICE            => 'Notice',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated',
            E_ERROR             => 'Error',
            E_CORE_ERROR        => 'Core Error',
            E_COMPILE_ERROR     => 'Compile Error',
            E_PARSE             => 'Parse',
        );

        return sprintf('%s: %s in %s line %d', isset($levels[$level]) ? $levels[$level] : $level, $message, $file, $line);
    }
}
