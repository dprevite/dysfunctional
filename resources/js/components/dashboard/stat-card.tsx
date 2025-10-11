import type { LucideIcon } from 'lucide-react';

interface StatCardProps {
    icon: LucideIcon;
    label: string;
    value: number | string;
    status?: 'online' | 'offline';
}

export function StatCard({ icon: Icon, label, value, status }: StatCardProps) {
    return (
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
}
