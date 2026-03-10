import $ from 'jquery';
import DataTable from 'datatables.net-bs5';
import { admissionStatusX, detailsBtn, displayPaystatus, flagIndicator, flagPatientReason, pendingIndicator, searchMin, searchPlaceholderText, sponsorAndPayPercent, wardState } from "../helpers";

const getPatientsVisitsByFilterTable = (tableId, filter) => {
    const preparedColumns = [
        {data: "came"},
        {data: row => `<span class="${flagIndicator(row.flagPatient)} tooltip-test" title="${flagPatientReason(row)}" >${row.patient}</span>`},
        {data: "doctor"},
        {data: "diagnosis"},
        {data: row => sponsorAndPayPercent(row)},
        {data: row =>  `
                    <div class="d-flex flex-">
                        <button class=" btn btn-outline-primary investigationsBtn tooltip-test" title="View Investigations" data-id="${ row.id }" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                            ${row.labPrescribed}<i class="bi bi-eyedropper"></i>${row.labDone}
                        </button>
                        <button class="ms-1 btn btn-${row.isOnList ? 'outline-' : ''}primary sendToListBtn tooltip-test" title="Send To List" data-id="${ row.id }" data-patient="${ row.patient }">
                           <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>`                
        },
        {data: row => admissionStatusX(row)},
        {
            sortable: false,
            data: row => detailsBtn(row) 
        },
    ]
    filter === 'Inpatient' ? preparedColumns.splice(7, 0, {data: row => wardState(row)},) : ''

    const allPatientsTable = new DataTable(tableId, {
        serverSide: true,
        ajax:  {url: '/investigations/load/consulted', data: {
            'filterBy': filter
        }},
        orderMulti: true,
        search:true,
        searchDelay: 500,
        language: {
            emptyTable: 'No patient record',
            searchPlaceholder: searchPlaceholderText
        },
        columns: preparedColumns
    });

    allPatientsTable.on('draw.init', searchMin(allPatientsTable, tableId, 2))

    return allPatientsTable
}

const getInpatientsInvestigationsTable = (tableId, notLab, button, span) => {
    let sampleCollectedCount = []
    let allCount = []
    const investigationsTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  '/investigations/load/inpatients',
        paging: true,
        orderMulti: false,
        searchDelay: 500,
        lengthMenu:[25, 50, 100, 200],
        language: {
            emptyTable: 'No lab investigation requested'
        },
        rowCallback: (row, data) => {
                        if (!data.collected){
                            row.classList.add('table-warning')
                            button.classList.remove('btn-primary')
                            button.classList.add('colour-change')
                            sampleCollectedCount.push(data.collected)
                        }
                        allCount.push(data.collected)         
                    },
        drawCallback: function (settings) {
            if (sampleCollectedCount.length){
                span.innerHTML = sampleCollectedCount.length + '/' + allCount.length
                sampleCollectedCount = []
                allCount = []
            } else {
                span.innerHTML = allCount.length ?? ''
                allCount = []
                button.classList.add('btn-primary')
                button.classList.remove('colour-change')
            }
        },
        columns: [
            {data: "requested"},
            {data: "type"},
            {data: "doctor"},
            {data: "patient"},
            {data: "diagnosis"},
            {data: row => function () {
                const credit = row.sponsorCategoryClass == 'Credit'
                const NHIS = row.sponsorCategory == 'NHIS'
                return `<span class="text-primary fw-semibold">${row.resource +' '+ displayPaystatus(row, credit, NHIS)}</span>`
                }
            },
            {
                sortable: false,
                data: row =>  `
                        <div class="d-flex flex- ${notLab ? 'd-none' : ''}">
                            <button class=" btn btn-${row.collected ? 'primary' : 'warning'} ${row.collected ? 'unMarkSampleCollectedBtn' : 'markSampleCollectedBtn'} tooltip-test" title="Sample collected ${row.collected ? 'by ' + row.collectedBy + ' at ' + row.collected : '?'}" data-id="${ row.id}" data-sampleCollected="${ row.collected}">
                                <i class="bi bi-check"></i>
                            </button>
                            <button class=" btn btn-primary addResultBtn tooltip-test ms-1" id="addResultBtn" title="add result" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                <i class="bi bi-plus-square"></i>
                            </button>
                            <button class="btn btn-primary removeTestBtn tooltip-test ms-1" id="removeTestBtn" title="remove test" data-id="${row.id}" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                <i class="bi bi-dash-square"></i>
                            </button>
                        </div>
                `   
            },
        ]
    });

    return investigationsTable
}

