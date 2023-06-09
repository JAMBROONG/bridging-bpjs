import React, { useState } from 'react';
import { ToastContainer, toast } from 'react-toastify';
import axios from 'axios';
import * as XLSX from 'xlsx';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import 'react-toastify/dist/ReactToastify.css';

export default function VendorObat({ auth, data }) {
  const [dataVendor, setDataVendor] = useState(data);
  const [newVendor, setNewVendor] = useState('');
  const [editVendorId, setEditVendorId] = useState(null);
  const [editVendorName, setEditVendorName] = useState('');

  const handleFileChange = (event) => {
    const file = event.target.files[0];
    // Implement your file handling logic here
  };

  const handleFileSubmit = () => {
    // Implement your file submission logic here
  };

  const handleAddVendor = () => {
    if (newVendor) {
      axios
        .post('/vendor-add', { vendor: newVendor })
        .then((response) => {
          const vendor = response.data.data;
          setDataVendor(vendor);
          setNewVendor('');
          toast.success('Vendor berhasil ditambahkan');
        })
        .catch((error) => {
          console.error(error);
          toast.error('Gagal menambahkan vendor');
        });
    } else {
      toast.error('Nama vendor harus diisi');
    }
  };

  const handleEditVendor = () => {
    if (editVendorName) {
      axios
        .post('/vendor-update', { id: editVendorId, vendor: editVendorName })
        .then(() => {
          setDataVendor((prevData) =>
            prevData.map((vendor) =>
              vendor.id === editVendorId ? { ...vendor, vendor: editVendorName } : vendor
            )
          );
          setEditVendorId(null);
          setEditVendorName('');
          toast.success('Vendor berhasil diperbarui');
        })
        .catch((error) => {
          console.error(error);
          toast.error('Gagal memperbarui vendor');
        });
    } else {
      toast.error('Nama vendor harus diisi');
    }
  };

  const handleDeleteVendor = (id) => {
    if (window.confirm('Anda yakin ingin menghapus vendor ini?')) {
      axios
        .post('/vendor-delete', { id })
        .then(() => {
          setDataVendor((prevData) => prevData.filter((vendor) => vendor.id !== id));
          toast.success('Vendor berhasil dihapus');
        })
        .catch((error) => {
          console.error(error);
          toast.error('Gagal menghapus vendor');
        });
    }
  };

  return (
    <AuthenticatedLayout user={auth.user}>
      <Head title="Vedor Obat" />
      <div className="bg-base-200">
        <div className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="w-full">
            <div className="p-4 flex">
              <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                <h1 className="text-5xl">
                  DATA <b className="bg-warning pl-2 pr-2">VENDOR OBAT</b>
                </h1>
              </div>
            </div>
            <div className="w-full p-4 rounded flex">
              <div className="overflow-x-auto flex-1  shadow">
                <table className="table table-zebra w-full">
                  {/* head */}
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Vendor</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {dataVendor.map((vendor, index) => (
                      <tr key={vendor.id}>
                        <th>{index + 1}</th>
                        <td>{vendor.vendor}</td>
                        <td>
                          <button
                            onClick={() => {
                              setEditVendorId(vendor.id);
                              setEditVendorName(vendor.vendor);
                            }}
                          >
                            <i className="fa fa-edit text-primary mr-2"></i>
                          </button>
                          <button onClick={() => handleDeleteVendor(vendor.id)}>
                            <i className="fa fa-trash text-error"></i>
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              <div className="flex-1 p-3 pt-0">
                <div className="w-full bg-base-100 p-3 rounded shadow">
                  <label htmlFor="">Tambah Vendor</label>
                  <input
                    type="text"
                    placeholder="Type here"
                    className="input w-full mt-3 bg-base-300"
                    value={newVendor}
                    onChange={(e) => setNewVendor(e.target.value)}
                  />
                  <button className="btn btn-primary btn-sm mt-3" onClick={handleAddVendor}>
                    Simpan
                  </button>
                </div>
              </div>
            </div>
            {editVendorId && (
              <div className="fixed inset-0 z-50 flex items-center justify-center bg-opacity-75 bg-gray-800">
                <div className="w-80 bg-base-100 p-4 rounded-lg modal-box">
                  <h3 className="font-bold text-lg">Edit Vendor</h3>
                  <div className="mt-4">
                    <input
                      type="text"
                      placeholder="Type here"
                      className="input w-full bg-base-300"
                      value={editVendorName}
                      onChange={(e) => setEditVendorName(e.target.value)}
                    />
                  </div>
                  <div className="mt-4 flex justify-between">
                    <button className="btn btn-primary btn-sm" onClick={handleEditVendor}>
                      Simpan Perubahan
                    </button>
                    <button className="btn btn-error btn-sm ml-2" onClick={() => setEditVendorId(null)}>
                      Batal
                    </button>
                  </div>
                </div>
              </div>
            )}
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
