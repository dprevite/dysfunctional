import {
    Download,
    Eye,
    Link2,
    MoreVertical,
    Trash2,
} from 'lucide-react';
import React from 'react';
import { Link } from '@inertiajs/react';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import { ScheduledBadge } from './badges/scheduled-badge';
import { StatusBadge } from './badges/status-badge';
import { TagDropdown } from './badges/tag-dropdown';

interface LogEntry {
    timestamp: string;
    message: string;
}

interface Metrics {
    executionTime?: string;
    maxMemory?: string;
    cost?: string;
    isBackgroundJob?: boolean;
    responseTime?: string;
    totalExecutionTime?: string;
}

interface ScheduledInfo {
    cron: string;
    description: string;
}

interface TerminalBlockProps {
    title: string;
    functionName?: string;
    runtime?: string;
    status: 'running' | 'success' | 'error';
    exitCode?: number;
    logs: LogEntry[];
    logId?: string;
    scheduled?: ScheduledInfo;
    metrics?: Metrics;
    startTime?: string;
}

export function TerminalBlock({
    title,
    functionName,
    runtime,
    status,
    exitCode,
    logs,
    logId,
    scheduled,
    metrics,
    startTime,
}: TerminalBlockProps) {
    const logContainerRef = React.useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        if (logContainerRef.current) {
            logContainerRef.current.scrollTop =
                logContainerRef.current.scrollHeight;
        }
    }, [logs]);

    const downloadLogs = () => {
        const logText = logs
            .map((log) => `${log.timestamp} ${log.message}`)
            .join('\n');
        const blob = new Blob([logText], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${logId || 'logs'}-${Date.now()}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    };

    const formatStartTime = (timestamp?: string) => {
        if (!timestamp) return null;
        const date = new Date(timestamp);
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true,
            timeZoneName: 'short',
        });
    };

    const shortenUuid = (uuid?: string) => {
        if (!uuid) return null;
        // Return first 8 characters of UUID
        return uuid.substring(0, 8);
    };

    return (
        <div className="overflow-hidden rounded-md border border-gray-700/50 bg-black/50 shadow-lg shadow-black/20">
            <div className="flex items-center justify-between border-b border-gray-700/50 bg-white/[0.02] px-4 py-2">
                <div className="flex min-w-0 flex-1 items-center gap-3">
                    <div className="min-w-0 flex-1">
                        <Link
                            href={`/logs/${logId}`}
                            className="block truncate font-mono text-sm transition-opacity hover:opacity-70"
                            style={{
                                maskImage:
                                    'linear-gradient(to right, black 85%, transparent 100%)',
                                WebkitMaskImage:
                                    'linear-gradient(to right, black 85%, transparent 100%)',
                            }}
                        >
                            {title.split(' ').map((part, index) => {
                                // Check if part is HTTP method
                                if (
                                    [
                                        'GET',
                                        'POST',
                                        'PUT',
                                        'DELETE',
                                        'PATCH',
                                    ].includes(part)
                                ) {
                                    const methodColors: Record<string, string> =
                                        {
                                            GET: 'text-blue-400',
                                            POST: 'text-green-400',
                                            PUT: 'text-yellow-400',
                                            DELETE: 'text-red-400',
                                            PATCH: 'text-purple-400',
                                        };
                                    return (
                                        <span
                                            key={index}
                                            className={`font-semibold ${methodColors[part] || 'text-gray-400'}`}
                                        >
                                            {part}{' '}
                                        </span>
                                    );
                                }
                                // Check if part contains query params
                                if (part.includes('?')) {
                                    const [path, query] = part.split('?');
                                    return (
                                        <span key={index}>
                                            <span className="text-white">
                                                {path}
                                            </span>
                                            <span className="text-gray-500">
                                                ?{query}
                                            </span>
                                        </span>
                                    );
                                }
                                // Regular path
                                return (
                                    <span key={index} className="text-white">
                                        {part}{' '}
                                    </span>
                                );
                            })}
                        </Link>
                    </div>
                    <div className="flex shrink-0 items-center gap-2">
                        {functionName && (
                            <TagDropdown label={functionName} type="function" />
                        )}
                        {runtime && (
                            <TagDropdown label={runtime} type="runtime" />
                        )}
                        {scheduled && (
                            <ScheduledBadge
                                cron={scheduled.cron}
                                description={scheduled.description}
                            />
                        )}
                        <StatusBadge status={status} exitCode={exitCode} />
                    </div>
                </div>
                <div className="relative ml-3">
                    {(() => {
                        const [toolsOpen, setToolsOpen] = React.useState(false);
                        const toolsRef = React.useRef<HTMLDivElement>(null);

                        React.useEffect(() => {
                            const handleClickOutside = (event: MouseEvent) => {
                                if (
                                    toolsRef.current &&
                                    !toolsRef.current.contains(
                                        event.target as Node,
                                    )
                                ) {
                                    setToolsOpen(false);
                                }
                            };
                            if (toolsOpen) {
                                document.addEventListener(
                                    'mousedown',
                                    handleClickOutside,
                                );
                            }
                            return () => {
                                document.removeEventListener(
                                    'mousedown',
                                    handleClickOutside,
                                );
                            };
                        }, [toolsOpen]);

                        return (
                            <div ref={toolsRef}>
                                <button
                                    onClick={() => setToolsOpen(!toolsOpen)}
                                    className="flex shrink-0 items-center gap-1.5 rounded border border-gray-600/30 bg-gray-600/10 px-2 py-0.5 font-mono text-[0.65rem] text-gray-400 transition-colors hover:bg-gray-600/20 hover:text-gray-300"
                                    title="Tools"
                                >
                                    <MoreVertical className="h-2.5 w-2.5 shrink-0" />
                                </button>
                                {toolsOpen && (
                                    <div className="absolute right-0 top-full z-10 mt-1 w-48 rounded-md border border-gray-700/50 bg-black/95 shadow-lg shadow-black/50 backdrop-blur-sm">
                                        <button
                                            onClick={() => {
                                                downloadLogs();
                                                setToolsOpen(false);
                                            }}
                                            className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-xs text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                                        >
                                            <Download className="h-3 w-3" />
                                            Download logs
                                        </button>
                                        <button
                                            onClick={() => {
                                                console.log('View raw logs');
                                                setToolsOpen(false);
                                            }}
                                            className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-xs text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                                        >
                                            <Eye className="h-3 w-3" />
                                            View raw
                                        </button>
                                        <button
                                            onClick={() => {
                                                console.log('Delete logs');
                                                setToolsOpen(false);
                                            }}
                                            className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-xs text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                                        >
                                            <Trash2 className="h-3 w-3" />
                                            Delete
                                        </button>
                                        <button
                                            onClick={() => {
                                                console.log('Copy share link');
                                                setToolsOpen(false);
                                            }}
                                            className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-xs text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                                        >
                                            <Link2 className="h-3 w-3" />
                                            Copy share link
                                        </button>
                                    </div>
                                )}
                            </div>
                        );
                    })()}
                </div>
            </div>
            <div
                ref={logContainerRef}
                className="max-h-64 overflow-y-auto p-4 font-mono text-xs text-gray-300"
            >
                {logs.map((log, index) => {
                    // Format timestamp to human readable
                    const date = new Date(log.timestamp);
                    const humanTime = date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false,
                    });

                    // Determine text color based on log level
                    const isError =
                        log.message.includes('[ERROR]') ||
                        log.message.includes('[CRITICAL]') ||
                        log.message.includes('[ALERT]') ||
                        log.message.includes('[EMERGENCY]');
                    const isWarning = log.message.includes('[WARNING]');
                    const isSuccess = log.message.includes('[SUCCESS]');
                    const textColor = isError
                        ? 'text-red-400'
                        : isWarning
                          ? 'text-yellow-400'
                          : isSuccess
                            ? 'text-green-400'
                            : 'text-gray-300';

                    // Highlight log level tags
                    const formattedMessage = log.message
                        .replace(
                            /(\[EMERGENCY\]|\[ALERT\]|\[CRITICAL\]|\[ERROR\])/g,
                            '<span class="font-semibold">$1</span>',
                        )
                        .replace(
                            /(\[WARNING\])/g,
                            '<span class="font-semibold">$1</span>',
                        )
                        .replace(
                            /(\[SUCCESS\])/g,
                            '<span class="font-semibold">$1</span>',
                        )
                        .replace(
                            /(\[INFO\])/g,
                            '<span class="font-semibold text-blue-400">$1</span>',
                        )
                        .replace(
                            /(\[DEBUG\])/g,
                            '<span class="font-semibold text-gray-500">$1</span>',
                        )
                        .replace(
                            /(\[NOTICE\])/g,
                            '<span class="font-semibold text-cyan-400">$1</span>',
                        );

                    return (
                        <div key={index} className="group">
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <a
                                        href={`#log-${logId}-${index}`}
                                        id={`log-${logId}-${index}`}
                                        className="inline-block text-gray-600 transition-colors hover:text-gray-400"
                                    >
                                        {humanTime}
                                    </a>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p className="font-mono text-xs">
                                        {log.timestamp}
                                    </p>
                                </TooltipContent>
                            </Tooltip>{' '}
                            <span
                                className={textColor}
                                dangerouslySetInnerHTML={{
                                    __html: formattedMessage,
                                }}
                            />
                        </div>
                    );
                })}
            </div>
            {metrics && (
                <div className="border-t border-gray-700/50 bg-white/[0.01] px-4 py-1.5">
                    <div className="flex flex-wrap items-center gap-x-6 gap-y-1 font-mono text-[0.65rem]">
                        {logId && (
                            <div className="flex items-center gap-1.5">
                                <span className="text-gray-500">Run ID:</span>
                                <span className="font-semibold text-gray-300">
                                    {shortenUuid(logId)}
                                </span>
                            </div>
                        )}
                        {startTime && (
                            <div className="flex items-center gap-1.5">
                                <span className="text-gray-500">Started:</span>
                                <span className="whitespace-nowrap text-gray-300">
                                    {formatStartTime(startTime)}
                                </span>
                            </div>
                        )}
                        {metrics.executionTime && (
                            <div className="flex items-center gap-1.5">
                                <span className="text-gray-500">Runtime:</span>
                                <span className="text-gray-300">
                                    {metrics.executionTime}
                                </span>
                            </div>
                        )}
                        {metrics.responseTime && (
                            <div className="flex items-center gap-1.5">
                                <span className="text-gray-500">Response:</span>
                                <span className="text-gray-300">
                                    {metrics.responseTime}
                                </span>
                            </div>
                        )}
                        {metrics.maxMemory && (
                            <div className="flex items-center gap-1.5">
                                <span className="text-gray-500">Memory:</span>
                                <span className="text-gray-300">
                                    {metrics.maxMemory}
                                </span>
                            </div>
                        )}
                        {metrics.cost && (
                            <div className="flex items-center gap-1.5">
                                <span className="text-gray-500">Cost:</span>
                                <span className="text-gray-300">
                                    {metrics.cost}
                                </span>
                            </div>
                        )}
                    </div>
                </div>
            )}
        </div>
    );
}
