import jQuery from "jquery";
import jszip from 'jszip';
import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';

const getAllStaffTable = (tableId) => {
    const allStaffTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/users/load',
        orderMulti: true,
        search:true,
        columns: [
            {data: "name"},
            {data: "employed"},
            {data: row =>  `<span class="btn p-0 border-0 deleteDesignationBtn" data-id="${row.designationId}" >${row.designation ?? ''}</span>` },
            {data: "lastLogin"},
            {data: "qualification"},
            {data: "username"},
            {data: "phone"},
            {data: "address"},
            {data: "createdAt"},
            {
                sortable: false,
                data: row => function () {
                    if (row.count < 1) {
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
                        return `
                        
                        <div class="d-flex flex-">
                            <button class=" btn btn-outline-primary designationBtn tooltip-test" title="designation" data-id="${ row.id }" data-name="${row.name}">
                                <i class="bi bi-arrow-left-circle-fill"></i>
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
    return allStaffTable
}

const getResourceStockDateTable = (tableId) => {
    const resourceStockDateTable = new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/resourcestockdate/load',
        orderMulti: true,
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

export {getAllStaffTable, getResourceStockDateTable, getResourceCategoryTable}