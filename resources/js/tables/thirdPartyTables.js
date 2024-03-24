import jQuery from "jquery";
import $ from 'jquery';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import { admissionStatus, admissionStatusX, detailsBtn, displayPaystatus, sponsorAndPayPercent } from "../helpers";

const account = new Intl.NumberFormat('en-US', {currencySign: 'accounting'})

const getlistOfServicesTable = (tableId) => {
    const preparedColumns = [
        {data: "date"},
        {data: "thirdParty"},
        {data: row => function () {
            const credit = row.sponsorCategoryClass == 'Credit'
            const NHIS = row.sponsorCategory == 'NHIS'
            return `<span class="text-primary fw-semibold">${row.resource +' '+ displayPaystatus(row, credit, NHIS)}</span>`
            }
        },
        {data: "patient"},
        {data: "doctor"},
        {data: "diagnosis"},
        {data: row => sponsorAndPayPercent(row)},
        {data: row => admissionStatusX(row)},
        {data: row => account.format(row.hmsBill)},
        {
            sortable: false,
            data: row => function () {
                    return `
                    <div class="d-flex flex-">
                        <button type="submit" class="ms-1 btn btn-outline-primary deleteThirPartyServiceBtn tooltip-test ${row.user ? '' : 'd-none'}" title="delete" data-id="${ row.id }">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                `
            }}
    ]

    return new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/thirdpartyservices/load/list', data: {
        }},
        orderMulti: true,
        search:true,
        language: {
            emptyTable: 'No Third Party Services'
        },
        columns: preparedColumns
    });
}

const getThirdPartiesTable = (tableId, notLab) => {
    const thirdPartiesTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/thirdparties/load/thirdparties',
        paging: true,
        orderMulti: false,
        language: {
            emptyTable: 'No Third Party'
        },
        rowCallback: (row, data) => {
            if (data.delisted) {
                row.classList.add('table-danger')
            }
            return row
        },
        columns: [
            {data: "fullName"},
            {data: "shortName"},
            {data: "phone"},
            {data: "address"},
            {data: "email"},
            {data: "comment"},
            {data: "createdAt"},
            {data: "createdBy"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-${row.delisted ? 'danger' : 'primary'} delistBtn tooltip-test" title="delist third party" data-id="${ row.id }">
                                ${row.delisted ? '<i class="bi bi-x-square-fill tooltip-test" title="delisted"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="listed"></i>'}
                            </button>
                            <button class="ms-1 btn btn-outline-${row.delisted ? 'danger' : 'primary'} updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="submit" class="ms-1 btn btn-outline-${row.delisted ? 'danger' : 'primary'} deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                    } else {
                        return `
                        <div class="d-flex flex-">
                        <button class=" btn btn-outline-${row.delisted ? 'danger' : 'primary'} delistBtn tooltip-test" title="delist third party" data-id="${ row.id }">
                            ${row.delisted ? '<i class="bi bi-x-square-fill tooltip-test" title="delisted"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="listed"></i>'}
                        </button>
                            <button class="ms-1 btn btn-outline-primary updateBtn" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    }
                }}
        ]
    });

    return thirdPartiesTable
}

// const getOutpatientsInvestigationTable = (tableId, notLab) => {
//     const investigationsTable =  new DataTable('#'+tableId, {
//         serverSide: true,
//         ajax:  {url: '/investigations/load/outpatients', data: {
//             'notLab': notLab
//         }},
//         paging: true,
//         orderMulti: false,
//         language: {
//             emptyTable: 'No lab investigation requested'
//         },
//         columns: [
//             {data: "requested"},
//             {data: "type"},
//             {data: "doctor"},
//             {data: "patient"},
//             {data: "diagnosis"},
//             {data: row => function () {
//                 const credit = row.sponsorCategoryClass == 'Credit'
//                 const NHIS = row.sponsorCategory == 'NHIS'
//                 return `<span class="text-primary fw-semibold">${row.resource +' '+ displayPaystatus(row, credit, NHIS)}</span>`
//                 }
//             },
//             {
//                 sortable: false,
//                 data: row => function () {
//                     if (row.result){
//                         return `
//                             <div class="d-flex flex-">
//                                 <button class="btn btn-primary resultAddedBtn tooltip-test" title="result added">
//                                 <i class="bi bi-check-circle-fill"></i>
//                                 </button>
//                             </div>`
//                         } else {
//                             return `
//                             <div class="d-flex flex- ${notLab ? 'd-none' : ''}">
//                                 <button class="btn btn-outline-primary addResultBtn tooltip-test" id="addResultBtn" title="add result" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }">
//                                     <i class="bi bi-plus-square"></i> Add Result
//                                 </button>
//                             </div>
//                             `
//                         }
//                     }  
//             },
//         ]
//     });

//     return investigationsTable
// }

export {getlistOfServicesTable, getThirdPartiesTable}