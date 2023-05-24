import React, { useState } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import 'react-toastify/dist/ReactToastify.css';

export default function JasaPelayanan({ auth, data }) {
    const [percentage, setPercentage] = useState('');
    const [isSubmitDisabled, setIsSubmitDisabled] = useState(true);
    const [isModalVisible, setIsModalVisible] = useState(false);
    const [dataSet, setData] = useState(data); // Tambahkan state untuk data

    const handlePercentageChange = (e) => {
        const value = e.target.value;

        // Validasi persentase
        if (value === '' || isNaN(value)) {
            setIsSubmitDisabled(true);
        } else {
            const parsedValue = parseInt(value);
            setIsSubmitDisabled(parsedValue < 1 || parsedValue > 100);
        }

        setPercentage(value);
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        // Kirim data ke route (ganti dengan rute yang sesuai)
        axios
            .post('/submit-percentage', { percentage })
            .then((response) => {
                // Tambahkan logika jika submit berhasil
                setPercentage('');
                setIsSubmitDisabled(true);
                setIsModalVisible(false);
                toast.success('Data berhasil diperbarui', {
                    position: 'top-right',
                    autoClose: 2000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                });
                if (response.data.data && response.data.data) {
                    setData([{ jp: response.data.data }]);
                }
            })
            .catch((error) => {
                console.error(error);
            });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Jasa Pelayanan" />
            <div className="bg-base-200">
                <div className="py-12 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="w-full">
                        <div className="p-4 flex">
                            <div className="flex-1">
                                <h1 className="text-5xl">Jasa Pelayanan (JP)</h1>
                                <p className="py-6">Memberikan Solusi Pelayanan Terbaik untuk Rumah Sakit Anda</p>
                                <label htmlFor="modalPercentage" className="btn btn-sm btn-primary">Ubah Persentase</label>
                                <input type="checkbox" id="modalPercentage" className="modal-toggle" />
                                <div className={`modal ${isModalVisible ? 'modal-open' : ''}`}>
                                    <div className="modal-box relative">
                                        <label htmlFor="modalPercentage" className="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
                                        <h3 className="text-lg font-bold">Ubah Persentase JP!</h3>
                                        <form onSubmit={handleSubmit}>
                                            <div className="py-4">
                                                <label htmlFor="inputPercentage" className="block mb-2">Persentase</label>
                                                <label className="input-group">
                                                    <input
                                                        type="number"
                                                        id="inputPercentage"
                                                        placeholder='Rekomendasi: 30 - 50'
                                                        className="input input-bordered  w-full"
                                                        value={percentage}
                                                        onChange={handlePercentageChange}
                                                    />
                                                    <span>%</span>
                                                </label>
                                            </div>
                                            <div className="mt-6 flex justify-end">
                                                <button
                                                    type="submit"
                                                    className="btn btn-primary btn-sm"
                                                    disabled={isSubmitDisabled}
                                                >
                                                    Submit
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div className="flex-1 text-center">
                                <h1 className="text-8xl">{dataSet ? dataSet[0].jp + '%' : '0%'}</h1>
                            </div>
                        </div>

                        <div className="p-4">
                            
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
