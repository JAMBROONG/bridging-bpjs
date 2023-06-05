import {Head, Link} from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {useState} from 'react';
import InfiniteScroll from "react-infinite-scroll-component";
export default function ShiftingJasaPelayanan({auth, data}) {
    const [dataTable,
        setDataTable] = useState(data.dataJPRI_BPJS
        ? data.dataJPRI_BPJS
        : data.dataJPRJ_BPJS);
    const [totalBPJS,
        setTotalBPJS] = useState(data.total_dataJPRI_BPJS
        ? data.total_dataJPRI_BPJS
        : data.total_dataJPRJ_NonBPJS);
    const [totalNonBPJS,
        setTotalNonBPJS] = useState(data.total_dataJPRI_NonBPJS
        ? data.total_dataJPRI_NonBPJS
        : data.total_dataJPRJ_NonBPJS);
    const [titleText,
        setTitleText] = useState(data["dataJPRI_BPJS"]
        ? "Pasien Rawat Inap"
        : "Pasien Rawat Jalan");
    const [titleBPJS,
        setTitleBPJS] = useState(data["dataJPRI_BPJS"] || data["dataJPRJ_BPJS"]
        ? "Pasien BPJS"
        : "Pasien Non BPJS");
    console.log(data.dataJPRI_NonBPJS);
    const handleFilterChange = (event) => {
        const selectedValue = event.target.value;

        if (selectedValue === "Rawat Inap" && titleBPJS === "Pasien BPJS") {
            setTotalBPJS(data.total_dataJPRI_BPJS);
            setTotalNonBPJS(data.total_dataJPRI_NonBPJS);
            setDataTable(data.dataJPRI_BPJS);
            setTitleText("Pasien Rawat Inap");
        } else if (selectedValue === "Rawat Inap" && titleBPJS === "Pasien Non BPJS") {
            setTotalBPJS(data.total_dataJPRI_BPJS);
            setTotalNonBPJS(data.total_dataJPRI_NonBPJS);
            setDataTable(data.dataJPRI_NonBPJS);
            setTitleText("Pasien Rawat Inap");
        } else if (selectedValue === "Rawat Jalan" && titleBPJS === "Pasien BPJS") {
            setTotalBPJS(data.total_dataJPRJ_BPJS);
            setTotalNonBPJS(data.total_dataJPRJ_NonBPJS);
            setDataTable(data.dataJPRJ_BPJS);
            setTitleText("Pasien Rawat Jalan");
        } else if (selectedValue === "Rawat Jalan" && titleBPJS === "Pasien Non BPJS") {
            setTotalBPJS(data.total_dataJPRJ_BPJS);
            setTotalNonBPJS(data.total_dataJPRJ_NonBPJS);
            setDataTable(data.dataJPRJ_NonBPJS);
            setTitleText("Pasien Rawat Jalan");
        }
    };
    const handleFilterBPJS = (event) => {
        const selectedValue = event.target.value;
        if (selectedValue === "Pasien BPJS" && titleText === "Pasien Rawat Inap") {
            setTitleBPJS("Pasien BPJS");
            setDataTable(data.dataJPRI_BPJS);
        } else if (selectedValue === "Pasien Non BPJS" && titleText === "Pasien Rawat Inap") {
            setTitleBPJS("Pasien Non BPJS");
            setDataTable(data.dataJPRI_NonBPJS);
        } else if (selectedValue === "Pasien BPJS" && titleText === "Pasien Rawat Jalan") {
            setTitleBPJS("Pasien BPJS");
            setDataTable(data.dataJPRJ_BPJS);
        } else if (selectedValue === "Pasien Non BPJS" && titleText === "Pasien Rawat Jalan") {
            setTitleBPJS("Pasien Non BPJS");
            setDataTable(data.dataJPRJ_NonBPJS);
        }
    };
    const [hasMore,
        setHasMore] = useState(true);
    const [dataToShow,
        setDataToShow] = useState(10);

    const loadMoreData = () => {
        if (dataToShow >= dataTable.length) {
            setHasMore(false);
            return;
        }
        setDataToShow(dataToShow + 10);
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
                                <Link href={route('dashboard')}>
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
                                    Dashboard
                                </Link>
                            </li>
                            <li>
                                <Link  href={route('dashboard')}>
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
                                <Link  href={route('data-shifting')}>
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
                                <Link  href={route('shifting.jp')}>
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
                                    Jasa Pelayanan
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div className="w-full mt-10">
                        <div className="flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                <h1 className="text-5xl mb-0">Shifting</h1><br/>
                                <small className="text-3xl p-1 bg-primary text-white rounded">Jasa Pelayanan {titleText}</small>
                            </div>
                            <div className="flex-1 text-center">
                                <div className="stats shadow">
                                    {totalNonBPJS && (
                                        <div className="stat">
                                            <div className="stat-figure text-secondary">
                                                <div className="avatar online">
                                                    <div className="w-16 rounded-full">
                                                        <img src="/img/pasien.jpg"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="stat-value">
                                                {totalNonBPJS || 0}
                                            </div>
                                            <div className="stat-desc text-secondary">Jenis Jasa</div>
                                            <div className="stat-title">Non BPJS</div>
                                        </div>
                                    )}
                                    {totalBPJS && (
                                        <div className="stat">
                                            <div className="stat-figure text-secondary">
                                                <div className="avatar online">
                                                    <div className="w-16 rounded-full">
                                                        <img src="/img/pasien.jpg"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="stat-value">
                                                {totalBPJS || 0}
                                            </div>
                                            <div className="stat-desc text-secondary">Jenis Jasa</div>
                                            <div className="stat-title">BPJS</div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="flex mt-10">
                            <div className="flex-1">
                                <select
                                    className="select w-full  rounded"
                                    onChange={handleFilterChange}
                                    defaultValue="">
                                    {data["dataJPRI_BPJS"] && (
                                        <option value="Rawat Inap">Rawat Inap</option>
                                    )}
                                    {data["dataJPRJ_BPJS"] && (
                                        <option value="Rawat Jalan">Rawat Jalan</option>
                                    )}
                                </select>
                            </div>
                            <div className="flex-1">

                                <select
                                    className="select w-full ml-2  rounded"
                                    onChange={handleFilterBPJS}
                                    defaultValue="">
                                    {data["dataJPRI_BPJS"] && (
                                        <option value="Pasien BPJS">Pasien BPJS</option>
                                    )}
                                    {data["dataJPRJ_BPJS"] && (
                                        <option value="Pasien Non BPJS">Pasien Non BPJS</option>
                                    )}
                                </select>
                            </div>
                            <div className="flex-1 text-end">
                                <small className="text-3xl p-1 bg-warning text-white rounded btn">
                                    <b>{titleBPJS}</b>
                                </small>
                            </div>
                        </div>
                        {titleBPJS == "Pasien Non BPJS"
                            ? (
                                <div className="">
                                    {dataTable && dataTable[0]
                                        ? (
                                            <InfiniteScroll
                                                dataLength={dataToShow}
                                                next={loadMoreData}
                                                hasMore={hasMore}
                                                loader={< h4 > Loading ...</h4>}
                                                endMessage={<p> No more data to load. </p>}>
                                                {dataTable
                                                    .slice(0, dataToShow)
                                                    .map((item, index) => (
                                                        <div className="" key={index}>
                                                            <div className="p-2 rounded bg-primary  mb-2 mt-10 text-sm">
                                                                <div>
                                                                    {index + 1}. {item['KLS TARIF']}
                                                                </div>
                                                            </div>
                                                            {item
                                                                .DATA
                                                                .map((dataItem, dataIndex) => (
                                                                    <div className="card bg-current mt-5 p-3 rounded" key={dataIndex}>
                                                                        <div className="flex">
                                                                            <div className="w-1/3 p-2  text-sm text-base-300 flex-1">
                                                                                <small className='text-lg btn btn-sm rounded'>No. {dataIndex + 1}</small>
                                                                                <br/>
                                                                                Pasien:
                                                                                <b>{dataItem.PASIEN}</b>
                                                                                <br/>
                                                                                No. RM:
                                                                                <b>{dataItem.RM}</b>
                                                                            </div>
                                                                        </div>
                                                                        <table className="table table-compact w-full">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th className="bg-base-300">NOTRANS</th>
                                                                                    <th className="bg-base-300">UNIT</th>
                                                                                    <th className="bg-base-300">PRODUK</th>
                                                                                    <th className="bg-base-300">DOKTER</th>
                                                                                    <th className="bg-base-300">Tarif RS</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                {dataItem
                                                                                    .data
                                                                                    .map((dataPasiens, dataPasiensIndex) => (
                                                                                        <tr key={dataPasiensIndex}>
                                                                                            <td>{dataPasiens.NOTRANS}</td>
                                                                                            <td>{dataPasiens.UNIT}</td>
                                                                                            <td>{dataPasiens["PRODUK"]}</td>
                                                                                            <td>{dataPasiens.DOKTER}</td>
                                                                                            <td className="text-end">
                                                                                                {dataPasiens
                                                                                                    .JUMLAH
                                                                                                    .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                                            </td>
                                                                                        </tr>
                                                                                    ))}
                                                                                <tr>
                                                                                    <td colSpan="4" className="text-end">
                                                                                        Jumlah:
                                                                                    </td>
                                                                                    <td className="text-end bg-base-300">
                                                                                        {dataItem
                                                                                            .data
                                                                                            .reduce((total, dataPasiens) => total + parseInt(dataPasiens.JUMLAH), 0)
                                                                                            .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                ))}
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
                            )
                            : (
                                <div className="">
                                    {dataTable && dataTable[0]
                                        ? (
                                            <InfiniteScroll
                                                dataLength={dataToShow}
                                                next={loadMoreData}
                                                hasMore={hasMore}
                                                loader={< h4 > Loading ...</h4>}
                                                endMessage={<p> No more data to load. </p>}>
                                                {dataTable
                                                    .slice(0, dataToShow)
                                                    .map((item, index) => (
                                                        <div className="" key={index}>
                                                            <div className="p-2 rounded bg-primary  mb-2 mt-10 text-sm">
                                                                <div>
                                                                    {index + 1}. {item['KLS TARIF']}
                                                                </div>
                                                            </div>
                                                            {item
                                                                .DATA
                                                                .map((dataItem, dataIndex) => (
                                                                    <div className="card bg-current mt-5 p-3 rounded" key={dataIndex}>
                                                                        <div className="flex">
                                                                            <div className="w-1/3 p-2  text-sm text-base-300 flex-1">
                                                                                <small className='text-lg btn btn-sm rounded'>No. {dataIndex + 1}</small>
                                                                                <br/>
                                                                                Pasien:
                                                                                <b>{dataItem.PASIEN}</b>
                                                                                <br/>
                                                                                No. RM:
                                                                                <b>{dataItem.RM}</b>
                                                                                <br/>
                                                                                INACBG:
                                                                                <b>{dataItem.INACBG}</b>
                                                                            </div>
                                                                        </div>
                                                                        <table className="table table-compact w-full">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th className="bg-base-300">NOTRANS</th>
                                                                                    <th className="bg-base-300">PRODUK</th>
                                                                                    <th className="bg-base-300">DOKTER</th>
                                                                                    <th className="bg-base-300">Tarif RS</th>
                                                                                    <th className="bg-base-300">Persentase</th>
                                                                                    <th className="bg-base-300">Setelah di Shifting</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                {dataItem
                                                                                    .data
                                                                                    .map((dataPasiens, dataPasiensIndex) => (
                                                                                        <tr key={dataPasiensIndex}>
                                                                                            <td>{dataPasiens.NOTRANS}</td>
                                                                                            <td>{dataPasiens["PRODUK"]}</td>
                                                                                            <td>{dataPasiens.DOKTER}</td>
                                                                                            <td className="text-end">
                                                                                                {dataPasiens
                                                                                                    .JUMLAH
                                                                                                    .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                                            </td>
                                                                                            <td className="text-end">
                                                                                                {((dataPasiens.JUMLAH / dataItem.data.reduce((total, dataPasiens) => total + parseInt(dataPasiens.JUMLAH), 0)) * 100).toFixed(2)}%
                                                                                            </td>
                                                                                            <td className="text-end">
                                                                                                {dataItem['Data Konversi'][dataPasiensIndex]['Jumlah_Konversi'].toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                                            </td>
                                                                                        </tr>
                                                                                    ))}
                                                                                <tr>
                                                                                    <td colSpan="3" className="text-end">
                                                                                        Jumlah:
                                                                                    </td>
                                                                                    <td className="text-end bg-base-300">
                                                                                        {dataItem
                                                                                            .data
                                                                                            .reduce((total, dataPasiens) => total + parseInt(dataPasiens.JUMLAH), 0)
                                                                                            .toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                                    </td>
                                                                                    <td className="text-end">100%</td>
                                                                                    <td className="text-end bg-base-300">
                                                                                        {dataItem['Data Konversi'].reduce((total, konversiItem) => total + parseFloat(konversiItem.Jumlah_Konversi), 2).toLocaleString("id-ID", {maximumFractionDigits: 0})}
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                ))}
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

                            )
}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
