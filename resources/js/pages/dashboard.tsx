import {
    Activity,
    AlertCircle,
    CheckCircle,
    Clock,
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

const DropdownMenu = ({ children }: { children: React.ReactNode }) => {
    const [open, setOpen] = React.useState(false);
    return (
        <div className="relative">
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

    return (
        <div ref={dropdownRef} className="relative">
            <button
                onClick={() => setOpen(!open)}
                className="rounded border border-gray-600/30 bg-gray-600/10 px-2 py-0.5 font-mono text-xs text-gray-400 transition-colors hover:bg-gray-600/20 hover:text-gray-300"
            >
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
            label: 'Exit 0',
            icon: CheckCircle,
        },
        error: {
            bg: 'bg-red-500/10',
            text: 'text-red-400',
            border: 'border-red-500/30',
            label: `Exit ${exitCode ?? 1}`,
            icon: AlertCircle,
        },
    };

    const { bg, text, border, label, icon: Icon } = config[status];

    return (
        <div ref={dropdownRef} className="relative">
            <button
                onClick={() => setOpen(!open)}
                className={`flex items-center gap-1 rounded border px-2 py-0.5 font-mono text-xs transition-opacity hover:opacity-80 ${bg} ${text} ${border}`}
            >
                <Icon className={`h-3 w-3 ${status === 'running' ? 'animate-spin' : ''}`} />
                {label}
            </button>

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
                <div className="flex items-center gap-3">
                    <Terminal className="h-4 w-4 text-gray-500" />
                    <span className="font-mono text-sm text-gray-400">
                        {title}
                    </span>
                    <div className="flex items-center gap-2">
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
                    className="flex items-center gap-1.5 rounded border border-gray-600/30 bg-white/5 p-1.5 font-mono text-xs text-gray-400 transition-colors hover:bg-white/10 hover:text-gray-300"
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
                    const isError = log.message.includes('[ERROR]');
                    const isSuccess = log.message.includes('[SUCCESS]');
                    const textColor = isError
                        ? 'text-red-400'
                        : isSuccess
                          ? 'text-green-400'
                          : 'text-gray-300';

                    return (
                        <div key={index} className="group">
                            <a
                                href={`#log-${logId}-${index}`}
                                id={`log-${logId}-${index}`}
                                className="inline-block text-gray-600 transition-colors hover:text-gray-400"
                            >
                                {log.timestamp}
                            </a>{' '}
                            <span className={textColor}>{log.message}</span>
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
                                <span className="inline-block h-4 w-1.5 animate-pulse bg-white"></span>
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
                    title="Docker Container :: musing_solomon1"
                    functionName="docker-provision"
                    runtime="node-20"
                    status="success"
                    exitCode={0}
                    logId="docker-provision-001"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:29:21.123Z',
                            message: '[INFO] Container started successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:29:21.456Z',
                            message:
                                '[INFO] Provisioning Docker containers as functions',
                        },
                        {
                            timestamp: '2025-10-11T19:29:22.001Z',
                            message:
                                '[SYSTEM] Active version: musing_solomon1 | 7:29:25 PM',
                        },
                        {
                            timestamp: '2025-10-11T19:29:22.234Z',
                            message:
                                '[INFO] Used by: 0 developers | Build time: 2.3s',
                        },
                        {
                            timestamp: '2025-10-11T19:29:22.567Z',
                            message:
                                '[STATUS] Last updated: a few seconds ago | Created by: dprevite',
                        },
                        {
                            timestamp: '2025-10-11T19:29:23.001Z',
                            message: '[INFO] Connecting to Docker daemon...',
                        },
                        {
                            timestamp: '2025-10-11T19:29:23.345Z',
                            message:
                                '[INFO] Docker daemon connected successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:29:23.678Z',
                            message: '[INFO] Pulling image: node:20-alpine',
                        },
                        {
                            timestamp: '2025-10-11T19:29:24.123Z',
                            message: '[INFO] Image pulled successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:29:24.456Z',
                            message:
                                '[INFO] Creating container with id: abc123def456',
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
                            message: '[INFO] Function ready to accept requests',
                        },
                    ]}
                />

                {/* Terminal Block - Running */}
                <TerminalBlock
                    title="Image Processing :: generate-thumbnail"
                    functionName="process-image"
                    runtime="python-3.11"
                    status="running"
                    logId="image-process-002"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:30:01.123Z',
                            message:
                                '[INFO] Starting image processing pipeline',
                        },
                        {
                            timestamp: '2025-10-11T19:30:01.456Z',
                            message:
                                '[INFO] Loading image from S3: images/photo.jpg',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.001Z',
                            message:
                                '[INFO] Image loaded: 4032x3024 pixels (12.2 MB)',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.234Z',
                            message: '[INFO] Applying thumbnail transformation',
                        },
                        {
                            timestamp: '2025-10-11T19:30:02.567Z',
                            message: '[INFO] Resizing to 800x600',
                        },
                        {
                            timestamp: '2025-10-11T19:30:03.001Z',
                            message: '[INFO] Optimizing image quality',
                        },
                        {
                            timestamp: '2025-10-11T19:30:03.345Z',
                            message: '[INFO] Processing... 45% complete',
                        },
                    ]}
                />

                {/* Terminal Block - Error */}
                <TerminalBlock
                    title="Database Migration :: migrate-users"
                    functionName="db-migrate"
                    runtime="node-18"
                    status="error"
                    exitCode={1}
                    logId="db-migrate-003"
                    logs={[
                        {
                            timestamp: '2025-10-11T19:28:01.123Z',
                            message: '[INFO] Starting database migration',
                        },
                        {
                            timestamp: '2025-10-11T19:28:01.456Z',
                            message:
                                '[INFO] Connecting to database: postgres://prod-db',
                        },
                        {
                            timestamp: '2025-10-11T19:28:02.001Z',
                            message: '[INFO] Connection established',
                        },
                        {
                            timestamp: '2025-10-11T19:28:02.234Z',
                            message: '[INFO] Reading migration files...',
                        },
                        {
                            timestamp: '2025-10-11T19:28:02.567Z',
                            message: '[INFO] Found 3 pending migrations',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.001Z',
                            message:
                                '[INFO] Running migration: 001_create_users_table.sql',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.345Z',
                            message:
                                '[INFO] Migration 001 completed successfully',
                        },
                        {
                            timestamp: '2025-10-11T19:28:03.678Z',
                            message:
                                '[INFO] Running migration: 002_add_email_column.sql',
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.123Z',
                            message:
                                "[ERROR] Duplicate column name 'email' in table 'users'",
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.456Z',
                            message:
                                '[ERROR] Migration failed: Constraint violation',
                        },
                        {
                            timestamp: '2025-10-11T19:28:04.789Z',
                            message: '[ERROR] Rolling back changes...',
                        },
                        {
                            timestamp: '2025-10-11T19:28:05.001Z',
                            message: '[ERROR] Process exited with code 1',
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
