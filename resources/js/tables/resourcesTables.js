import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const getResourceSubCategoryTable = (tableId) => {
    const resourceSubCategoryTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/resourcesubcategory/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "name"},
            {data: "description"},
            {data: "category"},
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
            //                 <div class="d-flex flex-">
            //                     <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
            //                         <i class="bi bi-pencil-fill"></i>
            //                     </button>
            //                 </div>
            //             `
            //         }
                           
            //     } 
            //         }
        ]
    })
    return resourceSubCategoryTable
}

const getResourceTable = (tableId) => {
    const resourceTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/resources/load',
        orderMulti: true,
        search:true,
        fixedHeader: true,
        lengthMenu:[20, 40, 80, 200],
        dom: 'l<"my-1 text-center "B>frtip',
        buttons: [
            {
                extend:'colvis',
                text:'Show/Hide',
                className:'btn btn-primary'       
            },
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
        ],
        rowCallback: (row, data) => {
            if ( !data.isActive) {
                row.classList.add('table-danger')
            }
            if ( !data.stock) {
                row.classList.add('table-warning')
            }
            if ( data.stock <= data.reOrder) {
                row.classList.add('table-info')
            }
            if ( data.expired == 'Soon') {
                row.classList.add('table-secondary')
            }
            if ( data.expired == 'Yes') {
                row.classList.add('table-dark')
            }

            return row
        },
        columns: [
            {data: "name"},
            {data: "flag"},
            {
                visible: false,
                data: "category"
            },
            {data: "subCategory"},
            {data: "unit"},
            {data: "purchasePrice"},
            {data: "sellingPrice"},
            {data: "reOrder"},
            {data: "stock"},
            {
                visible: false,
                data: "expiryDate"
            },
            {data: row => () => {
                return row.expired
            }},
            {
                visible: false,
                data: "createdBy"
            },
            {
                visible: false,
                data: "createdAt"
            },
            {
                sortable: false,
                data: row => () => {
                    if (row.count < 1) {
                         return `
                        <div class="d-flex flex-">
                            <div>
                                <a class="btn btn-outline-${!row.isActive ? 'danger' : 'primary'} toggleActiveStatusBtn" data-id="${ row.id }">
                                ${!row.isActive ? '<i class="bi bi-x-square-fill  tooltip-test" title="inactive"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="active"></i>'}
                                </a>
                            </div>
                            <div class="dropdown ms-1">
                                <a class="btn btn-outline-${!row.isActive ? 'danger' : 'primary'} tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                <i class="bi bi-gear" role="button"></i>
                                </a>
                                    <ul class="dropdown-menu">
                                    <li>
                                        <a class="btn dropdown-item addStockBtn tooltip-test" title="add" data-id="${ row.id }">
                                            <i class="bi bi-plus-square text-primary"></i> Add stock
                                        </a>
                                        <a class="btn dropdown-item updateBtn tooltip-test" title="edit"  data-id="${ row.id }">
                                        <i class="bi bi-pencil-fill text-primary"></i> Edit
                                        </a>
                                        <a class="btn dropdown-item deleteBtn tooltip-test" title="delete"  data-id="${ row.id }">
                                            <i class="bi bi-x-circle-fill text-primary"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <div>
                                    <a class="btn btn-outline-${!row.isActive ? 'danger' : 'primary'} toggleActiveStatusBtn" data-id="${ row.id }">
                                    ${!row.isActive ? '<i class="bi bi-x-square-fill tooltip-test" title="inactive"></i>' : '<i class="bi bi-check-square-fill tooltip-test" title="active"></i>'}
                                    </a>
                            </div>
                            <div class="dropdown ms-1">
                                <a class="btn btn-outline-${!row.isActive ? 'danger' : 'primary'} tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown"  >
                                <i class="bi bi-gear" role="button"></i>
                                </a>
                                    <ul class="dropdown-menu">
                                    <li>
                                        <a class="btn dropdown-item addStockBtn tooltip-test" title="add" data-id="${ row.id }">
                                            <i class="bi bi-plus-square text-primary"></i> Add stock
                                        </a>
                                        <a class="btn dropdown-item updateBtn tooltip-test" title="edit" data-id="${ row.id }">
                                        <i class="bi bi-pencil-fill text-primary"></i> Edit
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        `
                    }
                           
                } 
                    }
        ]
    });
    return resourceTable
}

const getAddResourceStockTable = () => {
    const addResourceStockTable = new DataTable('#addResourceStockTable', {
        serverSide: true,
        ajax:  '/addresourcestock/load',
        orderMulti: true,
        search:true,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        columns: [
            {data: "resource"},
            {data: "qty"},
            {data: "purchasePrice"},
            {data: "sellingPrice"},
            {data: "expiryDate"},
            {data: "supplier"},
            {data: "createdBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                        return `
                            <div class="d-flex flex-">
                                <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `
                } 
            }
        ]
    });
    return addResourceStockTable
}

const getResourceSupplierTable = () => {
    const resourceSupplierTable = new DataTable('#resourceSupplierTable', {
        serverSide: true,
        ajax:  '/resourcesupplier/load',
        orderMulti: true,
        search:true,
        dom: 'lfrtip<"my-5 text-center "B>',
        buttons: [
            {extend: 'copy', className: 'btn-primary', footer: true},
            {extend: 'csv', className: 'btn-primary', footer: true},
            {extend: 'excel', className: 'btn-primary', footer: true},
            {extend: 'pdfHtml5', className: 'btn-primary', footer: true},
            {extend: 'print', className: 'btn-primary', footer: true},
             ],
        columns: [
            {data: "company"},
            {data: "person"},
            {data: "phone"},
            {data: "email"},
            {data: "address"},
            {data: "createdBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                        return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary editBtn tooltip-test" title="update" data-id="${ row.id }">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `
                } 
            }
        ]
    });
    return resourceSupplierTable
} 

export {getResourceSubCategoryTable, getResourceTable, getAddResourceStockTable, getResourceSupplierTable}