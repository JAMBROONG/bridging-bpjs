import TableInvoice from '@/Components/TableInvoice';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Dashboard" />
            <div className="bg-base-200">
                <div className="py-12 grid  grid-cols-1 items-center gap-1 md:grid-cols-2 md:gap-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div>
                        <div className='p-4'>
                            <h1 className="text-5xl ">Bridging BPJS</h1>
                            <p className="py-6">Cocok untuk Klinik, Rumah Sakit, Puskesmas, Apotek, Laboratorium dan Klinik Kecantikan
                                Mudah, lengkap dan terintegrasi</p>
                            <label className="btn btn-primary" htmlFor="my-modal-3">Get Started</label>
                        </div>
                    </div>
                    <div className="card w-100  image-full p-3">
                        <figure style={{ aspectRatio: '2/1' }}>
                            <img
                                src="https://demos.creative-tim.com/soft-ui-dashboard-react/static/media/ivancik.442b474727c414bb3b85.jpg"
                                alt="Shoes"
                                style={{ objectFit: 'cover', objectPosition: 'top center', width: '100%', height: '100%' }}
                            />
                        </figure>
                        <div className="card-body">
                            <h2 className="card-title text-bold">Work with the rocket</h2>
                            <p>Solusi digital untuk meningkatkan
                                efisiensi fasilitas kesehatan</p>
                        </div>
                    </div>
                </div>

                <input type="checkbox" id="my-modal-3" className="modal-toggle" />
                <div className="modal">
                    <div className="modal-box relative">
                        <label htmlFor="my-modal-3" className="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
                        <h3 className="text-lg font-bold">Congratulations random Internet user!</h3>
                        <p className="py-4">You've been selected for a chance to get one year of subscription to use Wikipedia for free!</p>
                    </div>
                </div>
                <div className="py-12 bg-current">
                    <div className="grid  grid-cols-1 items-center gap-1 md:grid-cols-2 md:gap-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="">
                            <div className="alert shadow-lg rounded p-2">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" className="stroke-current flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span>Invoices</span>
                                </div>
                            </div>
                            <TableInvoice />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
