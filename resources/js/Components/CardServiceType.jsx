import React, { useState, useEffect } from 'react';
import CardServiceTypeBtnAdd from './CardServiceTypeBtnAdd';
import axios from 'axios';
import { toast, ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';


export default function CardServiceType({ data, data_template }) {
    const [deleteConfirmation, setDeleteConfirmation] = useState('');
    const [serviceTypes, setServiceTypes] = useState(data);

    useEffect(() => {
        setServiceTypes(data);
    }, [data]);

    const handleDeleteConfirmation = (index) => {
        setDeleteConfirmation(index);
    };

    const handleDelete = (id) => {
        axios
            .delete(`/service-types/${id}`)
            .then((response) => {
                setDeleteConfirmation('');
                // Mengupdate state serviceTypes dengan data terbaru dari respons
                setServiceTypes(response.data.data);
                toast.success('Data berhasil dihapus', {
                    position: 'bottom-right',
                    autoClose: 2000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                });
            })
            .catch((error) => {
                console.error(error);
            });
    };

    return (
        <div className="">
            <CardServiceTypeBtnAdd data={data}  data_template={data_template} setServiceTypes={setServiceTypes} />
            <ToastContainer position="top-right" autoClose={5000} hideProgressBar newestOnTop={false} closeOnClick rtl={false} pauseOnFocusLoss draggable pauseOnHover />
            <div className="mb-3 overflow-x-auto shadow-md rounded p-3">
                <div className="rounded p-2 bg-base-300 mb-2 text-center">
                    <span>Jenis Jasa Anda</span>
                </div>
                <table className="table table-compact w-full">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kelas Tarif</th>
                            <th>Jenis Jasa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {serviceTypes.map((item, index) => (
                            <tr key={index}>
                                <td>{index + 1}</td>
                                <td>{item.kelas_tarif}</td>
                                <td>{item.jenis_jasa}</td>
                                <td className='flex'>
                                    <button
                                        className='btn btn-sm btn-error mr-2'
                                        onClick={() => handleDeleteConfirmation(index)}
                                    >
                                        <i className='fa fa-trash'></i>
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
            {deleteConfirmation !== '' && (
                <div className="fixed inset-0 z-50 flex items-center justify-center">
                    <div className="modal-box relative bg-base-100">
                        <button
                            className="btn btn-sm btn-circle absolute right-2 top-2"
                            onClick={() => setDeleteConfirmation('')}
                        >
                            ✕
                        </button>
                        <h3 className="text-lg font-bold">Konfirmasi Hapus</h3>
                        <p>Anda yakin ingin menghapus data ini?</p>
                        <div className="modal-action">
                            <button
                                className="btn btn-error"
                                onClick={() => handleDelete(serviceTypes[deleteConfirmation].id)}
                            >
                                Ya
                            </button>
                            <button
                                className="btn btn-primary"
                                onClick={() => setDeleteConfirmation('')}
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
