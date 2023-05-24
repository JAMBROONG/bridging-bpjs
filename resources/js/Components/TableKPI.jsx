import React from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

export default function TableKPI() {

  return (
    <div className='mt-5'>
      <div className="flex bg-base-100 p-4 mb-4 rounded">
        <div className="text-sm">Data KPI</div>
        <button>Add data</button>
      </div>
      <div className="overflow-x-auto  animate__animated animate__fadeInUp animate__slow">
        <table className="table table-zebra w-full">
          {/* head */}
          <thead>
            <tr>
              <th></th>
              <th>Name</th>
              <th>Job</th>
              <th>Favorite Color</th>
            </tr>
          </thead>
          <tbody>
            {/* row 1 */}
            <tr>
              <th>1</th>
              <td>Cy Ganderton</td>
              <td>Quality Control Specialist</td>
              <td>Blue</td>
            </tr>
            {/* row 2 */}
            <tr>
              <th>2</th>
              <td>Hart Hagerty</td>
              <td>Desktop Support Technician</td>
              <td>Purple</td>
            </tr>
            {/* row 3 */}
            <tr>
              <th>3</th>
              <td>Brice Swyre</td>
              <td>Tax Accountant</td>
              <td>Red</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  );
}
