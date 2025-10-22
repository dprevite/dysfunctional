<?php

namespace App\Logging;

use App\Events\LogBroadcastEvent;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class BroadcastLogHandler extends AbstractProcessingHandler
{
    /**
     * Create a new broadcast log handler instance.
     */
    public function __construct(int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    /**
     * Write the log record.
     */
    protected function write(LogRecord $record): void
    {
        try {
            LogBroadcastEvent::dispatch(
                strtolower($record->level->getName()),
                $record->message,
                $record->context,
                $record->datetime->format('Y-m-d H:i:s'),
                (string) $record->formatted
            );
        } catch (\Exception $e) {
            // Silently fail if broadcasting is not available
            // This prevents logs from failing during build or when Reverb is down
        }
    }
}