const getOutpatientsInvestigationTable = (tableId, notLab) => {
    const investigationsTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/investigations/load/outpatients', data: {
            'notLab': notLab
        }},
        paging: true,
        orderMulti: false,
        searchDelay: 500,
        lengthMenu:[25, 50, 100, 200],
        language: {
            emptyTable: 'No lab investigation requested'
        },
        columns: [
            {data: "requested"},
            {data: "type"},
            {data: "doctor"},
            {data: "patient"},
            {data: "diagnosis"},
            {data: row => function () {
                const credit = row.sponsorCategoryClass == 'Credit'
                const NHIS = row.sponsorCategory == 'NHIS'
                return `<span class="text-primary fw-semibold">${row.resource +' '+ displayPaystatus(row, credit, NHIS)}</span>`
                }
            },
            {
                sortable: false,
                data: row => function () {
                    if (row.result){
                        return `
                            <div class="d-flex flex-">
                                <button class="btn btn-primary resultAddedBtn tooltip-test" title="result added">
                                <i class="bi bi-check-circle-fill"></i>
                                </button>
                            </div>`
                        } else {
                            return `
                            <div class="d-flex flex- ${notLab ? 'd-none' : ''}">
                                <button class="btn btn-primary addResultBtn tooltip-test" id="addResultBtn" title="add result" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                    <i class="bi bi-plus-square"></i>
                                </button>
                                <button class="btn btn-primary removeTestBtn tooltip-test ms-1" id="removeTestBtn" title="remove test" data-id="${row.id}" data-investigation="${row.resource}" data-table="${tableId}" title="add result" data-id="${ row.id}" data-diagnosis="${ row.diagnosis}" data-patient="${ row.patient }" data-sponsor="${ row.sponsor }" data-sponsorcat="${row.sponsorCategory}">
                                    <i class="bi bi-dash-square"></i>
                                </button>
                            </div>
                            `
                        }
                    }  
            },
        ]
    });

    return investigationsTable
}

