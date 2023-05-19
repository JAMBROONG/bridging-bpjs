export default function TableKlaimBPJS(props) {
    return (
        <div className="">
            <div className="alert shadow-lg rounded p-2 mb-3">
                <div>
                    <span>Menampilkan data Pasien BPJS</span>
                </div>
            </div>
            <table {...props} className='table table-compact w-full'>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tgl. Masuk</th>
                        <th>Tgl. Pulang</th>
                        <th>No. RM</th>
                        <th>Nama Pasien</th>
                        <th>No. Klaim / SEP</th>
                        <th>INACBG</th>
                        <th>Top Up</th>
                        <th>Total Tarif</th>
                        <th>Tarif RS</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody>
                    {props.data.map((item, index) => (
                        <tr key={index}>
                            <th>{item.no}</th>
                            <td>{item.tgl_masuk}</td>
                            <td>{item.tgl_pulang}</td>
                            <td>{item.no_rm}</td>
                            <td>{item.nama_pasien}</td>
                            <td>{item.no_klaim_sep}</td>
                            <td>{item.inacbg}</td>
                            <td>{item.top_up}</td>
                            <td>{item.total_tarif}</td>
                            <td>{item.tarif_rs}</td>
                            <td>{item.jenis}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
