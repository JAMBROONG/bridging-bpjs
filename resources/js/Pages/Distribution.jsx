import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Distribution" />
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <h1 className="text-5xl font-bold text-center mb-5">Distribution</h1>
                    <div className="card bg-base-100 shadow-sm">
                        <div className="card-body">
                            <form action="" method="post">
                                <div className="form-group">
                                    <label htmlFor="">
                                        <span className="text-xl font-semibold">Pilih Layanan</span>
                                    </label>
                                    <br />
                                    <select className="select select-bordered w-full max-w-xs">
                                        <option disabled selected>Pilih Layanan</option>
                                        <option>Rawat Inap</option>
                                        <option>Rawat Jalan</option>
                                    </select>
                                </div>
                                <div className="grid grid-cols-2 gap-2">
                                    <div>
                                        <div className="form-group mt-3">
                                            <label htmlFor="">
                                                <span className="text-xl font-semibold">File BPJS (Excel)</span>
                                            </label>
                                            <br />
                                            <input type="file" className="file-input file-input-bordered w-full max-w-xs" />
                                        </div>
                                    </div>
                                    <div>
                                        <div className="form-group mt-3">
                                            <label htmlFor="">
                                                <span className="text-xl font-semibold">File BPJS (Excel)</span>
                                            </label>
                                            <br />
                                            <input type="file" className="file-input file-input-bordered w-full max-w-xs" />
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" className='btn btn-primary btn-sm mt-3'>
                                    Submit
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
