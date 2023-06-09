import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import InfiniteScroll from 'react-infinite-scroll-component';

export default function PiutangBPJS({ auth, dataPiutang }) {
    console.log(dataPiutang);
    const [hasMore, setHasMore] = useState(true);
    const [dataToShow, setDataToShow] = useState(10); // Jumlah data yang ditampilkan awalnya
    const [dataPage, setDataPage] = useState(dataPiutang['dataPiutang_RJ'] ? dataPiutang['dataPiutang_RJ'] : dataPiutang['dataPiutang_RI']); // Page ke mana kita sedang
    const [titleText, setTitleText] = useState(dataPage == dataPiutang['dataPiutang_RJ'] ? "Pasien Rawat Jalan" : "Pasien Rawat Jalan");
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
            setDataPage(dataPiutang['dataPiutang_RI']);
            setTitleText("Pasien Rawat Inap");
        } else if (selectedValue === 'Rawat Jalan') {
            setDataPage(dataPiutang['dataPiutang_RJ']);
            setTitleText("Pasien Rawat Jalan");
        } else {
            setDataPage(dataPiutang['dataPiutang_RJ'] || dataPiutang['dataPiutang_RI']);
            setTitleText("Pasien Rawat Jalan");
        }
    };
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="BPJS Belum Terklaim" />
            <div className="bg-base-200">
                <div className="py-20 grid grid-cols-1 items-center gap-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="w-full">
                        <div className="p-4 flex">
                            <div className="flex-1 animate__animated animate__fadeInUp animate__slow">
                                <h1 className="text-5xl">Klaim BPJS Yang Belum Terbayar</h1>
                            </div>
                            <div className="flex-1 text-center">
                                <div className="stats shadow">
                                    {dataPiutang['dataPiutang_RJ'] ? (
                                        <div className="stat">
                                            <div className="stat-figure text-secondary">
                                                <div className="avatar online">
                                                    <div className="w-16 rounded-full">
                                                        <img src='/img/pasien.jpg' />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="stat-value">{dataPiutang['dataPiutang_RJ'] && dataPiutang['dataPiutang_RJ'][0] ? dataPiutang['dataPiutang_RJ'].length : 0}</div>
                                            <div className="stat-title">Pasien RJ</div>
                                        </div>
                                    ) : ''}
                                    {dataPiutang['dataPiutang_RI'] ? (
                                        <div className="stat">
                                            <div className="stat-figure text-secondary">
                                                <div className="avatar online">
                                                    <div className="w-16 rounded-full">
                                                        <img src='/img/pasien.jpg' />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="stat-value">{dataPiutang['dataPiutang_RI'] && dataPiutang['dataPiutang_RI'][0] ? dataPiutang['dataPiutang_RI'].length : 0}</div>
                                            <div className="stat-title">Pasien RI</div>
                                        </div>
                                    ) : ''}
                                </div>
                            </div>
                        </div>
                        {dataPiutang['dataPiutang_RJ'] || dataPiutang['dataPiutang_RI'] ? (
                            <select className="select w-full max-w-xs rounded" onChange={handleFilterChange} defaultValue="">
                                <option disabled value="">Tampilkan berdasarkan</option>
                                {dataPiutang['dataPiutang_RI'] && <option value="Rawat Inap">Rawat Inap</option>}
                                {dataPiutang['dataPiutang_RJ'] && <option value="Rawat Jalan">Rawat Jalan</option>}
                            </select>
                        ) : ''}
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
                                            <h3 className='bg-base00 p-2'>{index + 1}. {item.PASIEN}</h3>
                                            <table className="table table-compact w-full">
                                                <thead>
                                                    <tr>
                                                        <th>RM</th>
                                                        <th>NOTRANS</th>
                                                        <th>TANGGAL</th>
                                                        <th>UNIT</th>
                                                        <th>PRODUK</th>
                                                        <th>KLS TARIF</th>
                                                        <th>DOKTER</th>
                                                        <th>JUMLAH</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {item.data.map((dataItem, dataIndex) => (
                                                        <tr key={dataIndex}>
                                                            <td>{dataItem.RM}</td>
                                                            <td>{dataItem.NOTRANS}</td>
                                                            <td>{dataItem.TANGGAL}</td>
                                                            <td>{dataItem.UNIT}</td>
                                                            <td>{dataItem.PRODUK}</td>
                                                            <td>{dataItem['KLS TARIF']}</td>
                                                            <td>{dataItem.DOKTER}</td>
                                                            <td className='text-end'>{dataItem.JUMLAH.toLocaleString('id-ID', {
                                                                maximumFractionDigits: 0,
                                                            })}</td>
                                                        </tr>
                                                    ))}
                                                    <tr>
                                                        <td colSpan="7" className="text-end">Jumlah:</td>
                                                        <td className="text-end bg-base-300">
                                                            {item.data.reduce(
                                                                (total, dataItem) => total + parseFloat(dataItem.JUMLAH),
                                                                0
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
