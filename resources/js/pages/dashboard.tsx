import { Activity, Clock, FileText, LogOut, OctagonAlertIcon, Server, Settings, Terminal, User } from 'lucide-react';
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

// Terminal Output Block Component
const TerminalBlock = ({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) => (
    <div className="overflow-hidden rounded-md border border-gray-700/50 bg-black/50 shadow-lg shadow-black/20">
        <div className="flex items-center gap-2 border-b border-gray-700/50 bg-white/[0.02] px-4 py-2">
            <Terminal className="h-4 w-4 text-gray-500" />
            <span className="font-mono text-sm text-gray-400">{title}</span>
        </div>
        <div className="p-4 font-mono text-sm text-gray-300">{children}</div>
    </div>
);

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
                            <span className="text-gray-200">FUN</span>
                            <span className="text-gray-400">CTIONAL</span>
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
                            <div className="flex cursor-pointer items-center gap-2 rounded-md border border-gray-700/50 bg-white/5 px-3 py-1.5 transition-colors hover:bg-white/10">
                                <User className="h-4 w-4 text-gray-400" />
                                <span className="font-mono text-sm text-gray-300">
                                    dprevite
                                </span>
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

                {/* Terminal Blocks */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <TerminalBlock title="green-perch-4 :: code-server">
                        <div className="space-y-1">
                            <div className="flex items-center gap-2">
                                <span className="text-gray-600">⚙</span>
                                <span className="text-gray-400">
                                    Creating settings file...
                                </span>
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="text-gray-600">⚙</span>
                                <span className="text-gray-400">
                                    Creating machine settings file...
                                </span>
                            </div>
                            <div className="mt-2 text-gray-300">
                                Installing code-server!
                            </div>
                            <div className="text-gray-700">######</div>
                            <div className="mt-2 flex items-center gap-2">
                                <span className="text-gray-400">📦</span>
                                <span className="text-gray-200">
                                    code-server has been installed in
                                    /tmp/code-server
                                </span>
                            </div>
                            <div className="mt-2 flex items-center gap-2">
                                <span className="text-gray-400">💡</span>
                                <span className="text-gray-200">
                                    Running code-server in the background...
                                </span>
                            </div>
                            <div className="mt-1 text-gray-400">
                                Check logs at /tmp/code-server.log!
                            </div>
                        </div>
                    </TerminalBlock>

                    <TerminalBlock title="System Metrics :: Real-time">
                        <div className="space-y-2">
                            <div className="flex justify-between">
                                <span className="text-gray-500">
                                    CPU Cores:
                                </span>
                                <span className="text-gray-200">
                                    0.108 / 126 cores (1%)
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">
                                    RAM Usage:
                                </span>
                                <span className="text-gray-200">
                                    0.148 GiB / 73.1 GiB
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">
                                    Disk Space:
                                </span>
                                <span className="text-gray-200">
                                    1401/3608 GiB (39%)
                                </span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">
                                    Load Average:
                                </span>
                                <span className="text-gray-200">0.01</span>
                            </div>
                            <div className="mt-4 flex justify-between">
                                <span className="text-gray-500">Uptime:</span>
                                <span className="text-gray-200">1ms</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">
                                    Build Time:
                                </span>
                                <span className="text-gray-200">31.38s</span>
                            </div>
                        </div>
                    </TerminalBlock>
                </div>

                {/* Full Width Terminal Block */}
                <TerminalBlock title="Docker Container :: musing_solomon1">
                    <div className="space-y-1 text-xs">
                        <div className="text-gray-300">
                            <span className="font-semibold text-green-400">
                                [INFO]
                            </span>{' '}
                            Container started successfully
                        </div>
                        <div className="text-gray-300">
                            <span className="font-semibold text-green-400">
                                [INFO]
                            </span>{' '}
                            Provisioning Docker containers as Coder workspaces
                        </div>
                        <div className="text-gray-200">
                            <span className="font-semibold text-gray-400">
                                [SYSTEM]
                            </span>{' '}
                            Active version: musing_solomon1 | 7:29:25 PM
                        </div>
                        <div className="text-gray-300">
                            <span className="font-semibold text-green-400">
                                [INFO]
                            </span>{' '}
                            Used by: 0 developers | Build time: Unknown
                        </div>
                        <div className="text-gray-200">
                            <span className="font-semibold text-gray-400">
                                [STATUS]
                            </span>{' '}
                            Last updated: a few seconds ago | Created by:
                            dprevite
                        </div>
                        <div className="mt-3 flex gap-4 border-t border-gray-700/30 pt-2">
                            <span className="font-semibold text-green-400">
                                ● Active
                            </span>
                            <span className="text-gray-400">● Newest</span>
                        </div>
                    </div>
                </TerminalBlock>
            </main>

            {/* Fixed Status Bar */}
            <footer className="border-t border-gray-700/50 bg-black/90 px-6 py-2 shadow-lg shadow-black/20 backdrop-blur-sm">
                <div className="flex items-center justify-between font-mono text-xs">
                    <div className="flex items-center gap-6">
                        <div className="flex items-center gap-2">
                            <div className="h-1.5 w-1.5 animate-pulse rounded-full bg-green-400 shadow-lg shadow-green-400/50"></div>
                            <span className="font-semibold text-green-400">
                                ONLINE
                            </span>
                        </div>
                        <div className="text-gray-500">
                            <span className="text-gray-600">v</span>
                            musing_solomon1
                        </div>
                        <div className="text-gray-500">
                            Transmission:{' '}
                            <span className="text-gray-300">0 B / 0 B</span>
                        </div>
                    </div>

                    <div className="flex items-center gap-6">
                        <div className="text-gray-500">
                            Active Connections:{' '}
                            <span className="text-gray-300">0</span>
                        </div>
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
