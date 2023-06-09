import React, {useState} from 'react';
import {ToastContainer, toast} from 'react-toastify';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, Link} from '@inertiajs/react';

import 'react-toastify/dist/ReactToastify.css';

export default function KpiDokter({auth, data_kpi, dataKategori, kpi_kategori, kpi_dokter}) {

    console.log(kpi_dokter);
    const [kpi_dokter2,
        setkpi_dokter2] = useState(kpi_dokter);
    const [dataKategori2,
        setdataKategori2] = useState(dataKategori);
    const [kpi_kategori2,
        setkpi_kategori2] = useState(kpi_kategori);
    const [showModalAdd,
        setShowModalAdd] = useState(false);
    const [selectedVendor,
        setSelectedVendor] = useState(1);
    const [kelompok,
        setKelompok] = useState('');
    const [nilai,
        setNilai] = useState('');
    const [editVendorId,
        setEditVendorId] = useState(null);
    const [editVendorName,
        setEditVendorName] = useState('');
    const [editVendorKategori,
        setEditVendorKategori] = useState('');
    const [EditKPIId,
        setEditKPIId] = useState(null);
    const [EditKPIKelompok,
        setEditKPIKelompok] = useState('');
    const [EditKPINilai,
        setEditKPINilai] = useState('');

    const handleDeleteKPI = (id) => {
        if (window.confirm('Anda yakin ingin menghapus vendor ini?')) {
            axios
                .post('/kpi-delete', {id})
                .then((response) => {
                    const updatedKpiKategori = (response.data.kpi_dokter);
                    setkpi_dokter2(updatedKpiKategori);
                    toast.success('data KPI berhasil dihapus');
                })
                .catch((error) => {
                    console.error(error);
                    toast.error('Gagal menghapus data KPI');
                });
        }
    };
    const handleEditVendor = () => {
        if (editVendorName) {
            axios
                .post('/kpi-kategori-update', {
                id: editVendorId,
                bobot: editVendorName
            })
                .then((response) => {
                    setdataKategori2(response.data.dataKategori);
                    setEditVendorId(null);
                    setEditVendorName('');
                    setEditVendorKategori('');
                    toast.success('Indikator berhasil diperbarui');
                })
                .catch((error) => {
                    console.error(error);
                    toast.error('Gagal memperbarui Indikator');
                });
        } else {
            toast.error('Nama indikator harus diisi');
        }
    };
    const handleEditKPI = () => {
        if (EditKPIKelompok && EditKPINilai) {
            axios
                .post('/kpi-update', {
                id: EditKPIId,
                kelompok: EditKPIKelompok,
                nilai: EditKPINilai
            })
                .then((response) => {
                    const updatedKpiKategori = (response.data.kpi_dokter);
                    setkpi_dokter2(updatedKpiKategori);
                    setEditKPIId(null);
                    setEditKPIKelompok('');
                    setEditKPINilai('');
                    toast.success('Data KPI berhasil diperbarui');
                })
                .catch((error) => {
                    console.error(error);
                    toast.error('Gagal memperbarui data KPI');
                });
        } else {
            toast.error('Kelompok atau Nilai indikator harus diisi');
        }
    };

    const handleSumbmitKPI = () => {
        if (kelompok.trim() === '' || nilai.trim() === '') {
            toast.error('Mohon lengkapi semua input');
            return;
        }
        if (nilai.trim() > 100 || nilai.trim() < 0) {
            toast.error('Mohon isikan nilai 1 - 100');
            return;
        }

        const newKPI = {
            kategori: selectedVendor,
            kelompok: kelompok,
            nilai: nilai
        };

        axios
            .post('/kpi-add', newKPI)
            .then((response) => {
                const updatedKpiKategori = (response.data.kpi_dokter);
                setkpi_dokter2(updatedKpiKategori);
                setKelompok("");
                setNilai("");
                toast.success('Data berhasil ditambahkan');
            })
            .catch((error) => {
                console.error(error);
                toast.error('Gagal menambahkan data');
            });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="KPI Dashboard"/>
            <div className="bg-base-200">
                <div
                    className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="w-full">
                        <div className="p-4 flex items-center flex-col lg:flex-row">
                            <div
                                className="flex-1 animate__animated animate__fadeInUp animate__slow text-center lg:text-left">
                                <h1 className="text-5xl">Indikator Kinerja Utama (<i>KPI</i>)</h1>
                                <p className="py-6">
                                    Nilai terukur yang berfungsi untuk menunjukkan seberapa efektif rumah sakit
                                    dalam mencapai tujuan utamanya.
                                </p>
                                <button
                                    className="btn btn-primary"
                                    onClick={() => {
                                    setShowModalAdd(true);
                                }}>
                                    Tambah Data
                                </button>
                            </div>
                            <div className="flex-1 w-full lg:mt-0 mt-5">
                                <div className="overflow-x-auto flex-1  shadow w-full">
                                    <table className="table w-full">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Indikator Kinerja Utama</th>
                                                <th>Bobot</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        {dataKategori2 <= 0
                                            ? (
                                                <tbody>
                                                    <tr>
                                                        <td colSpan={4} className='text-center py-5'>
                                                            <small className='text-error'>*Data belum tersedia, silahkan untuk membuat template terlebih dahulu!</small>
                                                            <br/>
                                                            <Link href={route('getTemplateKPI')} className="p-2 btn btn-sm btn-warning m-2">
                                                                buat template
                                                                <i class="fa-regular fa-file-lines ml-2"></i>
                                                            </Link>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            )
                                            : (
                                                <tbody>{dataKategori2.map((vendor, index) => (
                                                        <tr key={index}>
                                                            <th className="text-center">{index + 1}</th>
                                                            <td>{vendor.dataKategori
                                                                    ? vendor.dataKategori
                                                                    : '-'}</td>
                                                            <td>{vendor.bobot}</td>
                                                            <td>
                                                                <button
                                                                    className='p-1 bg-info rounded text-white'
                                                                    onClick={() => {
                                                                    setEditVendorId(vendor.id);
                                                                    setEditVendorName(vendor.bobot);
                                                                    setEditVendorKategori(vendor.dataKategori);
                                                                }}>
                                                                    <i className="fa fa-edit mr-2"></i>
                                                                    bobot
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    ))
}
                                                </tbody>
                                            )}

                                    </table>
                                </div>
                            </div>
                        </div>

                        <div className="w-full p-4 rounded flex  flex-col">
                            <div className="shadow pt-2 w-full">
                                <label htmlFor="" className='p-3 pt-1 bg-base-100 rounded'>
                                    <b className='text-current'>1. Indeks Dasar (Basic Index)</b>
                                </label>
                                <table className="table w-full">
                                    <thead>
                                        <tr>
                                            <th className='bg-base-300'>Kelompok</th>
                                            <th className='bg-base-300'>Nilai</th>
                                            <th className='bg-base-300'>Bobot</th>
                                            <th className='bg-base-300'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {kpi_dokter2.map((item, index) => {
                                            if (item.kategori_id === 1) {
                                                return (
                                                    <tr key={index}>
                                                        <td className="whitespace-pre-wrap">{item.kelompok}</td>
                                                        <td>{item.nilai}</td>
                                                        <td>{dataKategori2[0]['bobot']}</td>
                                                        <td>
                                                            <button onClick={() => handleDeleteKPI(item.id)}>
                                                                <i className='fa fa-trash text-error'></i>
                                                            </button>
                                                            <button
                                                                onClick={() => {
                                                                setEditKPIId(item.id);
                                                                setEditKPIKelompok(item.kelompok);
                                                                setEditKPINilai(item.nilai);
                                                            }}>
                                                                <i className='fa fa-edit ml-2 text-primary'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                );
                                            }
                                            return null; // Jika item tidak sesuai kategori_id, return null
                                        })}
                                    </tbody>
                                </table>

                            </div>
                            
                            <div className="shadow mt-10">
                                <label htmlFor="" className='p-3 pt-1 bg-base-100 rounded'>
                                    <b className='text-current'>2. Indeks Kompetensi dan Kualifikasi</b>
                                </label>
                                <table className="table w-full">
                                    <thead>
                                        <tr>
                                            <th className='bg-base-300'>Kelompok</th>
                                            <th className='bg-base-300'>Nilai</th>
                                            <th className='bg-base-300'>Bobot</th>
                                            <th className='bg-base-300'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {kpi_dokter2.map((item, index) => {
                                            if (item.kategori_id === 2) {
                                                return (
                                                    <tr key={index}>
                                                        <td className="whitespace-pre-wrap">{item.kelompok}</td>
                                                        <td>{item.nilai}</td>
                                                        <td>{dataKategori2[1]['bobot']}</td>
                                                        <td>
                                                            <button onClick={() => handleDeleteKPI(item.id)}>
                                                                <i className='fa fa-trash text-error'></i>
                                                            </button>
                                                            <button
                                                                onClick={() => {
                                                                setEditKPIId(item.id);
                                                                setEditKPIKelompok(item.kelompok);
                                                                setEditKPINilai(item.nilai);
                                                            }}>
                                                                <i className='fa fa-edit ml-2 text-primary'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                );
                                            }
                                            return null; // Jika item tidak sesuai kategori_id, return null
                                        })}
                                    </tbody>
                                </table>
                            </div>
                            
                            <div className="shadow mt-10">
                                <label htmlFor="" className='p-3 bg-base-100 rounded pt-1'>
                                    <b className='text-current'>3. Indeks Resiko</b>
                                </label>
                                <table className="table w-full">
                                    <thead>
                                        <tr>
                                            <th className='bg-base-300'>Kelompok</th>
                                            <th className='bg-base-300'>Nilai</th>
                                            <th className='bg-base-300'>Bobot</th>
                                            <th className='bg-base-300'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {kpi_dokter2.map((item, index) => {

                                            if (item.kategori_id === 3) {
                                                return (
                                                    <tr key={index}>
                                                        <td className="whitespace-pre-wrap">{item.kelompok}</td>
                                                        <td>{item.nilai}</td>
                                                        <td>{dataKategori2[2]['bobot']}</td>
                                                        <td>
                                                            <button onClick={() => handleDeleteKPI(item.id)}>
                                                                <i className='fa fa-trash text-error'></i>
                                                            </button>
                                                            <button
                                                                onClick={() => {
                                                                setEditKPIId(item.id);
                                                                setEditKPIKelompok(item.kelompok);
                                                                setEditKPINilai(item.nilai);
                                                            }}>
                                                                <i className='fa fa-edit ml-2 text-primary'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                );
                                            }
                                            return null; // Jika item tidak sesuai kategori_id, return null
                                        })}
                                    </tbody>
                                </table>
                            </div>
                            <div className="shadow mt-10">
                                <label htmlFor="" className='p-3 bg-base-100 rounded pt-1'>
                                    <b className='text-current'>
                                        4. Indeks Emergensi</b>
                                </label>
                                <table className="table w-full">
                                    <thead>
                                        <tr>
                                            <th className='bg-base-300'>Kelompok</th>
                                            <th className='bg-base-300'>Nilai</th>
                                            <th className='bg-base-300'>Bobot</th>
                                            <th className='bg-base-300'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {kpi_dokter2.map((item, index) => {
                                            if (item.kategori_id === 4) {
                                                return (
                                                    <tr key={index}>
                                                        <td className="whitespace-pre-wrap">{item.kelompok}</td>
                                                        <td>{item.nilai}</td>
                                                        <td>{dataKategori2[3]['bobot']}</td>
                                                        <td>
                                                            <button onClick={() => handleDeleteKPI(item.id)}>
                                                                <i className='fa fa-trash text-error'></i>
                                                            </button>
                                                            <button
                                                                onClick={() => {
                                                                setEditKPIId(item.id);
                                                                setEditKPIKelompok(item.kelompok);
                                                                setEditKPINilai(item.nilai);
                                                            }}>
                                                                <i className='fa fa-edit ml-2 text-primary'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                );
                                            }
                                            return null; // Jika item tidak sesuai kategori_id, return null
                                        })}
                                    </tbody>
                                </table>
                            </div>
                            <div className="shadow mt-10">
                                <label htmlFor="" className='p-3 bg-base-100 rounded pt-1'>
                                    <b className='text-current'>
                                        5. Indeks Posisi</b>
                                </label>
                                <table className="table w-full">
                                    <thead>
                                        <tr>
                                            <th className='bg-base-300'>Kelompok</th>
                                            <th className='bg-base-300'>Nilai</th>
                                            <th className='bg-base-300'>Bobot</th>
                                            <th className='bg-base-300'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {kpi_dokter2.map((item, index) => {
                                            if (item.kategori_id === 5) {
                                                return (
                                                    <tr key={index}>
                                                        <td className="whitespace-pre-wrap">{item.kelompok}</td>
                                                        <td>{item.nilai}</td>
                                                        <td>{dataKategori2[4]['bobot']}</td>
                                                        <td>
                                                            <button onClick={() => handleDeleteKPI(item.id)}>
                                                                <i className='fa fa-trash text-error'></i>
                                                            </button>
                                                            <button
                                                                onClick={() => {
                                                                setEditKPIId(item.id);
                                                                setEditKPIKelompok(item.kelompok);
                                                                setEditKPINilai(item.nilai);
                                                            }}>
                                                                <i className='fa fa-edit ml-2 text-primary'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                );
                                            }
                                            return null; // Jika item tidak sesuai kategori_id, return null
                                        })}
                                    </tbody>
                                </table>
                            </div>
                            <div className="shadow mt-10">
                                <label htmlFor="" className='p-3 bg-base-100 rounded pt-1'>
                                    <b className='text-current'>6. Indeks Kinerja dan Disiplin</b>
                                </label>
                                <table className="table w-full">
                                    <thead>
                                        <tr>
                                            <th className='bg-base-300'>Kelompok</th>
                                            <th className='bg-base-300'>Nilai</th>
                                            <th className='bg-base-300'>Bobot</th>
                                            <th className='bg-base-300'>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {kpi_dokter2.map((item, index) => {
                                            if (item.kategori_id === 6) {
                                                return (
                                                    <tr key={index}>
                                                        <td className="whitespace-pre-wrap">{item.kelompok}</td>
                                                        <td>{item.nilai}</td>
                                                        <td>{dataKategori2[5]['bobot']}</td>
                                                        <td>
                                                            <button onClick={() => handleDeleteKPI(item.id)}>
                                                                <i className='fa fa-trash text-error'></i>
                                                            </button>
                                                            <button
                                                                onClick={() => {
                                                                setEditKPIId(item.id);
                                                                setEditKPIKelompok(item.kelompok);
                                                                setEditKPINilai(item.nilai);
                                                            }}>
                                                                <i className='fa fa-edit ml-2 text-primary'></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                );
                                            }
                                            return null; // Jika item tidak sesuai kategori_id, return null
                                        })}
                                    </tbody>
                                </table>
                            </div>
                            {showModalAdd && (
                                <div
                                    className="fixed inset-0 z-50 flex items-center justify-center bg-opacity-75 bg-gray-800">
                                    <div className="w-100 bg-base-100 p-4 rounded-lg modal-box">
                                        <h3 className="font-bold text-lg">Tambah Data</h3>
                                        <div className="mt-4">
                                            <label htmlFor="" className="w-full">
                                                Kategori
                                            </label>
                                            <select
                                                className="input w-full bg-base-100 input-bordered"
                                                value={selectedVendor}
                                                onChange={(e) => setSelectedVendor(e.target.value)}>
                                                {kpi_kategori2.map((vendor) => (
                                                    <option key={vendor.id} value={vendor.id}>
                                                        {vendor.kategori}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                        <div className="mt-2">
                                            <label htmlFor="" className="w-full">
                                                Kelompok
                                            </label>
                                            <textarea
                                                placeholder="misal: Kehadiran Tepat Waktu : 0%"
                                                className="textarea textarea-bordered textarea-xs w-full"
                                                value={kelompok}
                                                onChange={(e) => setKelompok(e.target.value)}></textarea>
                                        </div>
                                        <div className="">
                                            <label htmlFor="" className="w-full">
                                                Nilai
                                            </label>
                                            <input
                                                type="text"
                                                placeholder="(1 - 100) / (N/A)"
                                                className="input w-full bg-base-100 input-bordered"
                                                value={nilai}
                                                min="1"
                                                max="100"
                                                onChange={(e) => setNilai(e.target.value)}/>
                                        </div>
                                        <div className="mt-4 flex justify-between">
                                            <button className="btn btn-primary btn-sm" onClick={handleSumbmitKPI}>
                                                Simpan Perubahan
                                            </button>
                                            <button
                                                className="btn btn-error btn-sm ml-2"
                                                onClick={() => {
                                                setShowModalAdd(false);
                                            }}>
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                            {editVendorId && (
                                <div
                                    className="fixed inset-0 z-50 flex items-center justify-center bg-opacity-75 bg-gray-800">
                                    <div className="w-80 bg-base-100 p-4 rounded-lg modal-box">
                                        <h3 className="font-bold text-lg">Edit Vendor</h3>
                                        <div className="mt-4">
                                            <label htmlFor="">Kategori</label>
                                            <input
                                                type="text"
                                                placeholder="Type here"
                                                className="input w-full bg-base-200"
                                                value={editVendorKategori}
                                                disabled
                                                readOnly/>
                                        </div>
                                        <div className="mt-4">
                                            <label htmlFor="">Bobot</label>
                                            <input
                                                type="number"
                                                placeholder="Type here"
                                                className="input w-full bg-base-300"
                                                value={editVendorName}
                                                onChange={(e) => setEditVendorName(e.target.value)}/>
                                        </div>
                                        <div className="mt-4 flex justify-between">
                                            <button className="btn btn-primary btn-sm" onClick={handleEditVendor}>
                                                Simpan Perubahan
                                            </button>
                                            <button
                                                className="btn btn-error btn-sm ml-2"
                                                onClick={() => setEditVendorId(null)}>
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                            {EditKPIId && (
                                <div
                                    className="fixed inset-0 z-50 flex items-center justify-center bg-opacity-75 bg-gray-800">
                                    <div className="w-100 bg-base-100 p-4 rounded-lg modal-box">
                                        <h3 className="font-bold text-lg">Edit Data KPI</h3>
                                        <div className="mt-4">
                                            <label htmlFor="">Kelompok</label>
                                            <textarea
                                                placeholder="Type here"
                                                className="input w-full bg-base-200 h-52"
                                                onChange={(e) => setEditKPIKelompok(e.target.value)}>{EditKPIKelompok}
                                            </textarea>
                                        </div>
                                        <div className="mt-4">
                                            <label htmlFor="">Nilai</label>
                                            <input
                                                type="text"
                                                placeholder="Type here"
                                                className="input w-full bg-base-300"
                                                value={EditKPINilai}
                                                onChange={(e) => setEditKPINilai(e.target.value)}/>
                                        </div>
                                        <div className="mt-4 flex justify-between">
                                            <button className="btn btn-primary btn-sm" onClick={handleEditKPI}>
                                                Simpan Perubahan
                                            </button>
                                            <button
                                                className="btn btn-error btn-sm ml-2"
                                                onClick={() => setEditKPIId(null)}>
                                                Batal
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
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
                pauseOnHover/>
        </AuthenticatedLayout>
    );
}
