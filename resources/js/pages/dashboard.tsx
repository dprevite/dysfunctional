import { AppSidebar } from '@/components/app-sidebar';
import { FunctionCallsChart } from '@/components/dashboard/function-calls-chart';
import { StatCard } from '@/components/dashboard/stat-card';
import { TerminalBlock } from '@/components/dashboard/terminal-block';
import { SidebarInset, SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar';
import { useEchoPublic } from '@laravel/echo-react';
import { Activity, CheckCircle, Code2, FileText, HeartPulse, LogOut, Settings, User } from 'lucide-react';
import React from 'react';

interface DropdownMenuProps {
    children: React.ReactNode;
}

const DropdownMenu = ({ children }: DropdownMenuProps) => {
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

interface ChartDataPoint {
    time: string;
    success: number;
    errors: number;
}

interface LogEntry {
    timestamp: string;
    message: string;
}

interface LogData {
    id: string;
    title: string;
    functionName?: string;
    runtime?: string;
    status: 'running' | 'success' | 'error';
    exitCode?: number;
    startTime?: string;
    metrics?: {
        responseTime?: string;
        executionTime?: string;
        maxMemory?: string;
        cost?: string;
    };
    scheduled?: {
        cron: string;
        description: string;
    };
    entries: LogEntry[];
}

interface DashboardProps {
    stats: {
        functions: number;
        totalRuns: number;
        runsToday: number;
        unresolvedErrors: number;
    };
    chartData: {
        '24h': ChartDataPoint[];
        '7d': ChartDataPoint[];
        '30d': ChartDataPoint[];
    };
    logs: LogData[];
}

interface LogMessage {
    level: string;
    message: string;
    context: Record<string, any>;
    timestamp: string;
    formatted: string;
}

export default function Dashboard({ stats, chartData, logs }: DashboardProps) {
    const [logMessages, setLogMessages] = React.useState<LogMessage[]>([]);
    const logContainerRef = React.useRef<HTMLDivElement>(null);

    useEchoPublic('logs', 'LogBroadcastEvent', (event: LogMessage) => {
        setLogMessages((prev) => [...prev.slice(-49), event]);
    });

    React.useEffect(() => {
        if (logContainerRef.current) {
            logContainerRef.current.scrollTop =
                logContainerRef.current.scrollHeight;
        }
    }, [logMessages]);
    const [sidebarWidth, setSidebarWidth] = React.useState('12rem');

    React.useEffect(() => {
        const observer = new MutationObserver(() => {
            const sidebar = document.querySelector('[data-slot="sidebar"]');
            if (sidebar) {
                const state = sidebar.getAttribute('data-state');
                setSidebarWidth(state === 'collapsed' ? '3rem' : '12rem');
            }
        });

        const sidebar = document.querySelector('[data-slot="sidebar"]');
        if (sidebar) {
            observer.observe(sidebar, {
                attributes: true,
                attributeFilter: ['data-state'],
            });
        }

        return () => observer.disconnect();
    }, []);

    return (
        <SidebarProvider>
            <AppSidebar />
            <SidebarInset>
                <div className="flex min-h-screen flex-col bg-[#0a0a0a] pb-10 text-gray-300">
                    {/* Noise Overlay */}
                    <svg
                        className="pointer-events-none fixed inset-0 z-[99] h-screen mix-blend-overlay"
                        xmlns="http://www.w3.org/2000/svg"
                        version="1.1"
                        xmlnsXlink="http://www.w3.org/1999/xlink"
                        width="100%"
                        height="100%"
                        preserveAspectRatio="none"
                    >
                        <defs>
                            <filter id="noise-filter">
                                <feTurbulence
                                    type="turbulence"
                                    baseFrequency="1"
                                    numOctaves="1"
                                    stitchTiles="stitch"
                                    result="noise"
                                />
                                <feColorMatrix
                                    type="matrix"
                                    values="0 0 0 0 0
                                            0 0 0 0 0
                                            0 0 0 0 0
                                            0 0 0 1 0"
                                    result="coloredNoise"
                                />
                            </filter>
                        </defs>
                        <rect width="100%" height="100%" filter="url(#noise-filter)" />
                    </svg>

                    {/* Header */}
                    <header className="sticky top-0 z-50 border-b border-gray-700/50 bg-black/80 shadow-lg shadow-black/20 backdrop-blur-sm">
                        <div className="flex items-center justify-between px-6 py-3">
                            <div className="flex items-center gap-2">
                                <SidebarTrigger />
                            </div>

                            {/* User Dropdown */}
                            <DropdownMenu>
                                <DropdownMenuTrigger>
                                    <div className="flex cursor-pointer items-center gap-1 font-mono text-sm font-semibold text-white">
                                        <span>dprevite</span>
                                        <span className="animate-blink inline-block h-0.5 w-2 self-end bg-gray-500/60"></span>
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
                                icon={Code2}
                                label="Functions"
                                value={stats.functions}
                                status="online"
                            />
                            <StatCard
                                icon={Activity}
                                label="Total Runs"
                                value={stats.totalRuns}
                            />
                            <StatCard
                                icon={Activity}
                                label="Runs Today"
                                value={stats.runsToday}
                            />
                            <StatCard
                                icon={HeartPulse}
                                label="Unresolved Errors"
                                value={stats.unresolvedErrors}
                            />
                        </div>

                        {/* Real-time Logs */}
                        <div className="overflow-hidden rounded-lg border border-gray-700/50 bg-black/40 p-4 shadow-lg shadow-black/20 backdrop-blur-sm">
                            <div className="mb-3 flex items-center gap-2">
                                <Activity className="h-4 w-4 text-blue-400" />
                                <h2 className="font-mono text-sm font-semibold text-white">
                                    Real-time Logs
                                </h2>
                                <div className="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-400 shadow-lg shadow-blue-400/50"></div>
                            </div>
                            <div
                                ref={logContainerRef}
                                className="max-h-96 w-full space-y-1 overflow-x-auto overflow-y-auto font-mono text-xs"
                            >
                                {logMessages.length === 0 ? (
                                    <div className="text-gray-500">
                                        Waiting for log messages...
                                    </div>
                                ) : (
                                    logMessages.map((log, index) => {
                                        const getLevelColor = (
                                            level: string,
                                        ) => {
                                            switch (level) {
                                                case 'debug':
                                                    return 'text-gray-400';
                                                case 'info':
                                                    return 'text-blue-400';
                                                case 'notice':
                                                    return 'text-cyan-400';
                                                case 'warning':
                                                    return 'text-yellow-400';
                                                case 'error':
                                                    return 'text-red-400';
                                                case 'critical':
                                                    return 'text-red-500';
                                                case 'alert':
                                                    return 'text-orange-500';
                                                case 'emergency':
                                                    return 'text-purple-500';
                                                default:
                                                    return 'text-gray-300';
                                            }
                                        };

                                        return (
                                            <div
                                                key={index}
                                                className="flex items-start gap-2 text-gray-300"
                                            >
                                                <span className="shrink-0 text-gray-500">
                                                    [{log.timestamp}]
                                                </span>
                                                <span
                                                    className={`uppercase ${getLevelColor(log.level)} min-w-[60px] shrink-0 font-semibold`}
                                                >
                                                    [{log.level}]
                                                </span>
                                                <span className="min-w-0 whitespace-pre-wrap">
                                                    {log.message}
                                                </span>
                                            </div>
                                        );
                                    })
                                )}
                            </div>
                        </div>

                        {/* Function Calls Chart */}
                        <FunctionCallsChart
                            data24h={chartData['24h']}
                            data7d={chartData['7d']}
                            data30d={chartData['30d']}
                        />

                        {/* Terminal Blocks */}
                        {logs.map((log) => (
                            <TerminalBlock
                                key={log.id}
                                title={log.title}
                                functionName={log.functionName}
                                runtime={log.runtime}
                                status={log.status}
                                exitCode={log.exitCode}
                                logId={log.id}
                                startTime={log.startTime}
                                metrics={log.metrics}
                                scheduled={log.scheduled}
                                logs={log.entries}
                            />
                        ))}
                    </main>

                    {/* Fixed Status Bar */}
                    <footer
                        className="fixed right-0 bottom-0 left-0 z-50 border-t border-gray-700/50 bg-black/90 px-6 py-2 shadow-lg shadow-black/20 backdrop-blur-sm transition-[margin] duration-200"
                        style={{ marginLeft: sidebarWidth }}
                    >
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
                                <div className="text-gray-500">
                                    Uptime:{' '}
                                    <span className="text-gray-300">17s</span>
                                </div>
                                <div className="text-gray-500">
                                    v1.0.0-
                                    <span className="text-gray-400">
                                        a3f2e1d
                                    </span>
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            </SidebarInset>
        </SidebarProvider>
    );
}
