import React, { useState } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import 'react-toastify/dist/ReactToastify.css';


export default function KpiDokter({ auth, data_kpi, data_dokter }) {
    
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Jasa Pelayanan" />
            <div className="bg-base-200">
                <div className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="w-full">
                        <div className="p-4 flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                <h1 className="text-5xl">Indikator Kinerja Utama (<i>KPI</i>)</h1>
                                <p className="py-6">Nilai terukur yang berfungsi untuk menunjukkan seberapa efektif perusahaan dalam mencapai tujuan bisnis utamanya.</p>
                            </div>
                        </div>
                        <div className="p-4 flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ToastContainer
                position="top-right"
                autoClose={2000}
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
            />
            
        </AuthenticatedLayout>
    );
}
