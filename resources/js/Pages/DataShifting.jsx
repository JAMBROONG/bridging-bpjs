import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import InfiniteScroll from 'react-infinite-scroll-component';
import { useHistory } from 'react-router-dom';

export default function DataShifting({ auth, data }) {
    console.log(data);
    const [hasMore, setHasMore] = useState(true);
    const [dataToShow, setDataToShow] = useState(10); // Jumlah data yang ditampilkan awalnya
    const [dataPage, setDataPage] = useState(data['pasienBPJS_RI'] ? data['pasienBPJS_RI'] : data['pasienBPJS_RJ']); // Page ke mana kita sedang
    const [totalJS, setTotalJS] = useState(data.totalJSRI ? data.totalJSRI : data.totalJSRJ); // Page ke mana kita sedang
    const [totalJP, setTotalJP] = useState(data.totalJPRI ? data.totalJPRI : data.totalJPRJ); // Page ke mana kita sedang
    const [titleText, setTitleText] = useState(dataPage == data['pasienBPJS_RI'] ? "Pasien Rawat Inap" : "Pasien Rawat Jalan");
    const loadMoreData = () => {
        if (dataToShow >= dataPage.length) {
            setHasMore(false);
            return;
        }
        // Tambahkan jumlah data yang akan ditampilkan saat tombol "Load More" diklik
        setDataToShow(dataToShow + 10);
    };
    const handleFilterChange = (event) => {
        const selectedValue = event.target.value;

        if (selectedValue === 'Rawat Inap') {
            setDataPage(data['pasienBPJS_RI']);
            setTotalJS(data.totalJSRI);
            setTotalJP(data.totalJPRI);
            setTitleText("Pasien Rawat Inap");
        } else if (selectedValue === 'Rawat Jalan') {
            setDataPage(data['pasienBPJS_RJ']);
            setTotalJS(data.totalJSRJ);
            setTotalJP(data.totalJPRJ);
            setTitleText("Pasien Rawat Jalan");
        } else {
            setDataPage(data['pasienBPJS_RJ'] || data['pasienBPJS_RI']);
            setTitleText("Pasien Rawat Jalan");
        }
    };
    const history = useHistory();
    const handleJasaSaranaClick = () => {
        // Routing ke route yang diinginkan untuk Jasa Sarana
        history.push('/jasa-sarana');
    };

    const handleJasaPelayananClick = () => {
        // Routing ke route yang diinginkan untuk Jasa Pelayanan
        history.push('/jasa-pelayanan');
    };
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Jasa Pelayanan" />
            <div className="bg-base-200">
                <div className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="w-full">
                        <div className="p-4 flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                <h1 className="text-5xl">Data Sifting</h1>
                            </div>
                            <div className="flex-1 text-center">
                                <div className="stats shadow">
                                    {data['pasienBPJS_RJ'] ? (
                                        <div className="stat">
                                            <div className="stat-figure text-secondary">
                                                <div className="avatar online">
                                                    <div className="w-16 rounded-full">
                                                        <img src='/img/pasien.jpg' />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="stat-value">{data['pasienBPJS_RJ'] && data['pasienBPJS_RJ'][0] ? data['pasienBPJS_RJ'].length : 0}</div>
                                            <div className="stat-title">Pasien RJ</div>
                                        </div>
                                    ) : ''}
                                    {data['pasienBPJS_RI'] ? (
                                        <div className="stat">
                                            <div className="stat-figure text-secondary">
                                                <div className="avatar online">
                                                    <div className="w-16 rounded-full">
                                                        <img src='/img/pasien.jpg' />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="stat-value">{data['pasienBPJS_RI'] && data['pasienBPJS_RI'][0] ? data['pasienBPJS_RI'].length : 0}</div>
                                            <div className="stat-title">Pasien RI</div>
                                        </div>
                                    ) : ''}
                                </div>
                            </div>
                        </div>
                        
                        <div className="flex">
                        {data['pasienBPJS_RJ'] || data['pasienBPJS_RI'] ? (
                            <select className="select w-full max-w-xs rounded flex-1" onChange={handleFilterChange} defaultValue="">
                                <option disabled value="">Tampilkan berdasarkan</option>
                                {data['pasienBPJS_RI'] && <option value="Rawat Inap">Rawat Inap</option>}
                                {data['pasienBPJS_RJ'] && <option value="Rawat Jalan">Rawat Jalan</option>}
                            </select>
                        ) : ''}
                        <div className="flex-1 text-right">
                        <div className="indicator">
      <span className="indicator-item badge badge-secondary">{totalJS ? totalJS : ''}</span>
      <button className="btn" onClick={handleJasaSaranaClick}>
        Jasa Sarana
      </button>
    </div>
    <div className="indicator ml-10">
      <span className="indicator-item badge badge-secondary">{totalJP ? totalJP : ''}</span>
      <button className="btn" onClick={handleJasaPelayananClick}>
        Jasa Pelayanan
      </button>
    </div>
                        </div>
                        </div>
                        <h1 className='p-3 bg-warning text-black mt-3 rounded'>Menampilkan Data {titleText}</h1>
                        {dataPage && dataPage[0] ? (
                        <InfiniteScroll
                            dataLength={dataToShow}
                            next={loadMoreData}
                            hasMore={hasMore}
                            loader={<h4>Loading...</h4>}
                            endMessage={<p>No more data to load.</p>}
                        >
                            {dataPage.slice(0, dataToShow).map((item, index) => (
                                <div className="overflow-x-auto mt-5 shadow" key={index}>
                                    <div>
                                        <div className=' flex justify-between bg-base00 p-2'>
                                            <div className="text-sm flex-1">{index + 1}. {item.PASIEN} - <b>{item.RM}</b> </div>
                                            <div className="text-sm flex-1 text-right">

                                            INACBG: <b>{item.INACBG}</b>
                                            </div>
                                        </div>
                                        
                                        <table className="table table-compact w-full">
                                            <thead>
                                                <tr>
                                                    <th className='bg-base-300'>NOTRANS</th>
                                                    <th className='bg-base-300'>KLS TARIF</th>
                                                    <th  className='bg-base-300'>DOKTER</th>
                                                    <th  className='bg-base-300'>Tarif RS</th>
                                                    <th  className='bg-base-300'>Persentase</th>
                                                    <th  className='bg-base-300'>Setelah di Shifting </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {item.data.map((dataItem, dataIndex) => (
                                                    <tr key={dataIndex}>
                                                        <td>{dataItem.NOTRANS}</td>
                                                        <td>{dataItem['KLS TARIF']}</td>
                                                        <td>{dataItem.DOKTER}</td>
                                                        <td className='text-end'>{dataItem.JUMLAH.toLocaleString('id-ID', {
                                                            maximumFractionDigits: 0,
                                                        })}</td>
                                                        <td className='text-end'>{item.data_konversi[dataIndex].Persentase}</td>
                                                        <td className='text-end'>{item.data_konversi[dataIndex].Jumlah_Konversi.toLocaleString('id-ID', {
                                                            maximumFractionDigits: 0,
                                                        })}</td>
                                                    </tr>
                                                ))}
                                                <tr>
                                                    <td colSpan="3" className="text-end">Jumlah:</td>
                                                    <td className="text-end bg-base-300">
                                                        {item['Tarif RS'].toLocaleString('id-ID', {
                                                            maximumFractionDigits: 0,
                                                        }) }
                                                    </td>
                                                    <td className='text-end'>100%</td>
                                                    <td className="text-end bg-base-300">
                                                        {item.data_konversi.reduce(
                                                            (total, konversiItem) => total + parseFloat(konversiItem.Jumlah_Konversi),2
                                                        ).toLocaleString('id-ID', {
                                                            maximumFractionDigits: 0,
                                                        })}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            ))}
                        </InfiniteScroll>
                    ) : (
                        <div className="card bg-base-100 shadow-sm">
                            <div className="card-header">Menampilkan data pasien yang tak tertagih</div>
                            <div className="card-body"></div>
                        </div>
                    )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
