import { AlertCircle, CheckCircle, Loader2 } from 'lucide-react';
import React from 'react';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';

interface StatusBadgeProps {
    status: 'running' | 'success' | 'error';
    exitCode?: number;
}

export function StatusBadge({ status, exitCode }: StatusBadgeProps) {
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
            className={`flex items-center gap-1 rounded border px-2 py-0.5 font-mono text-[0.65rem] transition-opacity hover:opacity-80 ${bg} ${text} ${border}`}
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
                    <TooltipTrigger asChild>{button}</TooltipTrigger>
                    <TooltipContent>
                        <p className="font-mono text-xs">
                            Exit code: {exitCode ?? 1}
                        </p>
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
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Exclude this status
                    </button>
                    <button
                        onClick={() => {
                            console.log(`Show only status: ${status}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Show only this status
                    </button>
                </div>
            )}
        </div>
    );
}
