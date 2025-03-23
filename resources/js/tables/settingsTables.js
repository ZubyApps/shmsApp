import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import jszip, { forEach } from 'jszip';
import pdfmake from 'pdfmake';
import pdfFonts from './vfs_fontes'
DataTable.Buttons.jszip(jszip)
DataTable.Buttons.pdfMake(pdfmake)
pdfMake.vfs = pdfFonts;
$.fn.dataTable.Buttons.defaults.dom.button.className = 'btn';

const getSponsorCategoryTable = (tableId) => {
    const sponsorCategoryTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/sponsorcategory/load',
        orderMulti: true,
        search:true,
        searchDelay: 500,
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
        searchDelay: 500,
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
        searchDelay: 500,
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
        searchDelay: 500,
        columns: [
            {data:row => () => {
                return `<span class="text-primary"> ${row.name}</span>`
            }},
            {data: "description"},
            {data: row => row.visible ? '<span class="fw-bold text-success">true</span>' : '<span class="fw-bold text-danger">false</span>'},
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
        searchDelay: 500,
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

const getMedicationCategoryTable = (table) => {
    const medicationCategoryTable = new DataTable('#'+table, {
        serverSide: true,
        ajax:  '/medicationcategory/load',
        orderMulti: true,
        search:true,
        searchDelay: 500,
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
    return medicationCategoryTable
}

const getUnitDescriptionTable = (table) => {
    const unitDescriptionTable = new DataTable('#'+table, {
        serverSide: true,
        ajax:  '/unitdescription/load',
        orderMulti: true,
        search:true,
        // searchDelay: 500,
        columns: [
            {data:row => () => {
                return `<span class="text-primary"> ${row.longName}</span>`
            }},
            {data:row => () => {
                return `<span class="text-primary"> ${row.shortName}</span>`
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
                                <button class="ms-1 btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
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
                            <button class="ms-1 btn btn-outline-primary updateBtn tooltip-test" title="update" data-id="${ row.id }">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                        </div>
                    `
                    }
                           
                } 
                    }
        ]
    });
    return unitDescriptionTable
}

const getMarkedForTable = (table) => {
    const markedForTable = new DataTable('#'+table, {
        serverSide: true,
        ajax:  '/markedfor/load',
        orderMulti: true,
        search:true,
        searchDelay: 500,
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
    return markedForTable
}

const getWardTable = (table) => {
    const wardTable = new DataTable('#'+table, {
        serverSide: true,
        ajax:  '/ward/load',
        orderMulti: true,
        search:true,
        // searchDelay: 500,
        columns: [
            {data:row => () => {
                return `<span class="text-primary"> ${row.longName}</span>`
            }},
            {data:row => () => {
                return `<span class="text-primary"> ${row.shortName}</span>`
            }},
            {data: "bedNumber"},
            {data: "description"},
            {data: row => row.flag ? '<span class="fw-bold text-danger">Yes</span>' : 'No' },
            {data: "flagReason"},
            {data: "bill"},
            {data: "createdBy"},
            {data: "createdAt"},
            {data: "occupied"},
            {
                sortable: false,
                data: row => () => {
                         return `
                            <div class="dropdown ms-1">
                                <a class="btn btn-outline-primary tooltip-test text-decoration-none" title="options" data-bs-toggle="dropdown" href="" >
                                    <i class="bi bi-gear" role="button"></i>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="btn dropdown-item clearWardBtn" data-id="${ row.id }">
                                            <i class="bi bi-arrow-clockwise text-primary"></i> Clear Ward
                                        </a>
                                        <a class="btn dropdown-item updateBtn tooltip-test" title="update"  data-id="${ row.id }">
                                            <i class="bi bi-pencil-fill text-primary"></i> Update
                                        </a>
                                        <a class="btn dropdown-item deleteBtn tooltip-test ${row.count < 1 ? '' : 'd-none'}" title="delete"  data-id="${ row.id }">
                                            <i class="bi bi-x-circle-fill text-primary"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        `                           
                } 
                    }
        ]
    });
    return wardTable
}

const getOtherSettingsTable = (tableId) => {
    const otherSettingsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  '/admin/settings/load/othersettings',
        orderMulti: true,
        search:true,
        // searchDelay: 500,
        columns: [
            {data:row => () => {
                return `<span class="text-primary"> ${row.name}</span>`
            }},
            {data: row => () => {
                return `
                <div class="d-flex text-secondary">
                    <span class="btn btn-${row.value == 'Not set' ? 'white' : 'outline-primary'} optionSpan" data-name="${row.name}"> ${row.value == 0 ? 'Off' : row.value == 1 ? 'On' : row.value}</span>
                    ${row.name != 'Pre Search' ? `
                        <select class ="form-select form-select-md optionSelect d-none ms-1">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="25">25</option>
                            <option value="30">30</option>
                        </select>
                        ` :`
                        <select class ="form-select form-select-md optionSelect d-none ms-1">
                            <option value="">Select option</option>
                            <option value="1">On</option>
                            <option value="0">Off</option>
                        </select>
                    `}
                    
                </div>
                `
            }},
            {data: "desc"},
            // {data: "createdBy"},
            // {data: "createdAt"},
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
    return otherSettingsTable
}

export {getSponsorCategoryTable, getResourceStockDateTable, getResourceCategoryTable, getPayMethodTable, getExpenseCategoryTable, getMedicationCategoryTable, getUnitDescriptionTable, getMarkedForTable, getWardTable, getOtherSettingsTable}