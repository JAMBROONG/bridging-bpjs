import React from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

export default function TemplateKelasTarif({ data_template, setServiceTypes }) {
  const uniqueTemplates = Array.from(new Set(data_template.map(item => item.template)));

  const handleUseTemplate = (template) => {
    alert(123);
    axios
      .post('/use-template', { template })
      .then((response) => {
        setServiceTypes(response.data.data);

        toast.success('Data berhasil ditambahkan', {
          position: 'top-right',
          autoClose: 2000,
          hideProgressBar: false,
          closeOnClick: true,
          pauseOnHover: true,
          draggable: true,
          progress: undefined
        });
      })
      .catch((error) => {
        console.error(error);
      });
  };

  return (
    <div>
      {uniqueTemplates.map((template, index) => (
        <div key={index} tabIndex={0} className="collapse mb-3 collapse-arrow border border-base-300 bg-base-100 rounded-box">
          <div className="collapse-title text-xl font-medium">
            {template}
          </div>
          <div className="collapse-content">
            <div className="overflow-x-auto">
              <table className="table table-compact w-full">
                <thead>
                  <tr>
                    <td>Kelas Tarif</td>
                    <td>Jenis Jasa</td>
                    <td>Kategori Pendapatan</td>
                  </tr>
                </thead>
                <tbody>
                  {data_template
                    .filter(item => item.template === template)
                    .map((item, itemIndex) => (
                      <tr key={itemIndex}>
                        <td>{item.kelas_tarif}</td>
                        <td>{item.jenis_jasa}</td>
                        <td>{item.kategori_pendapatan.kategori}</td>
                      </tr>
                    ))}
                </tbody>
              </table>
            </div>
            <div className="flex justify-end mt-3">
              <a className='btn btn-sm btn-primary' onClick={() => handleUseTemplate(template)}>gunakan <i className='fa fa-head'></i></a>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
