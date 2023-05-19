import React from 'react';
import Tbody_CardShiftingNonBPJS from './Tbody_CardShiftingNonBPJS';
import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';

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
  const handlePrint = () => {
    const tables = document.querySelectorAll('.table');
    const doc = new jsPDF({ orientation: 'landscape' }); // Mengatur posisi PDF menjadi landscape
  
    Object.keys(groupedData).forEach((key, index) => {
      const patientName = groupedData[key][0].pasien; // Mengambil nama pasien berdasarkan indeks tabel
  
      // Tambahkan teks nama pasien di atas tabel di PDF
      doc.text(`Nama Pasien: ${patientName}`, 10, 10);
  
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
  
  
  
  return (
    <div className="">
      <div className="flex justify-between p-3">
        <div className="">
            Data Pendapatan Pasien UMUM</div>
        <button onClick={handlePrint} className='btn btn-sm btn-primary'>PDF <i className='fa fa-print ml-2'></i></button>
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
