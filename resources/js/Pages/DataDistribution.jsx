import React, {useState} from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {Head, Link} from "@inertiajs/react";
import InfiniteScroll from "react-infinite-scroll-component";
export default function DataDistribution({auth, data}) {
    console.log(data);
    const [hasMore,
        setHasMore] = useState(true);
    const [dataToShow,
        setDataToShow] = useState(10);
    const [dataPage,
        setDataPage] = useState(data["pasienBPJS_RI"]
        ? data["pasienBPJS_RI"]
        : data["pasienBPJS_RJ"]);
    const [totalJS,
        setTotalJS] = useState(data.totalJSRI
        ? data.totalJSRI
        : data.totalJSRJ);
    const [totalJP,
        setTotalJP] = useState(data.totalJPRI
        ? data.totalJPRI
        : data.totalJPRJ);
    const [titleText,
        setTitleText] = useState(dataPage == data["pasienBPJS_RI"]
        ? "Pasien Rawat Inap"
        : "Pasien Rawat Jalan");
    const loadMoreData = () => {
        if (dataToShow >= dataPage.length) {
            setHasMore(false);
            return;
        }
        setDataToShow(dataToShow + 10);
    };
    const handleFilterChange = (event) => {
        const selectedValue = event.target.value;

        if (selectedValue === "Rawat Inap") {
            setDataPage(data["pasienBPJS_RI"]);
            setTotalJS(data.totalJSRI);
            setTotalJP(data.totalJPRI);
            setTitleText("Pasien Rawat Inap");
        } else if (selectedValue === "Rawat Jalan") {
            setDataPage(data["pasienBPJS_RJ"]);
            setTotalJS(data.totalJSRJ);
            setTotalJP(data.totalJPRJ);
            setTitleText("Pasien Rawat Jalan");
        } else {
            setDataPage(data["pasienBPJS_RJ"] || data["pasienBPJS_RI"]);
            setTitleText("Pasien Rawat Jalan");
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Jasa Pelayanan"/>
            <div className="bg-base-200">
                <div
                    className="py-20 pt-3 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-sm breadcrumbs">
                        <ul>
                            <li>
                                <Link  href={route('allocation.distribution')}>
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
                                    Allocation & Distribution
                                </Link>
                            </li>
                            <li>
                                <Link  href={route('data-distribution')}>
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
                                    Data Allocation and Distribution
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div className="w-full mt-10">
                        <div className="p-4 flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                <h1 className="text-5xl">Data Allocation & Distribution</h1>
                            </div>
                            <div className="flex-1 text-center">
                                <div className="stats shadow">
                                    {data["pasienBPJS_RJ"]
                                        ? (
                                            <div className="stat">
                                                <div className="stat-figure text-secondary">
                                                    <div className="avatar online">
                                                        <div className="w-16 rounded-full">
                                                            <img src="/img/pasien.jpg"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="stat-value">
                                                    {data["pasienBPJS_RJ"] && data["pasienBPJS_RJ"][0]
                                                        ? data["pasienBPJS_RJ"].length
                                                        : 0}
                                                </div>
                                                <div className="stat-title">Pasien RJ</div>
                                            </div>
                                        )
                                        : ("")}
                                    {data["pasienBPJS_RI"]
                                        ? (
                                            <div className="stat">
                                                <div className="stat-figure text-secondary">
                                                    <div className="avatar online">
                                                        <div className="w-16 rounded-full">
                                                            <img src="/img/pasien.jpg"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="stat-value">
                                                    {data["pasienBPJS_RI"] && data["pasienBPJS_RI"][0]
                                                        ? data["pasienBPJS_RI"].length
                                                        : 0}
                                                </div>
                                                <div className="stat-title">Pasien RI</div>
                                            </div>
                                        )
                                        : ("")}
                                </div>
                            </div>
                        </div>

                        <div className="flex mt-10">
                            {data["pasienBPJS_RJ"] || data["pasienBPJS_RI"]
                                ? (
                                    <div className="flex-1">
                                        <select
                                            className="select w-full max-w-xs rounded"
                                            onChange={handleFilterChange}
                                            defaultValue="">
                                            <option disabled value="">
                                                Tampilkan berdasarkan
                                            </option>
                                            {data["pasienBPJS_RI"] && (
                                                <option value="Rawat Inap">Rawat Inap</option>
                                            )}
                                            {data["pasienBPJS_RJ"] && (
                                                <option value="Rawat Jalan">Rawat Jalan</option>
                                            )}
                                        </select>

                                        <Link href={route("distribution.all")} className="indicator ml-3">
                                            <button className="btn btn-primary">Lihat Laporan Keseluruhan</button>
                                        </Link>
                                    </div>

                                )
                                : ("")}
                            <div className="flex-1 text-right">
                                <Link href={route("shifting.js")} className="indicator ml-8">
                                    <span className="indicator-item badge badge-secondary">
                                        {totalJS
                                            ? totalJS
                                            : ""}
                                    </span>
                                    <button className="btn">Jasa Sarana</button>
                                </Link>
                                <Link href={route("shifting.jp")} className="indicator ml-8">
                                    <span className="indicator-item badge badge-secondary">
                                        {totalJP
                                            ? totalJP
                                            : ""}
                                    </span>
                                    <button className="btn">Jasa Pelayanan</button>
                                </Link>
                            </div>
                        </div>
                        <h1 className="p-3 bg-warning text-black mt-3 rounded">
                            Menampilkan Data {titleText}
                        </h1>
                        {dataPage && dataPage[0]
                            ? (
                                <InfiniteScroll
                                    dataLength={dataToShow}
                                    next={loadMoreData}
                                    hasMore={hasMore}
                                    loader={< h4 > Loading ...</h4>}
                                    endMessage={<p> No more data to load. </p>}>
                                    {dataPage
                                        .slice(0, dataToShow)
                                        .map((item, index) => (
                                            <div className="overflow-x-auto mt-5 shadow" key={index}>
                                                <div>
                                                    <div className=" flex justify-between bg-base00 p-2">
                                                        <div className="text-sm flex-1">
                                                            {index + 1}. {item.PASIEN}
                                                            -
                                                            <b>{item.RM}</b>{" "}
                                                        </div>
                                                        <div className="text-sm flex-1 text-right">
                                                            INACBG:
                                                            <b>{item.INACBG}</b>
                                                        </div>
                                                    </div>

                                                    <table className="table table-compact w-full">
                                                        <thead>
                                                            <tr>
                                                                <th className="bg-base-300">NOTRANS</th>
                                                                <th className="bg-base-300">KLS TARIF</th>
                                                                <th className="bg-base-300">DOKTER</th>
                                                                <th className="bg-base-300">Tarif RS</th>
                                                                <th className="bg-base-300">Tarif Setelah di Allocation</th>
                                                                <th className="bg-base-300">Persentase</th>
                                                                <th className="bg-base-300">
                                                                    Setelah di Konversi{" "}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {item
                                                                .data
                                                                .map((dataItem, dataIndex) => (
                                                                    <tr key={dataIndex}>
                                                                        <td>{dataItem.NOTRANS}</td>
                                                                        <td>{dataItem["KLS TARIF"]}</td>
                                                                        <td>{dataItem.DOKTER}</td>
                                                                        <td className="text-end">
                                                                            {dataItem
                                                                                .JUMLAH
                                                                                .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                        </td>
                                                                        <td className="text-end">
                                                                            {dataItem
                                                                                ['jumlahSetelahDiKonversi']
                                                                                .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                        </td>
                                                                        <td className="text-end">
                                                                            {item.data_konversi[dataIndex].Persentase}
                                                                        </td>
                                                                        <td className="text-end">
                                                                            {item
                                                                                .data_konversi[dataIndex]
                                                                                .Jumlah_Konversi
                                                                                .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                        </td>
                                                                    </tr>
                                                                ))}
                                                            <tr>
                                                                <td colSpan="3" className="text-end">
                                                                    Jumlah:
                                                                </td>
                                                                <td className="text-end bg-base-300">
                                                                    {item["Tarif RS"].toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                </td>
                                                                <td className="text-end bg-base-300">
                                                                    
                                                                {item
                                                                        .data
                                                                        .reduce((total, konversiItem) => total + parseFloat(konversiItem.jumlahSetelahDiKonversi), 2)
                                                                        .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                </td>
                                                                <td className="text-end">100%</td>
                                                                <td className="text-end bg-base-300">
                                                                    {item
                                                                        .data_konversi
                                                                        .reduce((total, konversiItem) => total + parseFloat(konversiItem.Jumlah_Konversi), 2)
                                                                        .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        ))}
                                </InfiniteScroll>
                            )
                            : (
                                <div className="card bg-base-100 shadow-sm">
                                    <div className="card-header">
                                        Menampilkan data pasien yang tak tertagih
                                    </div>
                                    <div className="card-body"></div>
                                </div>
                            )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
