import { Cpu } from 'lucide-react';
import { AppSidebar } from '@/components/app-sidebar';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';

export default function Runtimes() {
    return (
        <SidebarProvider>
            <AppSidebar />
            <SidebarInset>
                <div className="flex min-h-screen flex-col bg-[#0a0a0a] text-gray-300">
                    <header className="sticky top-0 z-50 border-b border-gray-700/50 bg-black/80 shadow-lg shadow-black/20 backdrop-blur-sm">
                        <div className="flex items-center px-6 py-3">
                            <SidebarTrigger />
                        </div>
                    </header>

                    <main className="flex flex-1 items-center justify-center p-6">
                        <div className="text-center">
                            <div className="mb-4 flex justify-center">
                                <div className="rounded-lg bg-white/5 p-4">
                                    <Cpu className="h-12 w-12 text-gray-400" />
                                </div>
                            </div>
                            <h1 className="mb-2 font-mono text-2xl font-bold text-gray-100">
                                Runtimes
                            </h1>
                            <p className="font-mono text-sm text-gray-400">
                                Coming soon...
                            </p>
                        </div>
                    </main>
                </div>
            </SidebarInset>
        </SidebarProvider>
    );
}
