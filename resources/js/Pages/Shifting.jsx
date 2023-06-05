import {Head, Link} from '@inertiajs/react';
import axios from 'axios';
import React, {useState, useEffect} from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DashboardOutputShifting from '@/Components/DashboardOutputShifting';
import {ToastContainer, toast} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

export default function Shifting({auth, file}) {
    console.log(file);
    const [count,
        setCount] = useState(Object.values(file).filter(value => value !== null).length);
    const [currentStep,
        setCurrentStep] = useState(count > 1
        ? 'Export File'
        : 'Import File');
    const [isValidFile1,
        setIsValidFile1] = useState(false);
    const [isValidFile2,
        setIsValidFile2] = useState(false);
    const [isValidFile3,
        setIsValidFile3] = useState(false);
    const [isValidFile4,
        setIsValidFile4] = useState(false);
    const [dataFile,
        setDataFile] = useState(file);
    const [file1,
        setFile1] = useState(null);
    const [file2,
        setFile2] = useState(null);
    const [file3,
        setFile3] = useState(null);
    const [file4,
        setFile4] = useState(null);
    const [successMessage,
        setSuccessMessage] = useState(null);
    const [isLoading,
        setIsLoading] = useState(false);

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

    const handleFileChange3 = (e) => {
        const file = e.target.files[0];
        const allowedExtensions = ['.xlsx', '.xls'];

        if (file && allowedExtensions.some(ext => file.name.endsWith(ext))) {
            setIsValidFile3(true);
            setFile3(file);
        } else {
            setIsValidFile3(false);
        }
    };

    const handleFileChange4 = (e) => {
        const file = e.target.files[0];
        const allowedExtensions = ['.xlsx', '.xls'];

        if (file && allowedExtensions.some(ext => file.name.endsWith(ext))) {
            setIsValidFile4(true);
            setFile4(file);
        } else {
            setIsValidFile4(false);
        }
    };

    const handleSubmit = async(e) => {
        e.preventDefault();
        if ((isValidFile1 && isValidFile3) || (isValidFile2 && isValidFile4) || (isValidFile1 && isValidFile2 && isValidFile3 && isValidFile4)) {
            setCurrentStep('Proccess');
            setIsLoading(true);

            const formData = new FormData();
            formData.append('filePendapatanRI', file1);
            formData.append('filePendapatanRJ', file2);
            formData.append('fileBPJSRI', file3);
            formData.append('fileBPJSRJ', file4);
            try {
                const response = await axios.post('/upload-shifting', formData, {
                    headers: {
                        'Content-Type': 'multipart/formdata'
                    }
                });
                setDataFile(response.data.file);
                const dataElements = <DashboardOutputShifting/>;
                toast.success(response.data.message, {
                    position: 'bottom-right',
                    autoClose: 2000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                });
                setCount(Object.values(response.data.file).filter(value => value !== null).length);
                console.log(count);
                setSuccessMessage(dataElements);
                setCurrentStep('Export File');
                setIsLoading(false);
            } catch (error) {
                setCurrentStep('Import File');
                console.error(error);
                setIsLoading(false);
            }
        } else {
            // Tampilkan pesan error atau lakukan tindakan lain jika ada validasi yang tidak
            // terpenuhi
        }
    };

    useEffect(() => {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');
        if (dataFile['pathPendapatanRI'] && dataFile['pathPendapatanRJ'] && dataFile['pathBPJSRI'] && dataFile['pathBPJSRJ']) {
            setCurrentStep('Export File');
        }
    }, [dataFile]);

    // Validasi tambahan untuk mengaktifkan tombol jika kriteria terpenuhi
    const isButtonActive = (isValidFile1 && isValidFile3) || (isValidFile2 && isValidFile4) || (isValidFile1 && isValidFile2 && isValidFile3 && isValidFile4);

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Shifting"/>
            <div className=" bg-base-200">
                <div className="max-w-7xl pt-3 py-20 mx-auto sm:px-6 lg:px-8">
                    <div className="text-sm breadcrumbs">
                        <ul>
                            <li>
                                <Link href={route('shifting')}>
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        className="w-4 h-4 mr-2 stroke-current">
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                    </svg>
                                    Shifting
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div className="text-center mb-5 mt-10">
                        <h1 className="text-5xl font-bold text-center mb-5">Shifting</h1>
                        <ul className="steps">
                            <li
                                className={`step ${currentStep === 'Import File' || currentStep === 'Proccess' || currentStep === 'Export File'
                                ? 'step-primary'
                                : ''}`}>
                                Import File
                            </li>
                            <li
                                className={`step ${currentStep === 'Proccess' || currentStep === 'Export File'
                                ? 'step-primary'
                                : ''}`}>
                                Proccess
                            </li>
                            <li
                                className={`step ${currentStep === 'Export File'
                                ? 'step-primary'
                                : ''}`}>
                                Output
                            </li>
                        </ul>
                    </div>
                    {count > 1 || successMessage
                        ? (
                            <div className="card bg-base-100 shadow-sm">
                                <div className="card-body">
                                    <DashboardOutputShifting file={dataFile}/>
                                </div>
                            </div>
                        )
                        : (
                            <div className="card bg-base-100 shadow-sm">
                                <div className="card-body">
                                    <form onSubmit={handleSubmit}>
                                        <div className="flex flex-col w-full lg:flex-row">
                                            <div className="mb-5 flex-1 flex  justify-end">
                                                <a
                                                    href="/excel/Template File BPJS.xlsx"
                                                    download
                                                    className="link link-primary text-sm mr-3">
                                                    <i className="fas fa-download mr-2"/>
                                                    Template Klaim Bpjs
                                                </a>
                                                <a
                                                    href="/excel/Template File Pendapatan RS.xlsx"
                                                    download
                                                    className="link link-primary text-sm">
                                                    <i className="fas fa-download mr-2"/>
                                                    Template Pendapatan
                                                </a>
                                            </div>
                                        </div>
                                        <div className="flex flex-col w-full lg:flex-row">
                                            <div
                                                className="grid flex-grow py-10 card bg-base-300 rounded-box place-items-center">
                                                <div className="form-group text-center">
                                                    <label htmlFor="">
                                                        <span className="text-xl font-semibold">Pendapatan RS Rawat Inap</span>
                                                    </label>
                                                    <br/>
                                                    <input
                                                        type="file"
                                                        name="filePendapatanRI"
                                                        accept=".xlsx, .xls"
                                                        className="file-input mt-3 file-input-bordered w-full max-w-xs"
                                                        onChange={handleFileChange1}/>
                                                </div>
                                            </div>
                                            <div className="divider lg:divider-horizontal"></div>
                                            <div
                                                className="grid flex-grow py-10 card bg-base-300 rounded-box place-items-center">
                                                <div className="form-group text-center">
                                                    <label htmlFor="">
                                                        <span className="text-xl font-semibold">Pendapatan RS Rawat Jalan</span>
                                                    </label>
                                                    <br/>
                                                    <input
                                                        type="file"
                                                        name="filePendapatanRJ"
                                                        accept=".xlsx, .xls"
                                                        className="file-input mt-3 file-input-bordered w-full max-w-xs"
                                                        onChange={handleFileChange2}/>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="flex flex-col w-full lg:flex-row mt-5">
                                            <div
                                                className="grid flex-grow py-10 card bg-base-300 rounded-box place-items-center">
                                                <div className="form-group text-center">
                                                    <label htmlFor="">
                                                        <span className="text-xl font-semibold">Klaim BPJS Rawat Inap</span>
                                                    </label>
                                                    <br/>
                                                    <input
                                                        type="file"
                                                        name="fileBPJSRI"
                                                        accept=".xlsx, .xls"
                                                        className="file-input mt-3 file-input-bordered w-full max-w-xs"
                                                        onChange={handleFileChange3}/>
                                                </div>
                                            </div>
                                            <div className="divider lg:divider-horizontal"></div>
                                            <div
                                                className="grid flex-grow py-10 card bg-base-300 rounded-box place-items-center">
                                                <div className="form-group text-center">
                                                    <label htmlFor="">
                                                        <span className="text-xl font-semibold">Klaim BPJS Rawat Jalan</span>
                                                    </label>
                                                    <br/>
                                                    <input
                                                        type="file"
                                                        name="fileBPJSRJ"
                                                        accept=".xlsx, .xls"
                                                        className="file-input mt-3 file-input-bordered w-full max-w-xs"
                                                        onChange={handleFileChange4}/>
                                                </div>
                                            </div>
                                        </div>
                                        <button
                                            type="submit"
                                            className={`${isLoading
                                            ? 'btn loading btn-sm mt-3'
                                            : 'md:inline-block md:w-auto block w-full btn btn-primary btn-sm mt-3'}`}
                                            disabled={!isButtonActive}>
                                            {isLoading
                                                ? 'Loading'
                                                : 'Upload'}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        )}
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