const getInvestigationsListTable = (tableId, date) => {
    const investigationsListTable =  new DataTable('#'+tableId, {
        serverSide: true,
        ajax:  {url: '/investigationslist/load/list', data: {
            'date': date
        }},
        paging: true,
        // select: true,
        orderMulti: false,
        searchDelay: 500,
        lengthMenu:[20, 50, 100, 150, 200],
        language: {
            emptyTable: 'No investigations listed'
        },
        rowCallback: (row, data) => {
                row.classList.add('table-light')
                if (data.status == 3){
                    row.classList.add('row-disabled')
                }
            return row
        },
        columns: [
            {data: "qNumber"},
            {data: "createdAt"},
            {data: row => `<div class="fw-semibold text-primary voidEntryBtn" data-id="${row.id}" data-patient="${row.patient}" data-status="${row.status}" >${row.patient}</div>`},
            {data: row => `<div class="text-primary-emphasis fw-semibold">${row.sponsor + '-' + row.sponsorCategory}</div>`},
            {data: "queueBy"},
            {data: row => admissionStatusX(row)},
            {data: row => wardState(row)}
        ]
    });

    function formatChild(data) {
            const prescriptions = data.prescriptions
                if (prescriptions.length > 0) {
                    let child = `   
                                <table class="table align-middle table-sm">
                                            <thead >
                                                <tr class="fs-italics">
                                                    <th class="text-secondary">Investigation</th>
                                                    <th class="text-secondary">HMO Approval</th>
                                                    <th class="text-secondary">Requested By</th>
                                                    <th class="text-secondary">Requested</th>
                                                    <th class="text-secondary">Actions</th>
                                                </tr>
                                            </thead>`
                                    prescriptions.forEach(p => {
                                        child += `<tbody>
                                                        <tr>
                                                            <td class="text-secondary">${`<span class="text-${p.rejected ? 'danger' : 'primary'}">${p.resource + ' ' + displayPaystatus(p, (p.payClass == 'Credit'), (p.sponsorCategory == 'NHIS')) }</span>`}</td>
                                                            <td class="text-secondary">${`<div class="text-primary fw-normal fst-italic">${p.hmoNote ? p.statusBy+'-'+p.hmoNote + pendingIndicator(p): p.statusBy}</div>`}</td>
                                                            <td class="text-secondary">${p.dr}</td>
                                                            <td class="text-secondary">${p.requested}</td>
                                                            <td class="text-secondary">${`
                                                            <div class="dropdown">
                                                                <i class="text-primary bi bi-gear" role="button" data-bs-toggle="dropdown"></i>

                                                                <ul class="dropdown-menu">
                                                                    <li class="${p.sent ? 'd-none' : ''}">
                                                                        <a class="btn btn-outline-primary dropdown-item addResultBtn" id="addResultBtn" data-investigation="${p.resource}" data-patient="${ data.patient }" data-sponsor="${ data.sponsor }" data-sponsorcat="${ data.sponsorCategory }" title="add result" data-id="${ p.id}" data-diagnosis="${ data.diagnosis}">
                                                                            <i class="bi bi-plus-square"></i> Add Result
                                                                        </a>
                                                                    </li>
                                                                    <li  class="${!p.sent ? 'd-none' : ''}">
                                                                        <a class="btn btn-outline-primary dropdown-item updateResultBtn" id="updateResultBtn" data-investigation="${p.resource}" data-patient="${ data.patient }" data-sponsor="${ data.sponsor }" data-sponsorcat="${ data.sponsorCategory }"  title="update result" data-id="${ p.id}" data-diagnosis="${ data.diagnosis}">
                                                                            <i class="bi bi-pencil-fill"></i> Update Result
                                                                        </a>
                                                                    </li>
                                                                    <li class="${!p.sent ? 'd-none' : ''}">
                                                                        <a class="btn dropdown-item deleteResultBtn" title="delete" data-id="${ p.id}" data-diagnosis="${ p.diagnosis}">
                                                                            <i class="bi bi-trash3-fill btn btn-primary"></i> Delete Result
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                                <i class="bi bi-check-square-fill ms-1 text-primary ${p.result ? '' : 'd-none'}" title="${'result added ' + p.sent}"></i>
                                                            </div>
                                                    `   }</td>
                                                        </tr>   
                                                </tbody>`
                                    })
                                        
                                child += `</table>`
                        return (child);
                } else {
                   return  `
                                <table class="table align-middle table-sm">
                                        <tr>
                                            <td align="center" colspan="8" class="text-secondary">
                                                No Investigations
                                            </td>
                                        </tr>
                                    </table>
                            `
                }
            }

    investigationsListTable.on('draw', function() {
            // const tableId = investigationsListTable.table().container().id.split('_')[0]
            
            investigationsListTable.rows().every(function () {
                let tr = $(this.node())
                let row = this.row(tr);
                if (row.data().status != 3){
                    this.child(formatChild(row.data(), tableId)).show()
                }
            })
        })

    return investigationsListTable
}

export {getPatientsVisitsByFilterTable, getInpatientsInvestigationsTable, getOutpatientsInvestigationTable, getInvestigationsListTable}