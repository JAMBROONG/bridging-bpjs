import React, { useState, useEffect } from 'react';

const TableInvoice = () => {
    const [isDarkMode, setIsDarkMode] = useState(false);

    const toggleTheme = () => {
        setIsDarkMode(!isDarkMode);
        // Logika lain yang diperlukan untuk mengubah tema Anda di sini
    };

    return (
        <div className="overflow-x-auto mt-5">
            <table className="table table-compact w-full">
                {/* head */}
                <thead>
                    <tr>
                        <th></th>
                        <th>Keterangan</th>
                        <th>Jenis Pembayaran</th>
                        <th>Tanggal Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    {/* row 1 */}
                    <tr>
                        <th>1</th>
                        <td>Pembayaran Langganan</td>
                        <td>BCA</td>
                        <td>12 September 2022</td>
                    </tr>
                    {/* row 2 */}
                    <tr className="hover">
                        <th>2</th>
                        <td>Pembayaran Langganan</td>
                        <td>BSI (Bank Syari'ah Indonesia)</td>
                        <td>10 September 2021</td>
                    </tr>
                </tbody>
            </table>
        </div>
    );
};

export default TableInvoice;
