import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const getAllStaffTable = (tableId) => {
    const allStaffTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/users/allstaff',
        orderMulti: true,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary'},
            {extend: 'csv', className: 'btn-primary'},
            {extend: 'excel', className: 'btn-primary'},
            {extend: 'pdfHtml5', className: 'btn-primary'},
            {extend: 'print', className: 'btn-primary'},
             ],
        search:true,
        searchDelay: 1000,
        columns: [
            {data: "name"},
            {data: "employed"},
            {data: row =>  `<span class="btn p-0 border-0 deleteDesignationBtn" data-id="${row.id}" data-designationid="${row.designationId}" >${row.designation ?? ''}</span>` },
            {data: "lastLogin"},
            {data: "lastLogout"},
            {data: "qualification"},
            {data: "username"},
            {data: "phone"},
            // {data: "address"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => function () {
                    if (!row.hasDesignation) {
                        if (row.guard && !row.superUser){
                            return ''
                        }
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary designationBtn tooltip-test" title="designation" data-id="${ row.id }" data-name="${row.name}">
                                <i class="bi bi-arrow-left-circle-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateUserBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button type="button" class="ms-1 btn btn-outline-primary deleteUserBtn tooltip-test" title="delete" data-id="${ row.id }">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>
                    `
                    } else {
                        if (row.guard && !row.superUser){
                            return ''
                        }
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary designationBtn tooltip-test" title="designation" data-id="${ row.id }" data-name="${row.name}">
                                <i class="bi bi-arrow-left-circle-fill"></i>
                            </button>
                            <button class="ms-1 btn btn-outline-primary updateUserBtn" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    }
                }}
        ]
    });
    return allStaffTable
}

const getActiveStaffTable = (tableId) => {
    const activeStaffTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/users/activestaff',
        orderMulti: true,
        search:true,
        columns: [
            {data: "loggedIn"},
            {data: "name"},
            {data: "designation"},
            {data: "phone"},
            {
                sortable: false,
                data: row => function () {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary logStaffOutBtn tooltip-test" title="logout" data-id="${ row.id }">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        </div>
                    `
                }}
        ]
    });
    return activeStaffTable
}

const getResourceStockDateTable = (tableId) => {
    const resourceStockDateTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/resourcestockdate/load',
        orderMulti: true,
        search:true,
        searchDelay: 1000,
        columns: [
            {data: "date"},
            {data: "description"},
            {data: "participants"},
            {data: "createdBy"},
            {data: "createdAt"},
            {data: row => function () {
                if (row.reset){
                    return `<span class="fs-italics text-primary"><i class="bi bi-check-circle-fill"></i> Stock reset</span>`
                } else {
                    return `
                    <div class="d-flex flex-">
                        <button class="btn btn-outline-primary resetResourceStockBtn tooltip-test" title="reset stock" data-id="${ row.id }">
                            <i class="bi bi-gear-wide-connected"></i>
                        </button>
                        <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                    </div>
                `
                }
            }},
        ]
    });
    return resourceStockDateTable
}

const getResourceCategoryTable = () => {
    const resourceCategoryTable = new DataTable('#resourceCategoryTable', {
        serverSide: true,
        ajax:  '/resourcecategory/load',
        orderMulti: true,
        searchDelay: 1000,
        search:true,
        columns: [
            {data:row => () => {
                return `<span class="text-primary"> ${row.name}</span>`
            }},
            {data: "description"},
            {data: "createdBy"},
            {data: "createdAt"},
            // {
            //     sortable: false,
            //     data: row => () => {
            //         if (row.count < 1) {
            //              return `
            //                 <div class="d-flex flex-">
            //                     <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
            //                         <i class="bi bi-pencil-fill"></i>
            //                     </button>
            //                     <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
            //                         <i class="bi bi-trash3-fill"></i>
            //                     </button>
            //                 </div>
            //             `
            //         } else {
            //             return `
            //             <div class="d-flex flex-">
            //                 <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
            //                     <i class="bi bi-pencil-fill"></i>
            //                 </button>
            //             </div>
            //         `
            //         }
                           
            //     } 
            //         }
        ]
    });
    return resourceCategoryTable
}

export {getAllStaffTable, getResourceStockDateTable, getResourceCategoryTable, getActiveStaffTable}