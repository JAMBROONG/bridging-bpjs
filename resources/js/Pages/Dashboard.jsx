import TableInvoice from '@/Components/TableInvoice';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({  auth, data, data_jp, data_dokter }) {
    console.log(data);
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Dashboard" />
            <div className="bg-base-200">
                <div className="py-12 grid  grid-cols-1 items-center gap-1 md:grid-cols-2 md:gap-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className='animate__animated animate__fadeInUp animate__slow'>
                        <div className='p-4'>
                            <h1 className="text-5xl ">Bridging BPJS</h1>
                            <p className="py-6">Cocok untuk Klinik, Rumah Sakit, Puskesmas, Apotek, Laboratorium dan Klinik Kecantikan
                                Mudah, lengkap dan terintegrasi</p>
                            <label className="btn btn-primary" htmlFor="my-modal-3">Get Started</label>
                        </div>
                    </div>
                    <div className="card w-100  image-full p-3 animate__animated animate__fadeInUp animate__slow">
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
                        <h3 className="text-lg font-bold">Mau pakai metode apa hari ini? <i className='fas fa-smile'></i> </h3>
                        <div className="py-4">
                            <div className="flex">
                                <div className="flex-1 p-2 pb-0 pl-0">
                                    <a href={route('shifting')} className="btn btn-block btn-primary">Shifting</a>
                                </div>
                                <div className="flex-1 p-2 pb-0 pr-0">
                                    <a href={route('distribution')} className="btn btn-block btn-primary">Distribution</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="py-12">
                    <div className="grid  grid-cols-1 items-start gap-1 md:grid-cols-2 md:gap-2 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="animate__animated animate__fadeInUp animate__slow">
                            <div className="alert shadow-sm rounded p-2  bg-base-100">
                                <div>
                                    <i className="fa-solid fa-file-invoice-dollar"/>
                                    <span>Invoices</span>
                                </div>
                            </div>
                            <TableInvoice />
                        </div>
                        <div className="flex justify-around items-start animate__animated animate__fadeInUp animate__slow">
                            <div className="stats  shadow-sm  w-full m-3 mt-0">
                                <div className="stat">
                                    <div className="stat-figure text-secondary">
                                        <div className="avatar online">
                                            <div className="w-16 rounded-full">
                                                <img src="https://img.freepik.com/free-vector/ambulance-flat-style_23-2147958337.jpg?w=826&t=st=1684867317~exp=1684867917~hmac=dc7d955f232c6bfe2251b24df009d7a8fc2c1ff87f08f73c5143db0a56b7a266" />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="stat-value">{ data[0] && data[0].js ? data[0].js : 0}%</div>
                                    <div className="stat-title">Jasa Sarana</div>
                                </div>
                            </div>
                            <div className="stats  shadow-sm w-full m-3 mt-0">
                                <div className="stat">
                                    <div className="stat-figure text-secondary">
                                        <div className="avatar online">
                                            <div className="w-16 rounded-full">
                                                <img src="https://img.freepik.com/free-photo/doctor-man-consulting-patient-while-filling-up-application-form-desk-hospital_1150-12966.jpg?w=1380&t=st=1684866558~exp=1684867158~hmac=661f3a0c1bbfec6c1e9436d72fc04216edc93040b5eecf941bae310c61ca4fd2" />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="stat-value">{data[0] && data[0].jp? data[0].jp : 0}%</div>
                                    <div className="stat-title">Jasa Pelayanan</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
