import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts.pdfMake.vfs;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const getSponsorCategoryTable = (tableId) => {
    const sponsorCategoryTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/sponsorcategory/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "name"},
            {data: "description"},
            {data: "consultationFee"},
            {data: "payClass"},
            {data: row => () =>{
                if (row.approval == 'false'){
                    return 'No'
                } else {
                    return 'Yes'
                }
                }},
            {data: row => row.billMatrix + "%"},
            {data: row => () => { 
                if (row.balanceRequired == 'false'){
                    return 'No'
                } else {
                    return 'Yes'
                }
            }
            },
            {data: "createdAt"},
            // {
            //     sortable: false,
            //     data: row => function () {
            //         if (row.count < 1) {
            //             return `
            //             <div class="d-flex flex-">
            //                 <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
            //                     <i class="bi bi-pencil-fill"></i>
            //                 </button>
            //                 <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
            //                     <i class="bi bi-trash3-fill"></i>
            //                 </button>
            //             </div>
            //         `
            //         } else {
            //             return `
            //             <div class="d-flex flex-">
            //                 <button class=" btn btn-outline-primary updateBtn" data-id="${ row.id }">
            //                 <i class="bi bi-pencil-fill"></i>
            //             </button>
            //             </div>
            //         `
            //         }
            //     }}
        ]
    });
    return sponsorCategoryTable
}

const getResourceStockDateTable = (tableId) => {
    const resourceStockDateTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/resourcestockdate/load',
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

const getPayMethodTable = () => {
    const resourceCategoryTable = new DataTable('#payMethodTable', {
        serverSide: true,
        ajax:  '/paymethod/load',
        orderMulti: true,
        search:true,
        columns: [
            {data:row => () => {
                return `<span class="text-primary"> ${row.name}</span>`
            }},
            {data: "description"},
            {data: "createdBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                    if (row.count < 1) {
                         return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    }
                           
                } 
                    }
        ]
    });
    return resourceCategoryTable
}

const getExpenseCategoryTable = (table) => {
    const resourceCategoryTable = new DataTable('#'+table, {
        serverSide: true,
        ajax:  '/expensecategory/load',
        orderMulti: true,
        search:true,
        columns: [
            {data:row => () => {
                return `<span class="text-primary"> ${row.name}</span>`
            }},
            {data: "description"},
            {data: "createdBy"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => () => {
                    if (row.count < 1) {
                         return `
                            <div class="d-flex flex-">
                                <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <button type="submit" class="ms-1 btn btn-outline-primary deleteBtn tooltip-test" title="delete" data-id="${ row.id }">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `
                    } else {
                        return `
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    }
                           
                } 
                    }
        ]
    });
    return resourceCategoryTable
}

export {getSponsorCategoryTable, getResourceStockDateTable, getResourceCategoryTable, getPayMethodTable, getExpenseCategoryTable}