import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import React, { useState } from 'react';

export default function Shifting({ auth }) {
  const [service, setService] = useState('');
  const [isValidFile1, setIsValidFile1] = useState(false);
  const [isValidFile2, setIsValidFile2] = useState(false);
  const [currentStep, setCurrentStep] = useState('Import File');
  const [file1, setFile1] = useState(null);
  const [file2, setFile2] = useState(null);
  const [successMessage, setSuccessMessage] = useState(null);
  const [isLoading, setIsLoading] = useState(false);

  const handleServiceChange = (e) => {
    setService(e.target.value);
  };

  const handleFileChange1 = (e) => {
    const file = e.target.files[0];
    const allowedExtensions = ['.xlsx', '.xls'];

    if (file && allowedExtensions.some(ext => file.name.endsWith(ext))) {
      setIsValidFile1(true);
      setFile1(file);
    } else {
      setIsValidFile1(false);
    }
  };

  const handleFileChange2 = (e) => {
    const file = e.target.files[0];
    const allowedExtensions = ['.xlsx', '.xls'];

    if (file && allowedExtensions.some(ext => file.name.endsWith(ext))) {
      setIsValidFile2(true);
      setFile2(file);
    } else {
      setIsValidFile2(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (service !== '' && isValidFile1 && isValidFile2) {
      setCurrentStep('Proccess');
      setIsLoading(true);

      const formData = new FormData();
      formData.append('file1', file1);
      formData.append('file2', file2);
      try {
        const response = await axios.post('/upload-shifting', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        const mergedData = Object.values(response.data);

        // Membuat array elemen untuk menampilkan data
        const dataElements = (
          <table className='table table-compact w-full'>
            <thead>
              <tr>
                <th>RM</th>
                <th>NOTRANS</th>
                <th>TANGGAL</th>
                <th>PASIEN</th>
                <th>UNIT</th>
                <th>FAKTUR</th>
                <th>PRODUK</th>
                <th>KLS TARIF</th>
                <th>OBAT</th>
                <th>QTY</th>
                <th>TARIP</th>
                <th>JUMLAH</th>
                <th>DOKTER</th>
                <th>PENJAMIN</th>
              </tr>
            </thead>
            <tbody>
              {mergedData[0].map((item, index) => (
                <tr key={index}>
                  <th>{item.rm}</th>
                  <td>{item.no_transaksi}</td>
                  <td>{item.tanggal}</td>
                  <td>{item.pasien}</td>
                  <td>{item.unit}</td>
                  <td>{item.faktur}</td>
                  <td>{item.produk}</td>
                  <td>{item.kls_tarif}</td>
                  <td>{item.obat}</td>
                  <td>{item.qty}</td>
                  <td>{item.tarip}</td>
                  <td>{item.jumlah}</td>
                  <td>{item.dokter}</td>
                  <td>{item.penjamin}</td>
                </tr>
              ))}
            </tbody>
          </table>
        );
        setSuccessMessage(dataElements);
        setIsLoading(false);
      } catch (error) {
        // Tangani error yang terjadi
        console.error(error);
        setIsLoading(false);
      }
    } else {
      // Tampilkan pesan error atau lakukan tindakan lain jika ada validasi yang tidak terpenuhi
    }
  };

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Shifting" />
      <div className=" py-12 bg-base-200">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="text-center mb-5">
            <h1 className="text-5xl font-bold text-center mb-5">Shifting</h1>
            <ul className="steps">
              <li className={`step ${currentStep === 'Import File' || 'Proccess' ? 'step-primary' : ''} mr-5`}>Import File</li>
              <li className={`step ${currentStep === 'Proccess' ? 'step-primary' : ''}`}>Proccess</li>
              <li className={`step ${currentStep === 'Export File' ? 'step-primary' : ''}`}>Export File</li>
            </ul>
          </div>
          <div className="card bg-base-100 shadow-sm">
            <div className="card-body">
              <form onSubmit={handleSubmit}>
                <div className="flex flex-col w-full lg:flex-row">
                  <div className="mb-5 flex-1">
                    <label htmlFor="">
                      <span className="text-xl font-semibold">Pilih Layanan</span>
                    </label>
                    <br />
                    <select className="select select-bordered w-full max-w-xs" value={service} onChange={handleServiceChange}>
                      <option value="" disabled>Pilih Layanan</option>
                      <option value="Rawat Inap">Rawat Inap</option>
                      <option value="Rawat Jalan">Rawat Jalan</option>
                    </select>
                  </div>
                  <div className="mb-5 flex-1 flex items-end justify-end">
                    <a href='' className='md:inline-block md:w-auto block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'><i className="fas fa-download mr-3" /> template</a>
                  </div>
                </div>
                <div className="flex flex-col w-full lg:flex-row">
                  <div className="grid flex-grow py-10 card bg-base-300 rounded-box place-items-center">
                    <div className="form-group text-center">
                      <label htmlFor="">
                        <span className="text-xl font-semibold">File Pendapatan RS (Excel)</span>
                      </label>
                      <br />
                      <input type="file" name='file1' accept=".xlsx, .xls" className="file-input mt-3 file-input-bordered w-full max-w-xs" onChange={handleFileChange1} />
                    </div>
                  </div>
                  <div className="divider lg:divider-horizontal">AND</div>
                  <div className="grid flex-grow py-10 card bg-base-300 rounded-box place-items-center">
                    <div className="form-group text-center">
                      <label htmlFor="">
                        <span className="text-xl font-semibold">File BPJS (Excel)</span>
                      </label>
                      <br />
                      <input type="file" name='file2' accept=".xlsx, .xls" className="file-input mt-3 file-input-bordered w-full max-w-xs" onChange={handleFileChange2} />
                    </div>
                  </div>
                </div>
                <button type="submit" className={`${isLoading ? 'btn loading btn-sm mt-3' : 'md:inline-block md:w-auto block w-full btn btn-primary btn-sm mt-3'}`} disabled={service === '' || !isValidFile1 || !isValidFile2}>
                  {isLoading ? 'Loading' : 'Upload'}
                </button>
              </form>
              {successMessage && (
                <div className="mt-5 overflow-x-auto shadow-md">{successMessage}</div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
