import {
    Activity,
    AlertCircle,
    CheckCircle,
    Clock,
    Code2,
    Cpu,
    FileText,
    HardDrive,
    Loader2,
    LogOut,
    OctagonAlertIcon,
    Server,
    Settings,
    Terminal,
    User
} from 'lucide-react';
import React from 'react';
import {
    Tooltip,
    TooltipContent,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const DropdownMenu = ({ children }: { children: React.ReactNode }) => {
    const [open, setOpen] = React.useState(false);
    const dropdownRef = React.useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target as Node)
            ) {
                setOpen(false);
            }
        };

        if (open) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [open]);

    return (
        <div ref={dropdownRef} className="relative">
            {React.Children.map(children, (child) => {
                if (React.isValidElement(child)) {
                    return React.cloneElement(
                        child as React.ReactElement<any>,
                        { open, setOpen },
                    );
                }
                return child;
            })}
        </div>
    );
};

const DropdownMenuTrigger = ({ children, open, setOpen }: any) => (
    <button onClick={() => setOpen(!open)} className="focus:outline-none">
        {children}
    </button>
);

const DropdownMenuContent = ({ children, open }: any) => {
    if (!open) return null;
    return (
        <div className="absolute right-0 mt-2 w-48 rounded-md border border-gray-700/50 bg-black/95 shadow-lg shadow-black/50 backdrop-blur-sm">
            {children}
        </div>
    );
};

const DropdownMenuItem = ({ children, onClick }: any) => (
    <button
        onClick={onClick}
        className="flex w-full items-center gap-2 px-4 py-2 text-left font-mono text-sm text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
    >
        {children}
    </button>
);

