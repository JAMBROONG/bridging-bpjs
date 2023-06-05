import InfiniteScroll from "react-infinite-scroll-component";
import React, { useState } from "react";

export default function TabelShiftingJasaPelayananBPJS({ data }) {
  const [hasMore, setHasMore] = useState(true);
  const [dataToShow, setDataToShow] = useState(10);
  const [dataPage, setDataPage] = useState(data);

  const loadMoreData = () => {
    if (dataToShow >= dataPage.length) {
      setHasMore(false);
      return;
    }
    setDataToShow(dataToShow + 10);
  };

  return (
    <div>
      <div className="flex"></div>
      {dataPage && dataPage[0] ? (
        <InfiniteScroll
          dataLength={dataToShow}
          next={loadMoreData}
          hasMore={hasMore}
          loader={<h4>Loading ...</h4>}
          endMessage={<p>No more data to load.</p>}
        >
          {dataPage.slice(0, dataToShow).map((item, index) => (
            <div className="" key={index}>
              <div className="p-2 rounded bg-current  mb-2 mt-10 text-sm">
                <div className="text-base-300">
                  {item['KLS TARIF']}
                </div>
              </div>
              {item.DATA.map((dataItem, dataIndex) => (
                <div className="card bg-current mt-5 p-3 rounded" key={dataIndex}>
                  <div className="flex">
                    <div className="w-1/3 p-2  text-sm text-base-300 flex-1">
                      Pasien: <b>{dataItem.PASIEN}</b>
                      <br />
                      No. RM: <b>{dataItem.RM}</b>
                      <br />
                      INACBG: <b>{dataItem.INACBG}</b>
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
                      {dataItem.data.map((dataPasiens, dataPasiensIndex) => (
                        <tr key={dataPasiensIndex}>
                          <td>{dataPasiens.NOTRANS}</td>
                          <td>{dataPasiens["PRODUK"]}</td>
                          <td>{dataPasiens.DOKTER}</td>
                          <td className="text-end">
                            {dataPasiens.JUMLAH.toLocaleString("id-ID", {
                              maximumFractionDigits: 0,
                            })}
                          </td>
                          <td className="text-end">
                            {(
                              (dataPasiens.JUMLAH /
                                dataItem.data.reduce(
                                  (total, dataPasiens) =>
                                    total + parseInt(dataPasiens.JUMLAH),
                                  0
                                )) *
                              100
                            ).toFixed(2)}%
                          </td>
                          <td className="text-end">
                            {dataItem['Data Konversi'][dataPasiensIndex]['Jumlah_Konversi'].toLocaleString("id-ID", {
                              maximumFractionDigits: 0,
                            })}
                          </td>
                        </tr>
                      ))}
                      <tr>
                        <td colSpan="3" className="text-end">
                          Jumlah:
                        </td>
                        <td className="text-end bg-base-300">
                          {dataItem.data.reduce(
                            (total, dataPasiens) =>
                              total + parseInt(dataPasiens.JUMLAH),
                            0
                          ).toLocaleString("id-ID", {
                            maximumFractionDigits: 0,
                          })}
                        </td>
                        <td className="text-end">100%</td>
                        <td className="text-end bg-base-300">
                          {dataItem['Data Konversi'].reduce(
                            (total, konversiItem) =>
                              total +
                              parseFloat(konversiItem.Jumlah_Konversi),
                            2
                          ).toLocaleString("id-ID", {
                            maximumFractionDigits: 0,
                          })}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              ))}
            </div>
          ))}
        </InfiniteScroll>
      ) : (
        <div className="card bg-base-100 shadow-sm">
          <div className="card-header">
            Menampilkan data pasien yang tak tertagih
          </div>
          <div className="card-body"></div>
        </div>
      )}
    </div>
  );
}
