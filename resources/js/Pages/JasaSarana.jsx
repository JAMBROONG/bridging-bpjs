import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function JasaSarana({ auth,data }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Jsa Sarana" />
            <div className="bg-base-200">
                <div className="py-12 grid  grid-cols-1 items-center gap-1  max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className=' w-full'>
                        <div className='p-4 flex'>
                            <div className="flex-1">
                                <h1 className="text-5xl">Jasa Sarana  (JS)</h1>
                                <p className="py-6">Memberikan Solusi Sarana Terbaik untuk Rumah Sakit Anda</p>
                            </div>
                            <div className="flex-1 text-center">
                                <h1 className="text-8xl">{ data[0] && data[0].js ? data[0].js + "%" :"0%" }</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