// Tag Dropdown Menu Component
const TagDropdown = ({
    label,
    type,
}: {
    label: string;
    type: 'function' | 'runtime' | 'status';
}) => {
    const [open, setOpen] = React.useState(false);
    const dropdownRef = React.useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target as Node)
            ) {
                setOpen(false);
            }
        };

        if (open) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [open]);

    const getIcon = () => {
        if (type === 'function') return Code2;
        if (type === 'runtime') return Cpu;
        return null;
    };

    const Icon = getIcon();

    return (
        <div ref={dropdownRef} className="relative">
            <button
                onClick={() => setOpen(!open)}
                className="flex items-center gap-1 rounded border border-gray-600/30 bg-gray-600/10 px-2 py-0.5 font-mono text-xs text-gray-400 transition-colors hover:bg-gray-600/20 hover:text-gray-300"
            >
                {Icon && <Icon className="h-2.5 w-2.5 shrink-0" />}
                {label}
            </button>

            {open && (
                <div className="absolute top-full left-0 z-10 mt-1 w-56 rounded-md border border-gray-700/50 bg-black/95 shadow-lg shadow-black/50 backdrop-blur-sm">
                    <button
                        onClick={() => {
                            console.log(`Exclude ${type}: ${label}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-2 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Exclude this {type}
                    </button>
                    <button
                        onClick={() => {
                            console.log(`Show only ${type}: ${label}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-2 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Show only this {type}
                    </button>
                    <div className="my-1 border-t border-gray-700/50"></div>
                    <button
                        onClick={() => {
                            console.log(`View ${type} details: ${label}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-2 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        View {type} details
                    </button>
                </div>
            )}
        </div>
    );
};

// Status Badge Component
const StatusBadge = ({
    status,
    exitCode,
}: {
    status: 'running' | 'success' | 'error';
    exitCode?: number;
}) => {
    const [open, setOpen] = React.useState(false);
    const dropdownRef = React.useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target as Node)
            ) {
                setOpen(false);
            }
        };

        if (open) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [open]);

    const config = {
        running: {
            bg: 'bg-blue-500/10',
            text: 'text-blue-400',
            border: 'border-blue-500/30',
            label: 'Running',
            icon: Loader2,
        },
        success: {
            bg: 'bg-green-500/10',
            text: 'text-green-400',
            border: 'border-green-500/30',
            label: 'Success',
            icon: CheckCircle,
        },
        error: {
            bg: 'bg-red-500/10',
            text: 'text-red-400',
            border: 'border-red-500/30',
            label: 'Error',
            icon: AlertCircle,
        },
    };

    const { bg, text, border, label, icon: Icon } = config[status];

    const button = (
        <button
            onClick={() => setOpen(!open)}
            className={`flex items-center gap-1 rounded border px-2 py-0.5 font-mono text-xs transition-opacity hover:opacity-80 ${bg} ${text} ${border}`}
        >
            <Icon
                className={`h-2.5 w-2.5 shrink-0 ${status === 'running' ? 'animate-spin' : ''}`}
            />
            {label}
        </button>
    );

    return (
        <div ref={dropdownRef} className="relative">
            {status === 'error' ? (
                <Tooltip>
                    <TooltipTrigger asChild>
                        {button}
                    </TooltipTrigger>
                    <TooltipContent>
                        <p className="font-mono text-xs">Exit code: {exitCode ?? 1}</p>
                    </TooltipContent>
                </Tooltip>
            ) : (
                button
            )}

            {open && (
                <div className="absolute top-full left-0 z-10 mt-1 w-56 rounded-md border border-gray-700/50 bg-black/95 shadow-lg shadow-black/50 backdrop-blur-sm">
                    <button
                        onClick={() => {
                            console.log(`Exclude status: ${status}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-2 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Exclude this status
                    </button>
                    <button
                        onClick={() => {
                            console.log(`Show only status: ${status}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-2 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Show only this status
                    </button>
                </div>
            )}
        </div>
    );
};

// Terminal Output Block Component
const TerminalBlock = ({
    title,
    functionName,
    runtime,
    status,
    exitCode,
    logs,
    logId,
}: {
    title: string;
    functionName?: string;
    runtime?: string;
    status: 'running' | 'success' | 'error';
    exitCode?: number;
    logs: Array<{ timestamp: string; message: string }>;
    logId?: string;
}) => {
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

    return (
        <div className="overflow-hidden rounded-md border border-gray-700/50 bg-black/50 shadow-lg shadow-black/20">
            <div className="flex items-center justify-between border-b border-gray-700/50 bg-white/[0.02] px-4 py-2">
                <div className="flex min-w-0 flex-1 items-center gap-3">
                    <Terminal className="h-4 w-4 shrink-0 text-white" />
                    <div className="min-w-0 flex-1">
                        <div
                            className="truncate font-mono text-sm"
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
                                    ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'].includes(
                                        part,
                                    )
                                ) {
                                    const methodColors: Record<string, string> = {
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
                                            <span className="text-white">{path}</span>
                                            <span className="text-gray-500">?{query}</span>
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
                        </div>
                    </div>
                    <div className="flex shrink-0 items-center gap-2">
                        {functionName && (
                            <TagDropdown label={functionName} type="function" />
                        )}
                        {runtime && (
                            <TagDropdown label={runtime} type="runtime" />
                        )}
                        <StatusBadge status={status} exitCode={exitCode} />
                    </div>
                </div>
                <button
                    onClick={downloadLogs}
                    className="ml-3 flex shrink-0 items-center gap-1.5 rounded border border-gray-600/30 bg-white/5 p-1.5 font-mono text-xs text-gray-400 transition-colors hover:bg-white/10 hover:text-gray-300"
                    title="Download logs"
                >
                    <HardDrive className="h-3.5 w-3.5" />
                </button>
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
                        hour12: false
                    });

                    // Determine text color based on log level
                    const isError = log.message.includes('[ERROR]') || log.message.includes('[CRITICAL]') || log.message.includes('[ALERT]') || log.message.includes('[EMERGENCY]');
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
                        .replace(/(\[EMERGENCY\]|\[ALERT\]|\[CRITICAL\]|\[ERROR\])/g, '<span class="font-semibold">$1</span>')
                        .replace(/(\[WARNING\])/g, '<span class="font-semibold">$1</span>')
                        .replace(/(\[SUCCESS\])/g, '<span class="font-semibold">$1</span>')
                        .replace(/(\[INFO\])/g, '<span class="font-semibold text-blue-400">$1</span>')
                        .replace(/(\[DEBUG\])/g, '<span class="font-semibold text-gray-500">$1</span>')
                        .replace(/(\[NOTICE\])/g, '<span class="font-semibold text-cyan-400">$1</span>');

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
                                    <p className="font-mono text-xs">{log.timestamp}</p>
                                </TooltipContent>
                            </Tooltip>{' '}
                            <span
                                className={textColor}
                                dangerouslySetInnerHTML={{ __html: formattedMessage }}
                            />
                        </div>
                    );
                })}
            </div>
        </div>
    );
};

// Stat Card Component
const StatCard = ({ icon: Icon, label, value, status }: any) => (
    <div className="rounded-md border border-gray-700/50 bg-black/50 p-4 shadow-md shadow-black/20">
        <div className="flex items-center gap-3">
            <div className="rounded-md bg-white/5 p-2">
                <Icon className="h-5 w-5 text-gray-400" />
            </div>
            <div className="flex-1">
                <div className="font-mono text-xs tracking-wider text-gray-500 uppercase">
                    {label}
                </div>
                <div className="font-mono text-lg font-semibold text-white">
                    {value}
                </div>
            </div>
            {status && (
                <div
                    className={`h-2 w-2 rounded-full ${status === 'online' ? 'bg-green-400 shadow-lg shadow-green-400/50' : 'bg-red-400 shadow-lg shadow-red-400/50'}`}
                />
            )}
        </div>
    </div>
);

export default function TerminalDashboard() {
    const [currentTime, setCurrentTime] = React.useState(new Date());

    React.useEffect(() => {
        const timer = setInterval(() => setCurrentTime(new Date()), 1000);
        return () => clearInterval(timer);
    }, []);

    return (
        <div className="flex min-h-screen flex-col bg-[#0a0a0a] text-gray-300">
            {/* Header */}
            <header className="sticky top-0 z-50 border-b border-gray-700/50 bg-black/80 shadow-lg shadow-black/20 backdrop-blur-sm">
                <div className="flex items-center justify-between px-6 py-3">
                    {/* Logo */}
                    <div className="flex items-center gap-2">
                        <div className="rounded-md bg-white/5 p-2">
                            <Terminal className="h-6 w-6 text-gray-400" />
                        </div>
                        <span className="font-mono text-xl font-bold tracking-tight text-white">
                            <span className="text-gray-400">DYS</span>
                            <span className="text-gray-200">FUNCTIONAL</span>
                        </span>
                    </div>

                    {/* Navigation */}
                    <nav className="hidden items-center gap-6 font-mono text-sm md:flex">
                        <a
                            href="#"
                            className="font-semibold text-white transition-colors hover:text-gray-300"
                        >
                            Dashboard
                        </a>
                        <a
                            href="#"
                            className="text-gray-500 transition-colors hover:text-gray-300"
                        >
                            Functions
                        </a>
                        <a
                            href="#"
                            className="text-gray-500 transition-colors hover:text-gray-300"
                        >
                            Runtimes
                        </a>
                        <a
                            href="#"
                            className="text-gray-500 transition-colors hover:text-gray-300"
                        >
                            Analytics
                        </a>
                        <a
                            href="#"
                            className="text-gray-500 transition-colors hover:text-gray-300"
                        >
                            Logs
                        </a>
                    </nav>

                    {/* User Dropdown */}
                    <DropdownMenu>
                        <DropdownMenuTrigger>
                            <div className="flex cursor-pointer items-center gap-1 font-mono text-sm font-semibold text-white">
                                <span>dprevite</span>
                                <span className="inline-block h-0.5 w-2 animate-blink self-end bg-gray-500/60"></span>
                            </div>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent>
                            <DropdownMenuItem>
                                <User className="h-4 w-4" />
                                Profile
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                                <Settings className="h-4 w-4" />
                                Settings
                            </DropdownMenuItem>
                            <DropdownMenuItem>
                                <FileText className="h-4 w-4" />
                                Documentation
                            </DropdownMenuItem>
                            <div className="my-1 border-t border-gray-700/50"></div>
                            <DropdownMenuItem>
                                <LogOut className="h-4 w-4" />
                                Logout
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </header>

            {/* Main Content */}
            <main className="flex-1 space-y-6 overflow-auto px-6 py-6">
                {/* Stats Grid */}
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatCard
                        icon={Server}
                        label="Functions"
                        value="3"
                        status="online"
                    />
                    <StatCard icon={Activity} label="Total Runs" value="126" />
                    <StatCard icon={Activity} label="Runs Today" value="21" />
                    <StatCard
                        icon={OctagonAlertIcon}
                        label="Unresolved Errors"
                        value="0"
                        status="online"
                    />
                </div>

                {/* Full Width Terminal Block - Successful Run */}
                <TerminalBlock
                    title="POST /run/plex-activity-webhook"
                    functionName="plex-webhook"
                    runtime="node-20"
                    status="success"
                    exitCode={0}
                    logId="plex-webhook-001"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:29:20.001Z',
                            message: '[DEBUG] Initializing container provisioner',
                        },
                        {
                            timestamp: '2025-10-11T19:29:20.234Z',
                            message: '[DEBUG] Loading configuration from /etc/dysfunctional/config.yml',
                        },
                        {
                            timestamp: '2025-10-11T19:29:20.567Z',
                            message: '[INFO] Starting container provisioning process',
                        },
                        {
                            timestamp: '2025-10-11T19:29:21.123Z',
                            message: '[INFO] Container started successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:29:21.456Z',
                            message: '[INFO] Provisioning Docker containers as functions',
                        },
                        {
                            timestamp: '2025-10-11T19:29:22.001Z',
                            message: '[NOTICE] Active version: musing_solomon1 | Build: 2.3s',
                        },
                        {
                            timestamp: '2025-10-11T19:29:22.234Z',
                            message: '[INFO] Used by: 0 developers | Created by: dprevite',
                        },
                        {
                            timestamp: '2025-10-11T19:29:22.567Z',
                            message: '[DEBUG] Checking Docker daemon availability',
                        },
                        {
                            timestamp: '2025-10-11T19:29:23.001Z',
                            message: '[INFO] Connecting to Docker daemon at unix:///var/run/docker.sock',
                        },
                        {
                            timestamp: '2025-10-11T19:29:23.345Z',
                            message: '[INFO] Docker daemon connected successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:29:23.678Z',
                            message: '[INFO] Pulling image: node:20-alpine',
                        },
                        {
                            timestamp: '2025-10-11T19:29:23.890Z',
                            message: '[DEBUG] Image pull progress: 23%',
                        },
                        {
                            timestamp: '2025-10-11T19:29:24.123Z',
                            message: '[INFO] Image pulled successfully (sha256:abc123def456)',
                        },
                        {
                            timestamp: '2025-10-11T19:29:24.456Z',
                            message: '[INFO] Creating container with id: abc123def456',
                        },
                        {
                            timestamp: '2025-10-11T19:29:24.789Z',
                            message: '[INFO] Container created successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:29:25.001Z',
                            message: '[INFO] Starting container...',
                        },
                        {
                            timestamp: '2025-10-11T19:29:25.234Z',
                            message: '[SUCCESS] Container is now running',
                        },
                        {
                            timestamp: '2025-10-11T19:29:25.567Z',
                            message: '[INFO] Health check passed',
                        },
                        {
                            timestamp: '2025-10-11T19:29:25.890Z',
                            message: '[INFO] Function ready to accept requests on port 3000',
                        },
                        {
                            timestamp: '2025-10-11T19:29:26.123Z',
                            message: '[DEBUG] Registering function endpoint: /api/docker-provision',
                        },
                    ]}
                />

                {/* Terminal Block - Running */}
                <TerminalBlock
                    title="GET /run/chatbot-fetch-whats-playing"
                    functionName="chatbot-media"
                    runtime="python-3.11"
                    status="running"
                    logId="chatbot-media-002"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:30:00.789Z',
                            message: '[DEBUG] Initializing image processor worker',
                        },
                        {
                            timestamp: '2025-10-11T19:30:01.123Z',
                            message: '[INFO] Starting image processing pipeline',
                        },
                        {
                            timestamp: '2025-10-11T19:30:01.456Z',
                            message: '[INFO] Loading image from S3: images/photo.jpg',
                        },
                        {
                            timestamp: '2025-10-11T19:30:01.789Z',
                            message: '[DEBUG] S3 GetObject request initiated',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.001Z',
                            message: '[INFO] Image loaded: 4032x3024 pixels (12.2 MB)',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.234Z',
                            message: '[INFO] Applying thumbnail transformation',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.456Z',
                            message: '[DEBUG] Allocating memory buffer: 12.2 MB',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.567Z',
                            message: '[INFO] Resizing to 800x600',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.890Z',
                            message: '[DEBUG] Using Lanczos3 interpolation algorithm',
                        },
                        {
                            timestamp: '2025-10-11T19:30:03.001Z',
                            message: '[INFO] Optimizing image quality',
                        },
                        {
                            timestamp: '2025-10-11T19:30:03.234Z',
                            message: '[DEBUG] Processing... 35% complete',
                        },
                        {
                            timestamp: '2025-10-11T19:30:03.567Z',
                            message: '[DEBUG] Processing... 68% complete',
                        },
                        {
                            timestamp: '2025-10-11T19:30:03.890Z',
                            message: '[INFO] Processing... 85% complete',
                        },
                        {
                            timestamp: '2025-10-11T19:30:04.123Z',
                            message: '[WARNING] High memory usage detected: 89%',
                        },
                    ]}
                />

                {/* Terminal Block - Error */}
                <TerminalBlock
                    title="GET /run/resize-image?width=1500px&src=IMG123.jpg&format=png"
                    functionName="resize-image"
                    runtime="go-1.21"
                    status="error"
                    exitCode={1}
                    logId="resize-image-003"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:28:00.890Z',
                            message: '[DEBUG] Database migration worker started',
                        },
                        {
                            timestamp: '2025-10-11T19:28:01.123Z',
                            message: '[INFO] Starting database migration',
                        },
                        {
                            timestamp: '2025-10-11T19:28:01.456Z',
                            message: '[INFO] Connecting to database: postgres://prod-db:5432/dysfunctional',
                        },
                        {
                            timestamp: '2025-10-11T19:28:01.789Z',
                            message: '[DEBUG] Connection pool initialized with max 10 connections',
                        },
                        {
                            timestamp: '2025-10-11T19:28:02.001Z',
                            message: '[INFO] Connection established',
                        },
                        {
                            timestamp: '2025-10-11T19:28:02.234Z',
                            message: '[INFO] Reading migration files from ./migrations',
                        },
                        {
                            timestamp: '2025-10-11T19:28:02.567Z',
                            message: '[INFO] Found 3 pending migrations',
                        },
                        {
                            timestamp: '2025-10-11T19:28:02.890Z',
                            message: '[DEBUG] Beginning transaction',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.001Z',
                            message: '[INFO] Running migration: 001_create_users_table.sql',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.345Z',
                            message: '[INFO] Migration 001 completed successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.567Z',
                            message: '[NOTICE] Created table: users (5 columns)',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.678Z',
                            message: '[INFO] Running migration: 002_add_email_column.sql',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.890Z',
                            message: '[DEBUG] Executing: ALTER TABLE users ADD COLUMN email VARCHAR(255)',
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.123Z',
                            message: "[ERROR] Duplicate column name 'email' in table 'users'",
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.234Z',
                            message: '[ERROR] Query failed: column "email" of relation "users" already exists',
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.456Z',
                            message: '[ERROR] Migration failed: Constraint violation',
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.567Z',
                            message: '[WARNING] Rolling back transaction...',
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.789Z',
                            message: '[ERROR] Rollback completed',
                        },
                        {
                            timestamp: '2025-10-11T19:28:05.001Z',
                            message: '[CRITICAL] Process exited with code 1',
                        },
                    ]}
                />

                {/* Terminal Block - Additional Example 1 */}
                <TerminalBlock
                    title="PUT /run/update-user-profile?userId=42"
                    functionName="user-profile"
                    runtime="php-8.2"
                    status="success"
                    exitCode={0}
                    logId="user-profile-004"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:31:00.123Z',
                            message: '[INFO] Received profile update request',
                        },
                        {
                            timestamp: '2025-10-11T19:31:00.456Z',
                            message: '[DEBUG] Validating request payload',
                        },
                        {
                            timestamp: '2025-10-11T19:31:00.789Z',
                            message: '[INFO] User ID: 42',
                        },
                        {
                            timestamp: '2025-10-11T19:31:01.123Z',
                            message: '[DEBUG] Connecting to Redis cache',
                        },
                        {
                            timestamp: '2025-10-11T19:31:01.456Z',
                            message: '[INFO] Cache hit for user:42',
                        },
                        {
                            timestamp: '2025-10-11T19:31:01.789Z',
                            message: '[INFO] Updating profile fields: name, email, avatar',
                        },
                        {
                            timestamp: '2025-10-11T19:31:02.123Z',
                            message: '[DEBUG] Executing SQL UPDATE statement',
                        },
                        {
                            timestamp: '2025-10-11T19:31:02.456Z',
                            message: '[INFO] Database updated successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:31:02.789Z',
                            message: '[DEBUG] Invalidating cache key: user:42',
                        },
                        {
                            timestamp: '2025-10-11T19:31:03.123Z',
                            message: '[NOTICE] Broadcasting update event to websocket clients',
                        },
                        {
                            timestamp: '2025-10-11T19:31:03.456Z',
                            message: '[SUCCESS] Profile updated successfully',
                        },
                    ]}
                />

                {/* Terminal Block - Additional Example 2 */}
                <TerminalBlock
                    title="DELETE /run/cleanup-temp-files?older_than=7d"
                    functionName="cleanup-worker"
                    runtime="rust-1.75"
                    status="running"
                    logId="cleanup-worker-005"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:32:00.001Z',
                            message: '[INFO] Starting cleanup worker',
                        },
                        {
                            timestamp: '2025-10-11T19:32:00.234Z',
                            message: '[DEBUG] Parsing parameter: older_than=7d',
                        },
                        {
                            timestamp: '2025-10-11T19:32:00.567Z',
                            message: '[INFO] Target cutoff date: 2025-10-04T19:32:00Z',
                        },
                        {
                            timestamp: '2025-10-11T19:32:00.890Z',
                            message: '[INFO] Scanning directory: /tmp/uploads',
                        },
                        {
                            timestamp: '2025-10-11T19:32:01.123Z',
                            message: '[DEBUG] Found 1,247 files',
                        },
                        {
                            timestamp: '2025-10-11T19:32:01.456Z',
                            message: '[INFO] Filtering files older than cutoff date',
                        },
                        {
                            timestamp: '2025-10-11T19:32:01.789Z',
                            message: '[DEBUG] 342 files match criteria',
                        },
                        {
                            timestamp: '2025-10-11T19:32:02.123Z',
                            message: '[INFO] Beginning deletion process',
                        },
                        {
                            timestamp: '2025-10-11T19:32:02.456Z',
                            message: '[DEBUG] Deleted: tmp_upload_a3f2e1.bin (2.3 MB)',
                        },
                        {
                            timestamp: '2025-10-11T19:32:02.789Z',
                            message: '[DEBUG] Deleted: tmp_upload_9c7b4f.bin (1.7 MB)',
                        },
                        {
                            timestamp: '2025-10-11T19:32:03.123Z',
                            message: '[DEBUG] Progress: 12/342 files deleted',
                        },
                        {
                            timestamp: '2025-10-11T19:32:03.456Z',
                            message: '[WARNING] Permission denied: tmp_system_lock.dat',
                        },
                        {
                            timestamp: '2025-10-11T19:32:03.789Z',
                            message: '[INFO] Continuing with remaining files',
                        },
                    ]}
                />
            </main>

            {/* Fixed Status Bar */}
            <footer className="border-t border-gray-700/50 bg-black/90 px-6 py-2 shadow-lg shadow-black/20 backdrop-blur-sm">
                <div className="flex items-center justify-between font-mono text-xs">
                    <div className="flex items-center gap-6">
                        <div className="flex items-center gap-2">
                            <div className="h-1.5 w-1.5 animate-pulse rounded-full bg-green-400 shadow-lg shadow-green-400/50"></div>
                            <span className="font-semibold text-green-400">
                                Connected
                            </span>
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckCircle className="h-3.5 w-3.5 text-green-400" />
                            <span className="font-semibold text-green-400">
                                Healthy
                            </span>
                        </div>
                    </div>

                    <div className="flex items-center gap-6">
                        <div className="flex items-center gap-2 text-gray-400">
                            <Clock className="h-3 w-3" />
                            {currentTime.toLocaleTimeString()}
                        </div>
                        <div className="text-gray-500">
                            Uptime: <span className="text-gray-300">17s</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
