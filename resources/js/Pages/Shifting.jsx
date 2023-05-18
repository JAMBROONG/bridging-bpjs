import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Inertia } from '@inertiajs/inertia';
import React, { useState } from 'react';

export default function Shifting({ auth }) {
  const [service, setService] = useState('');
  const [isValidFile1, setIsValidFile1] = useState(false);
  const [isValidFile2, setIsValidFile2] = useState(false);
  const [currentStep, setCurrentStep] = useState('Import File');
  const [file1, setFile1] = useState(null);
  const [file2, setFile2] = useState(null);
  const [successMessage, setSuccessMessage] = useState(null);

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

      const formData = new FormData();
      formData.append('file1', file1);
      formData.append('file2', file2);
      try {
        const response = await Inertia.post('/upload-shifting', formData);
        setSuccessMessage('File berhasil diunggah dan diproses.');
      } catch (error) {
        // Tangani error yang terjadi
        console.error(error);
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
                    <a href='' className='btn btn-sm p-1 rounded'><i className="fas fa-download" /> template</a>
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
                <button type="submit" className="btn btn-primary btn-sm mt-3" disabled={service === '' || !isValidFile1 || !isValidFile2}>
                  Upload
                </button>
                {successMessage && (
                  <div className="success-message">{successMessage}</div>
                )}
              </form>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
