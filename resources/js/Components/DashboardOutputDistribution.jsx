import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';

export default function DashboardOutputDistribution(file) {
    const [typedText, setTypedText] = useState('Data berhasil diolah, berikut beberapa data yang bisa saya olah:');
    useEffect(() => {
        setTypedText('');
        const textToType = 'Data berhasil diolah, berikut beberapa data yang bisa saya olah:';
        let currentIndex = -1;

        const typingInterval = setInterval(() => {
            setTypedText((prevText) => prevText + textToType[currentIndex]);
            currentIndex++;

            if (currentIndex === textToType.length - 1) {
                clearInterval(typingInterval);
            }
        }, 10); // Waktu antara setiap karakter yang ditambahkan (dalam milidetik)

        return () => {
            clearInterval(typingInterval);
        };
    }, []);

    const shouldDisplayData = typedText.length === 'Data berhasil diolah, berikut beberapa data yang bisa saya olah:'.length;

    return (
        <div className="">
            <div className="flex justify-between p-3 items-center">
                <h3>{typedText}</h3>
            </div>
            {shouldDisplayData && (
                <>
                    <div className="flex">
                        <div className="flex-1 p-2">
                            <Link href={route('piutang-bpjs-tak-tertagih')} className="btn  btn-block" as="button">
                                Klaim BPJS Yang Belum Terbayar
                            </Link>
                        </div>
                        {/* <div className="flex-1 p-2">
                            <a className="btn  btn-block">Pendapatan RS Rawat Inap</a>
                        </div> */}
                    {/* </div>
                    <div className="flex"> */}
                        {/* <div className="flex-1 p-2">
                            <a className="btn  btn-block bg-base-300">Pendapatan RS Rawat Jalan</a>
                        </div> */}
                        <div className="flex-1 p-2">
                            <Link href={route('data-distribution')} className="btn  btn-block" as="button">
                                Seluruh Data Hasil Allocation and Distribution
                            </Link>
                        </div>
                    </div>
                </>
            )}
            <div className='flex p-2'>
                <Link href={route('file.clear')} className='btn btn-sm btn-error'>
                    <i className='fa fa-upload mr-2'></i> unggah ulang file
                </Link>
            </div>
        </div>
    );
}
