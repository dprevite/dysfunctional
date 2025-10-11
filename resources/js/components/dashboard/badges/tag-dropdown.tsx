import { Code2, Cpu } from 'lucide-react';
import React from 'react';

interface TagDropdownProps {
    label: string;
    type: 'function' | 'runtime' | 'status';
}

export function TagDropdown({ label, type }: TagDropdownProps) {
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
                className="flex items-center gap-1 rounded border border-gray-600/30 bg-gray-600/10 px-2 py-0.5 font-mono text-[0.65rem] text-gray-400 transition-colors hover:bg-gray-600/20 hover:text-gray-300"
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
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Exclude this {type}
                    </button>
                    <button
                        onClick={() => {
                            console.log(`Show only ${type}: ${label}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        Show only this {type}
                    </button>
                    <div className="my-1 border-t border-gray-700/50"></div>
                    <button
                        onClick={() => {
                            console.log(`View ${type} details: ${label}`);
                            setOpen(false);
                        }}
                        className="flex w-full items-center gap-2 px-3 py-0.5 text-left font-mono text-[0.65rem] text-gray-300 transition-colors hover:bg-white/5 hover:text-white"
                    >
                        View {type} details
                    </button>
                </div>
            )}
        </div>
    );
}
