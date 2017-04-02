<?php

namespace John\Frame\Logger;

/**
 * Class Logger
 * @package John\Frame\Logger
 */
class Logger
{

    /**
     * Detailed debug information
     */
    const DEBUG = 100;
    /**
     * Interesting events
     */
    const INFO = 200;
    /**
     * Uncommon events
     */
    const NOTICE = 250;
    /**
     * Exceptional occurrences that are not errors
     */
    const WARNING = 300;
    /**
     * Runtime errors
     */
    const ERROR = 400;
    /**
     * Critical conditions
     */
    const CRITICAL = 500;
    /**
     * Action must be taken immediately
     */
    const ALERT = 550;
    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;

    /**
     * Logging levels from syslog protocol defined in RFC 5424
     *
     * This is a static variable and not a constant to serve as an extension point for custom levels
     *
     * @var string[] $levels Logging levels with the levels as key
     */
    protected static $levels = [
        self::DEBUG     => 'DEBUG',
        self::INFO      => 'INFO',
        self::NOTICE    => 'NOTICE',
        self::WARNING   => 'WARNING',
        self::ERROR     => 'ERROR',
        self::CRITICAL  => 'CRITICAL',
        self::ALERT     => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    ];

    /**
     * path to log file
     * @var
     */
    private static $PATH;

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
     * @param $path_to_log_dir
     */
    public static function setPATH($path_to_log_dir)
    {
        self::$PATH = $path_to_log_dir;
    }

    /**
     * Initializing the file stream function
     */
    public function open()
    {
        if (self::$PATH == null) {
            return;
        }
        $this->fp = fopen($this->file == null ?
            self::$PATH . DIRECTORY_SEPARATOR . $this->name . '.log' :
            self::$PATH . DIRECTORY_SEPARATOR . $this->file, 'a+');
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
     * @param int $level
     * @param string $message
     */
    private function log(int $level, string $message)
    {
        $log = "[".self::$levels[$level]."] [" . date('D M d H:i:s Y', time()) . '] ';
        $log .= $message;
        $log .= "\n";
        fwrite($this->fp, $log);
    }

    /**
     * Gets all supported logging levels.
     *
     * @return array Assoc array with human-readable level names => level codes.
     */
    public static function getLevels(): array
    {
        return array_flip(static::$levels);
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * @param string $message The log message
     */
    public function debug($message)
    {
        $this->log(static::DEBUG, (string) $message);
    }
    /**
     * Adds a log record at the INFO level.
     *
     * @param string $message The log message
     */
    public function info($message)
    {
        $this->log(static::INFO, (string) $message);
    }
    /**
     * Adds a log record at the NOTICE level.
     *
     * @param string $message The log message
     */
    public function notice($message)
    {
        $this->log(static::NOTICE, (string) $message);
    }
    /**
     * Adds a log record at the WARNING level.
     *
     * @param string $message The log message
     */
    public function warning($message)
    {
        $this->log(static::WARNING, (string) $message);
    }
    /**
     * Adds a log record at the ERROR level.
     *
     * @param string $message The log message
     */
    public function error($message)
    {
        $this->log(static::ERROR, (string) $message);
    }
    /**
     * Adds a log record at the CRITICAL level.
     *
     *
     * @param string $message The log message
     */
    public function critical($message)
    {
        $this->log(static::CRITICAL, (string) $message);
    }
    /**
     * Adds a log record at the ALERT level.
     *
     * @param string $message The log message
     */
    public function alert($message)
    {
        $this->log(static::ALERT, (string) $message);
    }
    /**
     * Adds a log record at the EMERGENCY level.
     *
     * @param string $message The log message
     */
    public function emergency($message)
    {
        $this->log(static::EMERGENCY, (string) $message);
    }

    /**
     * destruct file stream
     */
    public function __destruct()
    {
        fclose($this->fp);
    }
}