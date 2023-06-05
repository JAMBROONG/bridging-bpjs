import CardServiceType from '@/Components/CardServiceType';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function SetServiceType({ auth, data, data_template }) {
    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Dashboard" />
            <div className="bg-base-200">
                <div className="hero py-12 bg-base-200">
                    <div className="hero-content w-full text-left">
                            <h1 className="text-5xl font-bold">Service Type</h1>
                    </div>
                </div>
                <div className="py-12 flex max-w-7xl mx-auto px-4 sm:px-6 lg:px-8  items-start">
                    <div className="p-10 flex-1">
                        <div className="mb-6">
                            <h2 className="text-2xl font-semibold mb-2">Fitur Penentuan Jenis Jasa</h2>
                            <ul className="list-disc ml-6 text-justify">
                                <li>Memperinci dan mengkategorikan setiap layanan atau sarana dalam suatu sistem.</li>
                                <li>Membagi setiap layanan atau sarana ke dalam kategori pelayanan atau sarana yang tepat.</li>
                                <li>Membantu menentukan kelas tarif yang sesuai dengan setiap layanan atau sarana berdasarkan data yang ada.</li>
                                <li>Contoh: Jika terdapat pelayanan "Operasi Besar 3" yang termasuk ke dalam kelas tarif "Operasi", fitur ini akan membantu menentukan bahwa pelayanan tersebut termasuk ke dalam kategori jasa pelayanan.</li>
                                <li>Memudahkan pengguna dalam memahami dan mengelompokkan setiap layanan atau sarana berdasarkan jenisnya, sehingga mempermudah pengaturan tarif dan manajemen data.</li>
                                <li>Mengoptimalkan proses penentuan jenis jasa dan mencegah kesalahan atau kekeliruan dalam pengkategorian.</li>
                                <li>Memberikan kejelasan dan transparansi dalam menentukan apakah suatu layanan masuk dalam kategori pelayanan atau sarana.</li>
                            </ul>
                        </div>
                    </div>
                    <CardServiceType data={data} data_template={data_template} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
