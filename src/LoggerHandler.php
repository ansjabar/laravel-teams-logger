<?php

namespace AnsJabar\LaravelTeamsLogger;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class LoggerHandler extends AbstractProcessingHandler
{
    /** @var string */
    private $url;

    /** @var string */
    private $style;

    /** @var string */
    private $name;

    /**
     * @param $url
     * @param int $level
     * @param string $name
     * @param bool $bubble
     */
    public function __construct($url, $level = MonologLogger::DEBUG, $style = 'simple', $name = 'Default', $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->url   = $url;
        $this->style = $style;
        $this->name  = $name;
    }

    /**
     * @param array $record
     *
     * @return LoggerMessage
     */
    protected function getMessage(array $record)
    {
        $name = $record['level_name'];
        try {
            $exception = $record['context']['exception'];
            $content = "<br><strong>{$record['message']}</strong>";
            $content .= " in file <strong>{$exception->getFile()}</strong>";
            $content .= " at line <strong>{$exception->getLine()}</strong>";
            $content .= "<br>{$exception->getTraceAsString()}";

        } catch (\Exception $th) {
            $content = $record['message'];
        }
        $loggerColour = new LoggerColour($name);

        return new LoggerMessage([
            'text'       => ($this->name ? $this->name . ' - ' : '') . '<span style="color:#' . (string) $loggerColour . '">' . $name . '</span>: ' . $content,
            'themeColor' => (string) $loggerColour,
        ]);
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        $json = json_encode($this->getMessage($record));
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json)
        ]);

        curl_exec($ch);
    }
}
