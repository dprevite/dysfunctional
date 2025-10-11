import { CalendarClock } from 'lucide-react';
import React from 'react';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';

interface ScheduledBadgeProps {
    cron: string;
    description: string;
}

export function ScheduledBadge({ cron, description }: ScheduledBadgeProps) {
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

    const button = (
        <button
            onClick={() => setOpen(!open)}
            className="flex items-center gap-1 rounded border border-yellow-300/30 bg-yellow-300/10 px-2 py-0.5 font-mono text-[0.65rem] text-yellow-200 transition-colors hover:bg-yellow-300/20 hover:text-yellow-100"
        >
            <CalendarClock className="h-2.5 w-2.5 shrink-0" />
            Scheduled
        </button>
    );

    return (
        <div ref={dropdownRef} className="relative">
            <Tooltip>
                <TooltipTrigger asChild>{button}</TooltipTrigger>
                <TooltipContent>
                    <p className="font-mono text-xs">{cron}</p>
                    <p className="font-mono text-xs text-gray-400">
                        {description}
                    </p>
                </TooltipContent>
            </Tooltip>

            {open && (
                <div className="absolute top-full left-0 z-10 mt-1 w-56 rounded-md border border-gray-700/50 bg-black/95 shadow-lg shadow-black/50 backdrop-blur-sm">
                    <button
                        onClick={() => {
                            console.log(`Exclude scheduled jobs`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Exclude scheduled jobs
                    </button>
                    <button
                        onClick={() => {
                            console.log(`Show only scheduled jobs`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Show only scheduled jobs
                    </button>
                    <div className="my-1 border-t border-gray-700/50"></div>
                    <button
                        onClick={() => {
                            console.log(`View schedule details`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        View schedule details
                    </button>
                </div>
            )}
        </div>
    );
}
