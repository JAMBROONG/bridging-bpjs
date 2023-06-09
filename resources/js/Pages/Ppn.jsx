import React, { useState } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import axios from 'axios';
import * as XLSX from 'xlsx';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import 'react-toastify/dist/ReactToastify.css';

export default function Ppn({ auth, data }) {
    const [title,setTtitle] = useState('SHIFTING');
  const [selectedFile, setSelectedFile] = useState(null);
  const [dataPpn, setdataPpn] = useState(data.dataPPNShifting ? data.dataPPNShifting : data.dataPPNDistribution);
  const [excelData, setExcelData] = useState([]);
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [pathFile, setpathFile] = useState(data.pathFilePpn);

  const handleFileChange = (event) => {
    const file = event.target.files[0];
    setSelectedFile(file);
    const reader = new FileReader();
    reader.onload = (e) => {
      const workbook = XLSX.read(e.target.result, { type: 'binary' });
      const worksheet = workbook.Sheets[workbook.SheetNames[0]];
      const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

      setExcelData(jsonData);
    };
    reader.readAsBinaryString(file);
  };
  const filterData = (e) => {
    setTtitle(e.target.value);
    if (e.target.value == "ALLOCATION & DISTRIBUTION") {
        setdataPpn(data.dataPPNDistribution);
    } else if (e.target.value == "SHIFTING") {
        setdataPpn(data.dataPPNShifting);
    }
};

  const handleFileSubmit = () => {
    if (selectedFile) {
      const formData = new FormData();
      formData.append('file', selectedFile);

      axios
        .post('/upload-ppn', formData)
        .then((response) => {
          setExcelData([]);
          setpathFile(response.data.data.pathFilePpn);
          toast.success('File uploaded successfully.');
        })
        .catch((error) => {
          console.error(error);
          toast.error('Failed to upload file.');
        });
    }
  };


  const handleModalOpen = () => {
    setIsModalOpen(true);
  };

  const handleModalClose = () => {
    setIsModalOpen(false);
  };

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="PPN" />
      <div className="bg-base-200">
        <div className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="w-full">
            <div className="p-4 flex">
              <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                <h1 className="text-5xl">PPN DARI DATA <b className='bg-warning pl-2 pr-2'>{title}</b></h1>
              </div>
            </div>
            {
                !data.pathPendapatanRI && !data.pathPendapatanRJ && !data.pathBPJSRI && !data.pathBPJSRJ ? (
                    <>
                    <div className="flex">
                        <div className="w-full">
                            <div className="alert alert-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" className="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                <span>Anda belum mengupload file Pendapatan dan BPJS Rawat Inap dan Rawat Jalan! <Link href={route('shifting')}><u>Upload disini</u></Link></span>
                            </div>
                            <h3 className='text-center py-14' >Data tidak tersedia</h3>
                        </div>
                    </div>
                    </>
                ) : (
                    <>
                    
                    
            {!pathFile ? (
              <div className="w-80 bg-white p-4 rounded-lg shadow-lg">
                <label htmlFor="excelFile" className="w-full mb-2 font-medium text-lg btn">
                  Choose Excel File:
                </label>
                <input
                  id="excelFile"
                  type="file"
                  accept=".xlsx, .xls"
                  className="hidden"
                  onChange={handleFileChange}
                />
                <div className="flex items-center justify-between">
                  <div>
                    {selectedFile ? (
                      <p className="text-green-500 font-semibold">{selectedFile.name}</p>
                    ) : (
                      <p className="text-gray-400">No file chosen</p>
                    )}
                    {selectedFile && (
                      <p className="text-gray-400 text-sm">Size: {selectedFile.size} bytes</p>
                    )}
                  </div>
                  <button
                    className={`px-4 py-2 bg-blue-500 text-white font-semibold rounded ${
                      selectedFile ? '' : 'opacity-50 cursor-not-allowed'
                    }`}
                    disabled={!selectedFile}
                    onClick={handleFileSubmit}
                  >
                    Submit
                  </button>
                </div>
              </div>
            ) : (
              <>
                <div className="flex">
                    <button onClick={handleModalOpen} className="btn btn-primary">
                    Unggah Ulang Faktur
                    </button>
                    <select name="" onChange={filterData} className='input input-primary ml-2' id="">
                        <option value="SHIFTING">Shifting</option>
                        <option value="ALLOCATION & DISTRIBUTION">Allocation & Distribution</option>
                    </select>
                    {isModalOpen && (
                    <div className="fixed inset-0 z-50 flex items-center justify-center bg-opacity-75 bg-gray-800">
                        <div className="w-80 bg-base-100 p-4 rounded-lg modal-box">
                        <h3 className="font-bold text-lg">Unggah Ulang Faktur?</h3>
                        <p className="py-4">Aksi ini akan menghapus file faktur anda di sistem.</p>
                        <div className="flex">
                                <button onClick={handleModalClose} >
                                    <span className="text-end btn btn-sm btn-error">batal</span>
                                </button> 
                                <Link href={route('delete-ppn')} className='btn btn-sm btn-primary ml-2'>Ya. Unggah Ulang</Link>
                            </div>
                        </div>
                    </div>
                    )}
                </div>
                <div className="w-100 py-12">
                    {dataPpn? (
                        <>
                        
                        <div className="flex items-center">
                            <div className=' text-2xl mr-2'>
                                Rumus: 
                            </div>
                        <div className=" bg-base-100 p-2 rounded my-2">
                       (Obat Rawat Jalan / (Obat Rawat Jalan + Obar Rawat Inap)) X Jumlah PPN
                        </div>
                        </div>
                        <div className="flex flex-col lg:flex-row">
                            
                        <div className="overflow-x-auto flex-1">
                                <table className="table w-full table-zebra table-compact">
                                    {/* head */}
                                    <thead>
                                    <tr>
                                        <th>Sumber</th>
                                        <th>Akun</th>
                                        <th>Nominal</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>Rawat Jalan</th>
                                        <td>{dataPpn.laporanRJ['kategori']}</td>
                                        <td className='text-end text-xl'>{dataPpn.laporanRJ['jumlah'].toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <th colSpan={3} className=' text-center'>
                                            <hr className=' border-2 border-current' />
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Rawat Jalan</th>
                                        <td>{dataPpn.laporanRJ['kategori']}</td>
                                        <td className='text-end'>{dataPpn.laporanRJ['jumlah'].toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <th>Rawat Inap</th>
                                        <td>{dataPpn.laporanRI['kategori']}</td>
                                        <td className='text-end'>{dataPpn.laporanRI['jumlah'].toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <th colSpan={2}>Rawat Inap + Rawat Jalan</th>
                                        <td className='text-end text-xl'>
                                        <hr className=' border-1 border-current' />
                                        {(dataPpn.laporanRI['jumlah'] + dataPpn.laporanRJ['jumlah']).toLocaleString()}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th colSpan={3} className=' text-center'>
                                            <hr className=' border-2 border-current' />
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colSpan={2} className=' text-center'>
                                            JUMLAH
                                        </th>
                                        <th className='text-end text-2xl'>
                                        {(dataPpn.laporanRJ['jumlah'] / (dataPpn.laporanRI['jumlah'] + dataPpn.laporanRJ['jumlah'])).toLocaleString()}
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div className=" flex items-center p-4">
                            <h1 className='text-5xl'>X</h1>
                            </div>
                            <div className=" flex items-center justify-center p-4 flex-col">
                            <h1 className='text-3xl'>{data.jumlahPPN.toLocaleString()}</h1>
                            <h1 className='text-3xl'>(JUMLAH PPN)</h1>
                            </div>
                            <div className=" flex items-center p-4">
                            <h1 className='text-5xl'>=</h1>
                            </div>
                            <div className=" flex items-center p-4">
                            <h1 className='text-3xl'>{((dataPpn.laporanRJ['jumlah'] / (dataPpn.laporanRI['jumlah'] + dataPpn.laporanRJ['jumlah'])) * data.jumlahPPN).toLocaleString()}</h1>
                            </div>
                        </div>
                        </>
                    ):""}
                </div>
              </>
            )}

            {excelData.length > 0 && (
              <div className="mt-8 overflow-x-auto">
                <h2></h2>
                <table className=" table-compact w-full border">
                  <thead>
                    <tr>
                      {excelData[0].map((header, index) => (
                        <th key={index} className="bg-gray-200 border-b px-4 py-2">
                          {header}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody>
                    {excelData.slice(1).map((row, index) => (
                      <tr key={index}>
                        {row.map((cell, index) => (
                          <td key={index} className="border-b px-4 py-2">
                            {cell}
                          </td>
                        ))}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
            </>
                )
            }
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
