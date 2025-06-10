import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { CheckCircle2, Pencil, Plus, Search, Trash2, XCircle } from 'lucide-react';
import { useEffect, useState } from 'react';

interface List {
    id: number;
    title: string;
    description: string | null;
    tasks_count?: number;
}

interface Props {
    lists: List[];
    filters: {
        search: string;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Make Lists',
        href: '/lists',
    },
];

export default function ListsIndex({ lists, filters, flash }: Props) {
    const [showToast, setShowToast] = useState(false);
    const [toastMessage, setToastMessage] = useState('');
    const [searchTerm, setSearchTerm] = useState(filters.search);
    const [toastType, setToastType] = useState<'success' | 'error'>('success');

    useEffect(() => {
        if (flash?.success || flash?.error) {
            setToastMessage(flash.success || flash.error || '');
            setToastType(flash.success ? 'success' : 'error');
            setShowToast(true);
        }
    }, [flash]);

    useEffect(() => {
        if (showToast) {
            const timer = setTimeout(() => setShowToast(false), 3000);
            return () => clearTimeout(timer);
        }
    }, [showToast]);

    useEffect(() => {
        const handler = setTimeout(() => {
            router.get(
                route('lists.index'),
                {
                    search: searchTerm,
                },
                {
                    preserveState: true,  
                    preserveScroll: true,
                    replace: true,       
                },
            );
        }, 300);


        return () => {
            clearTimeout(handler);
        };
    }, [searchTerm]); 

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Lists" />
            <div className="flex flex-col gap-4 p-4">
                {/* Toast Notification */}
                {showToast && (
                    <div
                        className={`fixed top-4 right-4 z-50 flex items-center gap-2 rounded-lg p-4 shadow-lg text-white ${
                            toastType === 'success' ? 'bg-green-500' : 'bg-red-500'
                        } animate-in fade-in slide-in-from-top-5`}
                    >
                        {toastType === 'success' ? <CheckCircle2 className="h-5 w-5" /> : <XCircle className="h-5 w-5" />}
                        <span>{toastMessage}</span>
                    </div>
                )}

                <h1 className="text-2xl font-bold">Lists</h1>


                <div className="mb-4 flex gap-4">
                    <div className="relative flex-1"> 
                        <Search className="text-muted-foreground absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform" />
                        <Input
                            placeholder="Search list..."
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            className="pl-10"
                        />
                    </div>
                </div>

                {/* List Cards */}
                <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    {lists.map((list) => (
                        <Card key={list.id} className="hover:bg-accent/50 transition-colors">
                            <CardHeader className="pb-2">
                                <CardTitle className="text-lg font-medium">{list.title}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-muted-foreground text-sm">{list.description || 'No description'}</p>
                                {list.tasks_count !== undefined && (
                                    <p className="text-muted-foreground mt-2 text-sm">{list.tasks_count} Tasks</p>
                                )}
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </AppLayout>
    );
}