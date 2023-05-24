import React, { useState } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import 'react-toastify/dist/ReactToastify.css';
import TableKPI from '@/Components/TableKPI';

export default function JasaPelayanan({ auth, data, data_jp, data_dokter }) {
    const [percentage, setPercentage] = useState('');
    const [percentage2, setPercentage2] = useState('');
    const [tableDokter, setTableDokter] = useState(data_dokter);
    const [isSubmitDisabled, setIsSubmitDisabled] = useState(true);
    const [isModalVisible, setIsModalVisible] = useState(false);
    const [isSubmitDisabled2, setIsSubmitDisabled2] = useState(true);
    const [isModalVisible2, setIsModalVisible2] = useState(false);
    const [dataSet, setData] = useState(data); // Tambahkan state untuk data
    const [dataSet_jp, setData_jp] = useState(data_jp); // Tambahkan state untuk data
    const [namaDokter, setNamaDokter] = useState('');
    const [deleteModalVisible, setDeleteModalVisible] = useState(false);
    const [deleteDokterId, setDeleteDokterId] = useState(null);
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
    const handleModalSubmit = (e) => {
        e.preventDefault();

        axios
            .post('/add-dokter', { nama_dokter: namaDokter })
            .then((response) => {
                setNamaDokter('');
                setIsSubmitDisabled(true);
                setIsModalVisible(false);
                toast.success('Data berhasil ditambahkan', {
                    position: 'top-right',
                    autoClose: 2000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined,
                });

                const newTableDokter = [...tableDokter, response.data.dokter];
                setTableDokter(newTableDokter);
            })
            .catch((error) => {
                console.error(error);
            });
    };

    const handleDeleteModalOpen = (dokterId) => {
        setDeleteModalVisible(true);
        setDeleteDokterId(dokterId);
    };

    const handleDeleteModalClose = () => {
        setDeleteModalVisible(false);
        setDeleteDokterId(null);
    };

    const handleDeleteDokter = () => {
        if (deleteDokterId) {
            axios
                .delete(`/delete-dokter/${deleteDokterId}`)
                .then((response) => {
                    setDeleteModalVisible(false);
                    setDeleteDokterId(null);

                    if (response.status === 200) {
                        setTableDokter(response.data.dokter);
                        toast.success('Data berhasil dihapus', {
                            position: 'top-right',
                            autoClose: 2000,
                            hideProgressBar: false,
                            closeOnClick: true,
                            pauseOnHover: true,
                            draggable: true,
                            progress: undefined,
                        });
                    } else {
                        toast.error('Gagal menghapus data', {
                            position: 'top-right',
                            autoClose: 2000,
                            hideProgressBar: false,
                            closeOnClick: true,
                            pauseOnHover: true,
                            draggable: true,
                            progress: undefined,
                        });
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        }
    };
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Jasa Pelayanan" />
            <div className="bg-base-200">
                <div className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="w-full">
                        <div className="p-4 flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                <h1 className="text-5xl">Dokter (Jasa Langsung)</h1>
                                <p className="py-6">Mengatur Data Dokter</p>
                            </div>
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow text-center">
                                <h1 className="text-8xl">{dataSet_jp[0] && dataSet[0] ? (((dataSet_jp[0].jl/100) * (dataSet[0].jp/100)) * 100).toFixed(0) : '0'}%</h1>
                                <span>dari total pendapatan</span>
                            </div>
                        </div>
                        <div className="p-4  mt-10">
                            <div className="flex justify-end mb-2 animate__animated animate__fadeInUp animate__slow">
                                <label className='btn btn-sm btn-success' htmlFor="modalAddDokter"><i className='fa fa-plus mr-2'></i> Tambah dokter</label>
                            </div>
                            <input type="checkbox" id="modalAddDokter" className="modal-toggle" />
                            <div className="modal">
                                <div className="modal-box relative">
                                    <label htmlFor="modalAddDokter" className="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
                                    <h3 className="text-lg font-bold">Tambah Dokter!</h3>
                                    <form onSubmit={handleModalSubmit}>
                                        <div className="py-4">
                                            <label htmlFor="inputNamaDokter" className="block mb-2">
                                                Nama Dokter
                                            </label>
                                            <input
                                                type="text"
                                                id="inputNamaDokter"
                                                className="input input-bordered w-full"
                                                value={namaDokter}
                                                onChange={(e) => setNamaDokter(e.target.value)}
                                            />
                                        </div>
                                        <div className="mt-6 flex justify-end">
                                            <button
                                                type="submit"
                                                className="btn btn-primary btn-sm"
                                                disabled={!namaDokter}
                                            >
                                                Submit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div className="flex">
                                
                                <div className="flex-1">
                                    <div className="pl-5 overflow-x-auto   animate__animated animate__fadeInUp animate__slow">
                                        <table className='table table-compact w-full'>
                                            <thead>
                                                <tr>
                                                    <th className='bg-base-300'>No. </th>
                                                    <th className='bg-base-300'>Nama Dokter</th>
                                                    <th className='bg-base-300'>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {tableDokter.map((item, index) => (
                                                    <tr key={index}>
                                                        <td>{index + 1}</td>
                                                        <td>{item.nama_dokter}</td>
                                                        <td>
                                                            <button
                                                                className="btn btn-sm btn-error"
                                                                onClick={() => handleDeleteModalOpen(item.id)}
                                                            >
                                                                <i className="fa fa-trash"></i>
                                                            </button>

                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
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
            {deleteModalVisible && (
                <div className="fixed inset-0 flex items-center justify-center z-50">
                    <div className="modal modal-open">
                        <div className="modal-box">
                            <h3 className="text-lg font-bold">Hapus Data Dokter</h3>
                            <p>Apakah Anda yakin ingin menghapus data dokter ini?</p>
                            <div className="modal-action">
                                <button
                                    className="btn btn-error mr-2"
                                    onClick={handleDeleteDokter}
                                >
                                    Hapus
                                </button>
                                <button
                                    className="btn btn-secondary"
                                    onClick={handleDeleteModalClose}
                                >
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
