<?php

namespace john\frame\Logger;

/**
 * Class Logger
 * @package john\frame\Logger
 */
class Logger
{
    /**
     * path to log file
     * @var
     */
    public static $PATH;
    /**
     * logger
     * @var null
     */
    protected static $logger = null;
    /**
     * logger name
     * logger file
     * logger file stream
     * @var
     */
    private $name;
    private $file;
    private $fp;

    /**
     * Logger constructor.
     * @param string $name
     * @param null|string $file
     */
    public function __construct(string $name, string $file = null)
    {
        $this->name = $name;
        $this->file = $file;
        $this->open();
    }

    /**
     * Initializing the file stream function
     */
    public function open()
    {
        if (self::$PATH == null) {
            return;
        }
        $this->fp = fopen($this->file == null ? self::$PATH . '/' . $this->name . '.log' : self::$PATH . '/' . $this->file, 'a+');
    }

    /**
     * return logger function
     * @param string $name
     * @param null|string $file
     * @return Logger|mixed
     */
    public static function getLogger(string $name = 'root', string $file = null): self
    {
        if (!isset(self::$logger[$name])) {
            self::$logger[$name] = new Logger($name, $file);
        }
        return self::$logger[$name];
    }

    /**
     * write message in logger
     * @param $message
     */
    public function log(string $message)
    {
        $log = '[' . date('D M d H:i:s Y', time()) . '] ';
        $log .= $message;
        $log .= "\n";
        fwrite($this->fp, $log);
    }

    /**
     * destruct file stream
     */
    public function __destruct()
    {
        fclose($this->fp);
    }
}