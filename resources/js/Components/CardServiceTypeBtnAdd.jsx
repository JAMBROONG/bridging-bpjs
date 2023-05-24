import React, { useState } from 'react';
import axios from 'axios';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

import TemplateKelasTarif from './TemplateKelasTarif';

export default function CardServiceTypeBtnAdd({setServiceTypes, data_template }) {

    const [kelasTarif, setKelasTarif] = useState('');
    const [jenisJasa, setJenisJasa] = useState('');
    const [isSubmitDisabled, setIsSubmitDisabled] = useState(true);
    const [isModalVisible, setIsModalVisible] = useState(false);

    const handleKelasTarifChange = (e) => {
        setKelasTarif(e.target.value);
        checkFormValidity(e.target.value, jenisJasa);
    };

    const handleJenisJasaChange = (e) => {
        setJenisJasa(e.target.value);
        checkFormValidity(kelasTarif, e.target.value);
    };

    const checkFormValidity = (kelasTarifValue, jenisJasaValue) => {
        if (kelasTarifValue.trim() !== '' && (jenisJasaValue === 'JS' || jenisJasaValue === 'JP')) {
            setIsSubmitDisabled(false);
        } else {
            setIsSubmitDisabled(true);
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        axios
            .post('/service-types', {
                kelas_tarif: kelasTarif,
                jenis_jasa: jenisJasa
            })
            .then((response) => {
                setKelasTarif('');
                setJenisJasa('');
                setIsSubmitDisabled(true);
                setIsModalVisible(false); // Hide the modal

                axios
                    .get('/service-types')
                    .then((response) => {
                        setServiceTypes(response.data.data);
                    })
                    .catch((error) => {
                        console.error(error);
                    });

                // Show success toast
                toast.success('Data berhasil ditambahkan', {
                    position: 'top-right',
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
        <div className="flex flex-col">
            {/* The button to open modal */}
            <div className="flex justify-between">
                <label htmlFor="modaGetTemplate" className="btn btn-sm btn-warning"><i className='fa fa-download mr-2'></i> Gunakan Template</label>

                {/* Put this part before </body> tag */}
                <input type="checkbox" id="modaGetTemplate" className="modal-toggle" />
                <div className="modal modal-bottom sm:modal-middle">
                    <div className="modal-box">
                        <label htmlFor="modaGetTemplate" className="btn btn-sm btn-circle absolute right-2 top-2" onClick={() => setIsModalVisible(false)}>✕</label>
                        <h3 className="font-bold text-lg">Gunakan Template?</h3>
                        <div className="py-4">
                            <TemplateKelasTarif data_template={data_template} setServiceTypes={setServiceTypes} />
                        </div>
                    </div>
                </div>
                <button className="btn btn-primary btn-sm" onClick={() => setIsModalVisible(true)}>
                    <i className="fa fa-cog mr-2"></i> tambah data
                </button>
                {isModalVisible && (
                    <div className="modal modal-open">
                        <div className="modal-box relative">
                            <label htmlFor="my-modal-3" className="btn btn-sm btn-circle absolute right-2 top-2" onClick={() => setIsModalVisible(false)}>✕</label>
                            <h3 className="text-lg font-bold">Tambah</h3>
                            <form onSubmit={handleSubmit}>
                                <div className="py-4">
                                    <label className="block mb-2">Kelas Tarif</label>
                                    <input
                                        type="text"
                                        value={kelasTarif}
                                        onChange={handleKelasTarifChange}
                                        className="input input-bordered w-full"
                                    />
                                </div>
                                <div className="py-4">
                                    <label className="block mb-2">Jenis Jasa</label>
                                    <select
                                        value={jenisJasa}
                                        onChange={handleJenisJasaChange}
                                        className="select select-bordered w-full"
                                    >
                                        <option value="">Pilih jenis jasa</option>
                                        <option value="JS">JS</option>
                                        <option value="JP">JP</option>
                                    </select>
                                </div>
                                <div className="mt-6 flex justify-end">
                                    <button type="submit" className="btn btn-primary btn-sm" disabled={isSubmitDisabled}>
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
            <ToastContainer
                position="top-right"
                autoClose={5000}
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
                theme="light"
            />
        </div>
    );
}
