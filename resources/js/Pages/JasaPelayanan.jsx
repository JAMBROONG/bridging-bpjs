import React, { useState } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import 'react-toastify/dist/ReactToastify.css';

export default function JasaPelayanan({ auth, data, data_jp }) {
    const [percentage, setPercentage] = useState('');
    const [percentage2, setPercentage2] = useState('');
    const [isSubmitDisabled, setIsSubmitDisabled] = useState(true);
    const [isModalVisible, setIsModalVisible] = useState(false);
    const [isSubmitDisabled2, setIsSubmitDisabled2] = useState(true);
    const [isModalVisible2, setIsModalVisible2] = useState(false);
    const [dataSet, setData] = useState(data); // Tambahkan state untuk data
    const [dataSet_jp, setData_jp] = useState(data_jp); // Tambahkan state untuk data
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
    const handlePercentageChange2 = (e) => {
        const value = e.target.value;
        // Validasi persentase
        if (value === '' || isNaN(value)) {
            setIsSubmitDisabled2(true);
        } else {
            const parsedValue = parseInt(value);
            setIsSubmitDisabled2(parsedValue < 1 || parsedValue > 100);
        }

        setPercentage2(value);
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


    const handleSubmit2 = (e) => {
        e.preventDefault();

        // Kirim data ke route (ganti dengan rute yang sesuai)
        axios
            .post('/submit-percentage-jl', { percentage: percentage2 })
            .then((response) => {
                // Tambahkan logika jika submit berhasil
                setPercentage2('');
                setIsSubmitDisabled2(true);
                setIsModalVisible2(false);
                toast.success('Data berhasil diperbarui', {
                    position: 'top-right',
                    autoClose: 2000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                });
                if (response.data.jl) {
                    setData_jp([{ jl: response.data.jl, jtl: response.data.jtl }]);
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
                <div className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="w-full">
                        <div className="p-4 flex">
                            <div className="flex-1 ">
                                <h1 className="text-5xl animate__animated animate__fadeInUp animate__slow">Jasa Pelayanan (JP)</h1>
                                <p className="py-6 animate__animated animate__fadeInUp animate__slow">Memberikan Solusi Pelayanan Terbaik untuk Rumah Sakit Anda</p>
                                <label htmlFor="modalPercentage" className="btn btn-sm btn-primary animate__animated animate__fadeInUp animate__slow">Ubah Persentase</label>
                                <input type="checkbox" id="modalPercentage" className="modal-toggle" />
                                <div className={`modal ${isModalVisible ? 'modal-open' : ''}`} style={{ zIndex: 100 }}>
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
                                                        placeholder='1 - 100'
                                                        className="input input-bordered  w-full"
                                                        value={percentage}
                                                        onChange={handlePercentageChange}
                                                    />
                                                    <span>%</span>
                                                </label>
                                                <small><b className='text-error'>*</b> Peraturan menteri kesehatan 28/2014: <b>30% - 50%</b></small>
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
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow text-center">
                                <h1 className="text-8xl">{dataSet[0] ? dataSet[0].jp + '%' : '0%'}</h1>
                            </div>
                        </div>
                        <div className="p-4  mt-10">
                            <div className="flex">
                                <div className="flex-1">
                                    <div className="flex flex-col w-full lg:flex-row">
                                        <div className="m-1 grid flex-grow flex-1 card rounded-box place-items-center">
                                            <div className="stats shadow  w-full  animate__animated animate__fadeInUp animate__slow">
                                                <div className="stat">
                                                    <div className="stat-figure text-secondary">
                                                        <div className="avatar online">
                                                            <div className="w-16 rounded-full">
                                                                <img src="https://img.freepik.com/free-vector/doctor-character-background_1270-84.jpg?size=626&ext=jpg&ga=GA1.2.1030933719.1684728785&semt=sph" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="stat-value">{dataSet_jp[0] ? dataSet_jp[0].jl + '%' : '0%'}</div>
                                                    <div className="stat-title">Jasa Langsung</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="m-1 grid flex-grow  flex-1 card rounded-box place-items-center">
                                            <div className="stats shadow  w-full  animate__animated animate__fadeInUp animate__slow">
                                                <div className="stat">
                                                    <div className="stat-figure text-secondary">
                                                        <div className="avatar online">
                                                            <div className="w-16 rounded-full">
                                                                <img src="https://img.freepik.com/free-vector/meeting-business-people-avatar-character_24877-57276.jpg?w=1380&t=st=1684894493~exp=1684895093~hmac=ac06d737242b641d228ca8f767b02d95e42f88e8e6e390a74dd560619ad6a5d3" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div className="stat-value">{dataSet_jp[0] ? dataSet_jp[0].jtl + '%' : '0%'}</div>
                                                    <div className="stat-title">Jasa Tidak Langsung</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <label htmlFor="modalPercentageJl" className="btn btn-sm btn-primary mt-3  animate__animated animate__fadeInUp animate__slow">Ubah Persentase</label>
                                    <input type="checkbox" id="modalPercentageJl" className="modal-toggle" />
                                    <div className={`modal ${isModalVisible2 ? 'modal-open' : ''}`}>
                                        <div className="modal-box relative">
                                            <label htmlFor="modalPercentageJl" className="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
                                            <h3 className="text-lg font-bold">Ubah Persentase JL!</h3>
                                            <form onSubmit={handleSubmit2}>
                                                <div className="py-4">
                                                    <label htmlFor="inputPercentageJl" className="block mb-2">PersentaseJasa Langsung (JL)</label>
                                                    <label className="input-group">
                                                        <input
                                                            type="number"
                                                            id="inputPercentageJl"
                                                            placeholder='1 - 100'
                                                            className="input input-bordered  w-full"
                                                            value={percentage2}
                                                            onChange={handlePercentageChange2}
                                                        />
                                                        <span>%</span>
                                                    </label>
                                                    <small><b className='text-error'>*</b> Jasa Tidak Langsung (JTL) = 100 - Jasa Langsung(JL)</small>
                                                </div>
                                                <div className="mt-6 flex justify-end">
                                                    <button
                                                        type="submit"
                                                        className="btn btn-primary btn-sm"
                                                        disabled={isSubmitDisabled2}
                                                    >
                                                        Submit
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
