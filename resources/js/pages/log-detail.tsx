import { FileText } from 'lucide-react';
import { AppSidebar } from '@/components/app-sidebar';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';

interface LogDetailProps {
    logId: string;
}

export default function LogDetail({ logId }: LogDetailProps) {
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
                                    <FileText className="h-12 w-12 text-gray-400" />
                                </div>
                            </div>
                            <h1 className="mb-2 font-mono text-2xl font-bold text-gray-100">
                                Log Detail
                            </h1>
                            <p className="font-mono text-sm text-gray-400">
                                Log ID: {logId}
                            </p>
                            <p className="mt-2 font-mono text-sm text-gray-500">
                                Coming soon...
                            </p>
                        </div>
                    </main>
                </div>
            </SidebarInset>
        </SidebarProvider>
    );
}
