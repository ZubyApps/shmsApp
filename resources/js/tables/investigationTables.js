import DataTable from 'datatables.net-bs5';
import { admissionStatusX, detailsBtn, displayPaystatus, flagIndicator, flagPatientReason, searchMin, searchPlaceholderText, sponsorAndPayPercent, wardState } from "../helpers";

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
                        console.log(data.collected)
                        if (!data.collected){
                            row.classList.add('table-warning')
                            button.classList.remove('btn-primary')
                            button.classList.add('colour-change')
                            sampleCollectedCount.push(data.collected)
                        }
                        allCount.push(data.collected)         
                    },
        drawCallback: function (settings) {
            console.log(sampleCollectedCount.length)
            if (sampleCollectedCount.length){
                span.innerHTML = sampleCollectedCount.length + '/' + allCount.length
                sampleCollectedCount = []
                allCount = []
            } else {
                span.innerHTML = allCount.length
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
                            <button class=" btn btn-${row.collected ? 'primary' : 'warning'} sampleCollectedBtn tooltip-test" id="sampleCollectedBtn" title="Sample collected ${row.collected ? row.collectedBy : '?'}" data-id="${ row.id}" data-sampleCollected="${ row.collected}">
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

export {getPatientsVisitsByFilterTable, getInpatientsInvestigationsTable, getOutpatientsInvestigationTable}