import { Book, Github, LayoutDashboard, Code2, Cpu, Activity, FileText, Terminal } from 'lucide-react';
import { Link } from '@inertiajs/react';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

const menuItems = [
    {
        title: 'Dashboard',
        url: '/dashboard',
        icon: LayoutDashboard,
    },
    {
        title: 'Functions',
        url: '/functions',
        icon: Code2,
    },
    {
        title: 'Runtimes',
        url: '/runtimes',
        icon: Cpu,
    },
    {
        title: 'Analytics',
        url: '/analytics',
        icon: Activity,
    },
    {
        title: 'Logs',
        url: '/logs',
        icon: FileText,
    },
];

const footerItems = [
    {
        title: 'GitHub',
        url: 'https://github.com/dprevite/dysfunctional',
        icon: Github,
        external: true,
    },
    {
        title: 'Documentation',
        url: '/documentation',
        icon: Book,
        external: false,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" className="font-mono">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href="/dashboard" className="flex items-center gap-2 font-mono">
                                <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-white/5">
                                    <Terminal className="size-4 text-gray-400" />
                                </div>
                                <div className="flex flex-col gap-0.5 leading-none">
                                    <span className="font-mono font-bold tracking-tight">
                                        <span className="text-gray-400">DYS</span>
                                        <span className="text-gray-200">FUNCTIONAL</span>
                                    </span>
                                </div>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
                <SidebarGroup>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            {menuItems.map((item) => (
                                <SidebarMenuItem key={item.title}>
                                    <SidebarMenuButton asChild>
                                        <Link href={item.url} className="group relative font-mono text-xs text-gray-400 hover:text-white">
                                            <item.icon className="h-3.5 w-3.5" />
                                            <span className="relative inline-flex items-center">
                                                {item.title}
                                                <span className="absolute left-full ml-0 hidden h-0.5 w-1.5 translate-y-1 bg-gray-500/60 group-hover:inline-block group-hover:animate-blink"></span>
                                            </span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            ))}
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>
            <SidebarFooter>
                <SidebarMenu>
                    {footerItems.map((item) => (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton asChild size="sm">
                                {item.external ? (
                                    <a href={item.url} target="_blank" rel="noopener noreferrer" className="font-mono text-gray-500 hover:text-gray-400">
                                        <item.icon className="h-3 w-3" />
                                        <span className="text-xs">{item.title}</span>
                                    </a>
                                ) : (
                                    <Link href={item.url} className="font-mono text-gray-500 hover:text-gray-400">
                                        <item.icon className="h-3 w-3" />
                                        <span className="text-xs">{item.title}</span>
                                    </Link>
                                )}
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                </SidebarMenu>
            </SidebarFooter>
        </Sidebar>
    );
}
