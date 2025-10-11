import { Clock } from 'lucide-react';
import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Bar,
    BarChart,
    CartesianGrid,
    ResponsiveContainer,
    Tooltip as RechartsTooltip,
    XAxis,
    YAxis,
} from 'recharts';

interface ChartDataPoint {
    time: string;
    success: number;
    errors: number;
}

interface FunctionCallsChartProps {
    data24h: ChartDataPoint[];
    data7d: ChartDataPoint[];
    data30d: ChartDataPoint[];
}

export function FunctionCallsChart({
    data24h,
    data7d,
    data30d,
}: FunctionCallsChartProps) {
    const [timeframe, setTimeframe] = React.useState<'24h' | '7d' | '30d'>(
        '24h',
    );
    const [dropdownOpen, setDropdownOpen] = React.useState(false);
    const dropdownRef = React.useRef<HTMLDivElement>(null);

    React.useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (
                dropdownRef.current &&
                !dropdownRef.current.contains(event.target as Node)
            ) {
                setDropdownOpen(false);
            }
        };
        if (dropdownOpen) {
            document.addEventListener('mousedown', handleClickOutside);
        }
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [dropdownOpen]);

    const chartData =
        timeframe === '24h' ? data24h : timeframe === '7d' ? data7d : data30d;

    const timeframeLabels = {
        '24h': 'Last 24 Hours',
        '7d': 'Last 7 Days',
        '30d': 'Last 30 Days',
    };

    return (
        <div className="rounded-md border border-gray-700/50 bg-black/50 p-4 shadow-md shadow-black/20">
            <div className="mb-4 flex items-center justify-between">
                <div>
                    <h3 className="font-mono text-sm font-semibold text-white">
                        Function Executions
                    </h3>
                    <p className="font-mono text-xs text-gray-500">
                        Success vs errors over time
                    </p>
                </div>
                <div ref={dropdownRef} className="relative">
                    <button
                        onClick={() => setDropdownOpen(!dropdownOpen)}
                        className="flex items-center gap-1 rounded border border-gray-600/30 bg-gray-600/10 px-2 py-0.5 font-mono text-[0.65rem] text-gray-400 transition-colors hover:bg-gray-600/20 hover:text-gray-300"
                    >
                        <Clock className="h-2.5 w-2.5 shrink-0" />
                        {timeframeLabels[timeframe]}
                    </button>
                    {dropdownOpen && (
                        <div className="absolute right-0 top-full z-10 mt-1 w-40 rounded-md border border-gray-700/50 bg-black/95 shadow-lg shadow-black/50 backdrop-blur-sm">
                            <Link
                                href="/dashboard?timeframe=24h"
                                onClick={() => setDropdownOpen(false)}
                                className={`flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] transition-colors hover:bg-white/5 hover:text-white ${timeframe === '24h' ? 'text-white' : 'text-gray-300'}`}
                                preserveScroll
                            >
                                Last 24 Hours
                            </Link>
                            <Link
                                href="/dashboard?timeframe=7d"
                                onClick={() => setDropdownOpen(false)}
                                className={`flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] transition-colors hover:bg-white/5 hover:text-white ${timeframe === '7d' ? 'text-white' : 'text-gray-300'}`}
                                preserveScroll
                            >
                                Last 7 Days
                            </Link>
                            <Link
                                href="/dashboard?timeframe=30d"
                                onClick={() => setDropdownOpen(false)}
                                className={`flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] transition-colors hover:bg-white/5 hover:text-white ${timeframe === '30d' ? 'text-white' : 'text-gray-300'}`}
                                preserveScroll
                            >
                                Last 30 Days
                            </Link>
                        </div>
                    )}
                </div>
            </div>
            <ResponsiveContainer width="100%" height={200}>
                <BarChart
                    data={chartData}
                    margin={{ top: 5, right: 5, bottom: 5, left: 5 }}
                >
                    <CartesianGrid
                        strokeDasharray="2 2"
                        stroke="#374151"
                        opacity={0.5}
                        strokeWidth={1}
                    />
                    <XAxis
                        dataKey="time"
                        stroke="#6B7280"
                        style={{ fontSize: '10px', fontFamily: 'monospace' }}
                        tickLine={false}
                        axisLine={{ stroke: '#4B5563', strokeWidth: 1 }}
                    />
                    <YAxis
                        stroke="#6B7280"
                        style={{ fontSize: '10px', fontFamily: 'monospace' }}
                        tickLine={false}
                        axisLine={false}
                        width={30}
                    />
                    <RechartsTooltip
                        contentStyle={{
                            backgroundColor: 'rgba(0, 0, 0, 0.95)',
                            border: '1px solid rgba(107, 114, 128, 0.5)',
                            borderRadius: '0.375rem',
                            fontFamily: 'monospace',
                            fontSize: '11px',
                            padding: '8px 12px',
                        }}
                        labelStyle={{ color: '#9CA3AF', marginBottom: '4px' }}
                        itemStyle={{ color: '#E5E7EB' }}
                        cursor={{ fill: 'rgba(255, 255, 255, 0.05)' }}
                    />
                    <Bar
                        dataKey="errors"
                        stackId="a"
                        fill="#EF4444"
                        opacity={0.7}
                        radius={0}
                        stroke="#DC2626"
                        strokeWidth={1}
                        name="Errors"
                    />
                    <Bar
                        dataKey="success"
                        stackId="a"
                        fill="#10B981"
                        opacity={0.7}
                        radius={0}
                        stroke="#059669"
                        strokeWidth={1}
                        name="Success"
                    />
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}
