import React from 'react';
import Tbody_CardShiftingNonBPJS from './Tbody_CardShiftingNonBPJS';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import * as XLSX from 'xlsx';


export default function CardShiftingNonBPJS(props) {
    const groupedData = {};
    if (props.data) {
        props.data.forEach((item) => {
            const key = `${item.rm}-${item.pasien}`;
            if (!groupedData[key]) {
                groupedData[key] = [];
            }
            groupedData[key].push(item);
        });
    }
    const handleExportPdf = () => {
        const tables = document.querySelectorAll('.table');
        const doc = new jsPDF({ orientation: 'landscape' }); // Mengatur posisi PDF menjadi landscape

        Object.keys(groupedData).forEach((key, index) => {

            html2canvas(tables[index], { scrollY: -window.scrollY }).then((canvas) => {
                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 280; // Lebar gambar dalam PDF
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                doc.addImage(imgData, 'PNG', 10, 20, imgWidth, imgHeight); // Menambahkan gambar tabel ke PDF

                if (index < tables.length - 1) {
                    doc.addPage();
                } else {
                    doc.save('report.pdf');
                }
            });
        });
    };
    const handleExportExcel = () => {
        // Membuat workbook baru
        const wb = XLSX.utils.book_new();

        // Mengumpulkan semua baris untuk semua tabel dalam satu array
        const allRows = [];

        // Loop melalui setiap grup data
        Object.keys(groupedData).forEach((key) => {
            // Menambahkan nama pasien sebagai baris
            allRows.push(["Pendapatan Pasien", groupedData[key][0].pasien]);

            // Mengkonversi data ke bentuk tabel Excel
            const ws_data = [
                ['RM', 'NOTRANS', 'PASIEN', 'UNIT', 'KLS TARIF', 'DOKTER', 'JUMLAH'],
                ...groupedData[key].map(item => [item.rm, item.no_transaksi, item.pasien, item.unit, item.kls_tarif, item.dokter, item.jumlah])
            ];

            // Menambahkan baris dari tabel ini ke array semua baris
            allRows.push(...ws_data);

            // Menambahkan baris jumlah
            const jumlah = groupedData[key].reduce((total, item) => total + item.jumlah, 0);
            allRows.push(['', '', '', '', '', 'Jumlah:', jumlah]);
            
            // Menambahkan baris kosong sebagai jarak antara tabel
            allRows.push([]);
        });

        // Mengkonversi semua baris ke worksheet
        const ws = XLSX.utils.aoa_to_sheet(allRows);

        // Menambahkan worksheet ke workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

        // Membuat file Excel
        XLSX.writeFile(wb, "report.xlsx");
    };
    return (
        <div className="">
            <div className="flex justify-between p-3 items-center">
                <div className="">Data Pendapatan Pasien UMUM</div>
                <div className="btn-group">
                    <button className="btn btn-sm" onClick={handleExportPdf} ><i className='fa fa-download mr-2'></i>Pdf</button>
                    <button className="btn btn-sm" onClick={handleExportExcel}><i className='fa fa-download mr-2'></i>Excel</button>
                </div>
            </div>
            {Object.keys(groupedData).map((key) => (
                <div key={key} className="mb-3 overflow-x-auto shadow-md rounded p-3">
                    <div className="rounded p-2 bg-base-300 mb-2">
                        <span>Pendapatan Pasien <b>{groupedData[key][0].pasien}</b></span>
                    </div>
                    <table className="table table-compact w-full">
                        <thead>
                            <tr>
                                <th>RM</th>
                                <th>NOTRANS</th>
                                <th>PASIEN</th>
                                <th>UNIT</th>
                                <th>KLS TARIF</th>
                                <th>DOKTER</th>
                                <th>JUMLAH</th>
                            </tr>
                        </thead>
                        {groupedData[key] && (
                            <Tbody_CardShiftingNonBPJS data={groupedData[key]} />
                        )}
                    </table>
                </div>
            ))}
        </div>
    );
}
