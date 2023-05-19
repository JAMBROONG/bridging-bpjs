export default function TablePendapatanRsRi(props) {
    return (
        <table className='table table-compact w-full'>
            <thead>
                <tr>
                    <th>RM</th>
                    <th>NOTRANS</th>
                    <th>TANGGAL</th>
                    <th>PASIEN</th>
                    <th>UNIT</th>
                    <th>FAKTUR</th>
                    <th>PRODUK</th>
                    <th>KLS TARIF</th>
                    <th>OBAT</th>
                    <th>QTY</th>
                    <th>TARIP</th>
                    <th>JUMLAH</th>
                    <th>DOKTER</th>
                    <th>PENJAMIN</th>
                </tr>
            </thead>
            <tbody>
                {props.data.map((item, index) => (
                    <tr key={index}>
                        <th>{item.rm}</th>
                        <td>{item.no_transaksi}</td>
                        <td>{item.tanggal}</td>
                        <td>{item.pasien}</td>
                        <td>{item.unit}</td>
                        <td>{item.faktur}</td>
                        <td>{item.produk}</td>
                        <td>{item.kls_tarif}</td>
                        <td>{item.obat}</td>
                        <td>{item.qty}</td>
                        <td>{item.tarip}</td>
                        <td>{item.jumlah}</td>
                        <td>{item.dokter}</td>
                        <td>{item.penjamin}</td>
                    </tr>
                ))}
            </tbody>
        </table>
    );
}
