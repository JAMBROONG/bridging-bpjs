import React, {useState} from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {Head, Link} from "@inertiajs/react";
import InfiniteScroll from "react-infinite-scroll-component";
export default function ShiftingReportAll({auth, data}) {
    const {laporanRI} = data;
    const {laporanRJ} = data;
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Jasa Pelayanan"/>
            <div className="bg-base-200">
                <div
                    className="py-20 pt-3 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                            <li>
                                <Link href={route('data-shifting')}>
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
                                    Data Shifting
                                </Link>
                            </li>
                            <li>
                                <Link href={route('shifting.all')}>
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
                                    Laporan Keseluruhan
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div className="w-full mt-10">
                        <div className="p-4 flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                <h1 className="text-5xl">Laporan Shifting Secara Keseluruhan</h1>
                            </div>
                            <div className="flex-1 text-center"></div>
                        </div>
                    </div>
                    <div className="w-full mt-10">
                        <div className="overflow-x-auto">
                            <table className="table w-full">
                                <thead>
                                    <tr>
                                        <th className="bg-base-300">Jenis</th>
                                        <th className="bg-base-300">Penjamin</th>
                                        <th className="bg-base-300">Total Pendapatan (RP)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Rawat Jalan</td>
                                        <td>BPJS</td>
                                        <td className="text-right">{data
                                                .pendapatanRJBPJS
                                                .toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <td>Rawat Inap</td>
                                        <td>BPJS</td>
                                        <td className="text-right">{data
                                                .pendapatanRIBPJS
                                                .toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <td>Rawat Jalan</td>
                                        <td>NON BPJS</td>
                                        <td className="text-right">{data
                                                .pendapatanRJNonBPJS
                                                .toLocaleString()}</td>
                                    </tr>
                                    <tr>
                                        <td>Rawat Inap</td>
                                        <td>NON BPJS</td>
                                        <td className="text-right">{data
                                                .pendapatanRINonBPJS
                                                .toLocaleString()}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th className="bg-base-300 text-lg" colSpan="2">Jumlah</th>
                                        <th className="bg-base-300 text-lg text-right">{(data.pendapatanRJBPJS + data.pendapatanRIBPJS + data.pendapatanRINonBPJS + data.pendapatanRJNonBPJS).toLocaleString()}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr className="my-10"/>
                        <div className="flex">
                            <div className="flex-1 p-3">
                                <div className="p-2 bg-current">
                                    <div className="text-base-300">
                                        Rawat Inap
                                    </div>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="table w-full">
                                        <thead>
                                            <tr>
                                                <th className="bg-base-300">Kelas Tarif</th>
                                                <th className="bg-base-300">Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {laporanRI.map((item, index) => (
                                                <tr key={index}>
                                                    <td>{item.kategori}</td>
                                                    <td className="text-right">{item
                                                            .jumlah
                                                            .toLocaleString()}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th className="bg-base-300 text-md">Jumlah</th>
                                                <th className="bg-base-300 text-md text-right">
                                                    {laporanRI.reduce((total, item) => total + item.jumlah, 0).toLocaleString()}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div className="flex-1 p-3">
                                <div className="overflow-x-auto">
                                    <div className="p-2 bg-current">
                                        <div className="text-base-300">
                                            Rawat Jalan
                                        </div>
                                    </div>
                                    <table className="table w-full">
                                        <thead>
                                            <tr>
                                                <th className="bg-base-300">Kelas Tarif</th>
                                                <th className="bg-base-300">Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {laporanRJ.map((item, index) => (
                                                <tr key={index}>
                                                    <td>{item.kategori}</td>
                                                    <td className="text-right">{item
                                                            .jumlah
                                                            .toLocaleString()}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th className="bg-base-300 text-md">Jumlah</th>
                                                <th className="bg-base-300 text-md text-right">
                                                    {laporanRJ.reduce((total, item) => total + item.jumlah, 0).toLocaleString()}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div className="flex">
                            <div className="flex-1 p-3">
                                <div className="p-2 bg-current">
                                    <div className="text-base-300">
                                        Jumlah Pendapatan
                                    </div>
                                </div>
                                <div className="overflow-x-auto">
                                    <table className="table w-full">
                                        <tbody>
                                            <tr>
                                                <td>Rawat Jalan</td>
                                                <td className="text-right">{laporanRJ.reduce((total, item) => total + item.jumlah, 0).toLocaleString()}</td>
                                            </tr>
                                            <tr>
                                                <td>Rawat Inap</td>
                                                <td className="text-right">{laporanRI.reduce((total, item) => total + item.jumlah, 0).toLocaleString()}</td>
                                            </tr>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th className="bg-base-300 text-lg">Jumlah</th>
                                                <th className="bg-base-300 text-right text-lg">{((laporanRJ.reduce((total, item) => total + item.jumlah, 0)) + (laporanRI.reduce((total, item) => total + item.jumlah, 0))).toLocaleString()}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
