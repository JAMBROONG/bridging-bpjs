import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import axios from 'axios';
import { Head } from '@inertiajs/react';

export default function Dashboard({ auth }) {
  const [selectedOption, setSelectedOption] = useState('');
  const [file1, setFile1] = useState(null);
  const [file2, setFile2] = useState(null);
  const [responseArray, setResponseArray] = useState([]);

  const handleOptionChange = (event) => {
    setSelectedOption(event.target.value);
  };

  const handleFile1Change = (event) => {
    const selectedFile = event.target.files[0];
    setFile1(selectedFile);
  };

  const handleFile2Change = (event) => {
    const selectedFile = event.target.files[0];
    setFile2(selectedFile);
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    const formData = new FormData();
    formData.append('selectedOption', selectedOption);
    formData.append('file1', file1);
    formData.append('file2', file2);

    try {
      const response = await axios.post('/upload-shifting', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      console.log('Response:', response.data);
      alert('Response:', response.data);
      // Mengosongkan form dan menampilkan respons dari excel
      setSelectedOption('');
      setFile1(null);
      setFile2(null);
      setResponseArray(response.data);

    } catch (error) {
      console.error('Error:', error);
    }
  };

  return (
    <AuthenticatedLayout
      user={auth.user}
      header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
    >
      <Head title="Dashboard" />

      <div className="card mt-3">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900">
            <form encType="multipart/form-data"  onSubmit={handleSubmit} >
                <label htmlFor="selectOption" className="block font-medium text-sm text-gray-700">
                  Select Option
                </label>
                <select id="selectOption" name="selectOption" value={selectedOption} onChange={handleOptionChange} className="form-select mt-1 block w-full">
                  <option value="">Pilih Layanan</option>
                  <option value="option2">Rawat Inap</option>
                  <option value="option1">Rawat Jalan</option>
                </select>

                <label htmlFor="file1" className="block font-medium text-sm text-gray-700 mt-3">
                  File 1 (Excel)
                </label>
                <input id="file1" type="file" name="file1" accept=".xlsx, .xls" onChange={handleFile1Change} className="file-input w-full max-w-xs" />

                <label htmlFor="file2" className="block font-medium text-sm text-gray-700 mt-3">
                  File 2 (Excel)
                </label>
                <input id="file2" type="file" name="file1" accept=".xlsx, .xls" onChange={handleFile2Change}  className="file-input w-full max-w-xs" />

                <button type="submit" className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 mt-3 rounded">
                  Submit
                </button>
              </form>
              {/* Menampilkan respons dari excel */}
              {responseArray.length >= 0 && (
                <div>
                  <h3 className="text-lg font-semibold mt-4">Response from Excel:</h3>
                  <ul>
                    {responseArray.map((response, index) => (
                      <li key={index}>{response}</li>
                    ))}
                  </ul>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
