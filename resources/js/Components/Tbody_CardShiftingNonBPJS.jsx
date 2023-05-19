
const Tbody_CardShiftingNonBPJS = ({ data }) => {
    const totalTarif = data.reduce((acc, item) => acc + item.jumlah, 0);
  return (
    <tbody>
      {data.map((item, index) => (
        <tr key={index}>
          <th>{item.rm}</th>
          <td>{item.no_transaksi}</td>
          <td>{item.pasien}</td>
          <td>{item.unit}</td>
          <td>{item.kls_tarif}</td>
          <td>{item.dokter}</td>
          <td className="text-right">{item.jumlah.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })}</td>
        </tr>
      ))}
      <tr>
        <td colSpan="6" className="text-center"><b>Total Pendapatan:</b></td>
        <td className="text-right  bg-base-200"><b>{totalTarif.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 })}</b></td>
      </tr>
    </tbody>
  );
};

export default Tbody_CardShiftingNonBPJS;
