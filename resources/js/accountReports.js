import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getPayMethodsSummmaryTable, getCapitationPaymentsTable, getExpenseSummaryTable, getVisitSummaryTable1, getVisitSummaryTable2, getByPayMethodsTable, getVisitsBySponsorTable, getYearlyIncomeAndExpenseTable, getTPSSummaryTable, getTPSByThirdPartyTable, getYearlyIncomeAndExpenseTable2, getYearlyIncomeAndExpenseTable3 } from "./tables/accountReportTables";
import { getExpensesTable } from "./tables/billingTables";
import { clearDivValues, getDivData, openModals, resetFocusEndofLine } from "./helpers";
import { getYearlySummaryChart, getYearlySummaryChart2, getYearlySummaryChart3 } from "./charts/vitalsignsCharts";

window.addEventListener('DOMContentLoaded', function () {
    const byPayMethodModal           = new Modal(document.getElementById('byPayMethodModal'))
    const newExpenseModal            = new Modal(document.getElementById('newExpenseModal'))
    const updateExpenseModal         = new Modal(document.getElementById('updateExpenseModal'))
    const byExpenseCategoryModal     = new Modal(document.getElementById('byExpenseCategoryModal'))
    const visitsBySponsorModal       = new Modal(document.getElementById('visitsBySponsorModal'))
    const TPSByThirdPartyModal       = new Modal(document.getElementById('TPSByThirdPartyModal'))
    const payDirectModal             = new Modal(document.getElementById('payDirectModal'))

    const payMethodDiv               = document.querySelector('.payMethodDiv')
    const capitationDatesDiv         = document.querySelector('.capitationDatesDiv')
    const TPSSummaryDatesDiv         = document.querySelector('.TPSSummaryDatesDiv')
    const expenseSummaryDatesDiv     = document.querySelector('.expenseSummaryDatesDiv')
    const visistSummaryDiv1          = document.querySelector('.visistSummaryDiv1')
    const visistSummaryDiv2          = document.querySelector('.visistSummaryDiv2')
    const yearlyIncomeAndExpenseDiv  = document.querySelector('.yearlyIncomeAndExpenseDiv')
    const yearlyIncomeAndExpenseDiv2 = document.querySelector('.yearlyIncomeAndExpenseDiv2')
    const yearlyIncomeAndExpenseDiv3 = document.querySelector('.yearlyIncomeAndExpenseDiv3')
    const yearlySummaryChart         = document.querySelector('#yearlySummaryChart')
    const yearlySummaryChart2        = document.querySelector('#yearlySummaryChart2')
    const yearlySummaryChart3        = document.querySelector('#yearlySummaryChart3')

    const payMethodSummaryTab       = document.querySelector('#nav-payMethodSummary-tab')
    const capitationPaymentsTab     = document.querySelector('#nav-capitationPayments-tab')
    const TPSSummaryTab             = document.querySelector('#nav-TPSSummary-tab')
    const expensesTab               = document.querySelector('#nav-expenses-tab')
    const expenseSummaryTab         = document.querySelector('#nav-expenseSummary-tab')
    const visitSummaryTab1          = document.querySelector('#nav-visitSummary1-tab')
    const visitSummaryTab2          = document.querySelector('#nav-visitSummary2-tab')
    const yearlyIncomeAndExpense    = document.querySelector('#nav-yearlyIncomeAndExpense-tab')
    const yearlyIncomeAndExpense2    = document.querySelector('#nav-yearlyIncomeAndExpense2-tab')
    const yearlyIncomeAndExpense3    = document.querySelector('#nav-yearlyIncomeAndExpense3-tab')

    const searchPayMethodByDatesBtn  = document.querySelector('.searchPayMethodByDatesBtn')
    const searchPayMethodByMonthBtn  = document.querySelector('.searchPayMethodByMonthBtn')

    const searchByCapitationDatesBtn = document.querySelector('.searchByCapitationDatesBtn')
    const searchByCapitationMonthBtn = document.querySelector('.searchByCapitationMonthBtn')

    const searchTPSSummaryByDatesBtn = document.querySelector('.searchTPSSummaryByDatesBtn')
    const searchTPPSSummaryMonthBtn  = document.querySelector('.searchTPPSSummaryMonthBtn')

    const searchExpenseSummaryByDatesBtn = document.querySelector('.searchExpenseSummaryByDatesBtn')
    const searchExpenseSummaryByMonthBtn = document.querySelector('.searchExpenseSummaryByMonthBtn')

    const searchVisitsByDatesBtn1        = document.querySelector('.searchVisitsByDatesBtn1')
    const searchVisitsByMonthBtn1        = document.querySelector('.searchVisitsByMonthBtn1')

    const searchVisitsByDatesBtn2        = document.querySelector('.searchVisitsByDatesBtn2')
    const searchVisitsByMonthBtn2        = document.querySelector('.searchVisitsByMonthBtn2')

    const searchVisitsByDatesBtn3        = document.querySelector('.searchVisitsByDatesBtn3')
    const searchVisitsByMonthBtn3        = document.querySelector('.searchVisitsByMonthBtn3')

    const searchIncomeAndExpenseByYearBtn = document.querySelector('.searchIncomeAndExpenseByYearBtn')
    const searchIncomeAndExpense2ByYearBtn = document.querySelector('.searchIncomeAndExpense2ByYearBtn')
    const searchIncomeAndExpense3ByYearBtn = document.querySelector('.searchIncomeAndExpense3ByYearBtn')


    const newExpenseBtn                 = document.querySelector('#newExpenseBtn')
    const payDirectBtn                 = document.querySelector('#payDirectBtn')
    const saveExpenseBtn                = newExpenseModal._element.querySelector('#saveExpenseBtn')
    const updateExpenseBtn              = updateExpenseModal._element.querySelector('#updateExpenseBtn')
    const savePaymentBtn                = payDirectModal._element.querySelector('#savePaymentBtn')

    let payMethodsSummmaryTable, capitationPaymentsTable, TPSSummaryTable, TPSByThirdPartyTable, visitSummaryTable1, visitSummaryTable2, expensesTable, expenseSummaryTable, byPayMethodTable, byExpenseCategoryTable, visitsBySponsorTable, yearlyIncomeAndExpenseTable, yearlyIncomeAndExpenseTable2, yearlyIncomeAndExpenseTable3, yearlyIncomeAndExpenseChart, yearlyIncomeAndExpenseChart2, yearlyIncomeAndExpenseChart3

    payMethodsSummmaryTable = getPayMethodsSummmaryTable('payMethodSummaryTable')
    payMethodDiv.querySelector('#payMethodMonth').value == '' ? payMethodDiv.querySelector('#payMethodMonth').value = new Date().toISOString().slice(0,7) : ''

    payMethodSummaryTab.addEventListener('click', function() {
        payMethodsSummmaryTable.draw()
    })

    capitationPaymentsTab.addEventListener('click', function() {
        capitationDatesDiv.querySelector('#capitationMonth').value == '' ? capitationDatesDiv.querySelector('#capitationMonth').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#capitationPaymentsTable' )){
            $('#capitationPaymentsTable').dataTable().fnDraw()
        } else {
            capitationPaymentsTable = getCapitationPaymentsTable('capitationPaymentsTable')
        }
    })

    TPSSummaryTab.addEventListener('click', function() {
        TPSSummaryDatesDiv.querySelector('#TPSSummaryMonth').value == '' ? TPSSummaryDatesDiv.querySelector('#TPSSummaryMonth').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#TPSSummaryTable' )){
            $('#TPSSummaryTable').dataTable().fnDraw()
        } else {
            TPSSummaryTable = getTPSSummaryTable('TPSSummaryTable')
        }
    })

    expensesTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#expensesTable' )){
            $('#expensesTable').dataTable().fnDraw()
        } else {
            expensesTable = getExpensesTable('#expensesTable')
        }
    })

    expenseSummaryTab.addEventListener('click', function () {
        if (expenseSummaryDatesDiv.querySelector('#expenseMonth').value == '' && expenseSummaryDatesDiv.querySelector('#startDate').value == '') {
            expenseSummaryDatesDiv.querySelector('#expenseMonth').value = new Date().toISOString().slice(0,7)
        }
        if ($.fn.DataTable.isDataTable( '#expenseSummaryTable' )){
            $('#expenseSummaryTable').dataTable().fnDraw()
        } else {
            expenseSummaryTable = getExpenseSummaryTable('expenseSummaryTable')
        }
    })

    visitSummaryTab1.addEventListener('click', function () {
        visistSummaryDiv1.querySelector('#visitSummaryMonth1').value == '' ? visistSummaryDiv1.querySelector('#visitSummaryMonth1').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable1' )){
            $('#visitSummaryTable1').dataTable().fnDraw()
        } else {
            visitSummaryTable1 = getVisitSummaryTable1('visitSummaryTable1')
        }
    })

    visitSummaryTab2.addEventListener('click', function () {
        visistSummaryDiv2.querySelector('#visitSummaryMonth2').value == '' ? visistSummaryDiv2.querySelector('#visitSummaryMonth2').value = new Date().toISOString().slice(0,7) : ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable2' )){
            $('#visitSummaryTable2').dataTable().fnDraw()
        } else {
            visitSummaryTable2 = getVisitSummaryTable2('visitSummaryTable2')
        }
    })

    yearlyIncomeAndExpense.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#yearlyIncomeAndExpenseTable' )){
            $('#yearlyIncomeAndExpenseTable').dataTable().fnDraw()
        } else {
            yearlyIncomeAndExpenseTable = getYearlyIncomeAndExpenseTable('yearlyIncomeAndExpenseTable')
        }

        http.get(`/reports/accounts/yearlysummary`, {params:  {chart: 'chart' }}).then((response) => {
            
                yearlyIncomeAndExpenseChart?.destroy()
                yearlyIncomeAndExpenseChart = getYearlySummaryChart(yearlySummaryChart, response.data)
        })
    })

    yearlyIncomeAndExpense2.addEventListener('click', function () {
        if (yearlyIncomeAndExpenseTable2){
            yearlyIncomeAndExpenseTable2.draw()
        } else {
            yearlyIncomeAndExpenseTable2 = getYearlyIncomeAndExpenseTable2('yearlyIncomeAndExpenseTable2')
        }

        http.get(`/reports/accounts/yearlysummary2`, {params:  {chart: 'chart' }}).then((response) => {
            
                yearlyIncomeAndExpenseChart2?.destroy()
                yearlyIncomeAndExpenseChart2 = getYearlySummaryChart2(yearlySummaryChart2, response.data)
        })
    })

    yearlyIncomeAndExpense3.addEventListener('click', function () {
        if (yearlyIncomeAndExpenseTable3){
            yearlyIncomeAndExpenseTable3.draw()
        } else {
            yearlyIncomeAndExpenseTable3 = getYearlyIncomeAndExpenseTable3('yearlyIncomeAndExpenseTable3')
        }

        http.get(`/reports/accounts/yearlysummary3`, {params:  {chart: 'chart' }}).then((response) => {
            
                yearlyIncomeAndExpenseChart3?.destroy()
                yearlyIncomeAndExpenseChart3 = getYearlySummaryChart3(yearlySummaryChart3, response.data)
        })
    })


    searchPayMethodByDatesBtn.addEventListener('click', function () {
        payMethodDiv.querySelector('#payMethodMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#payMethodSummaryTable' )){
            $('#payMethodSummaryTable').dataTable().fnDestroy()
        }
        payMethodsSummmaryTable = getPayMethodsSummmaryTable('payMethodSummaryTable', payMethodDiv.querySelector('#startDate').value, payMethodDiv.querySelector('#endDate').value)
    })

    searchPayMethodByMonthBtn.addEventListener('click', function () {
        payMethodDiv.querySelector('#startDate').value = ''; payMethodDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#payMethodSummaryTable' )){
            $('#payMethodSummaryTable').dataTable().fnDestroy()
        }
        payMethodsSummmaryTable = getPayMethodsSummmaryTable('payMethodSummaryTable', null, null, payMethodDiv.querySelector('#payMethodMonth').value)
    })

    searchByCapitationDatesBtn.addEventListener('click', function () {
        capitationDatesDiv.querySelector('#capitationMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#capitationPaymentsTable' )){
            $('#capitationPaymentsTable').dataTable().fnDestroy()
        }
        capitationPaymentsTable = getCapitationPaymentsTable('capitationPaymentsTable', capitationDatesDiv.querySelector('#startDate').value, capitationDatesDiv.querySelector('#endDate').value)
    })

    searchByCapitationMonthBtn.addEventListener('click', function () {
        capitationDatesDiv.querySelector('#startDate').value = ''; capitationDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#capitationPaymentsTable' )){
            $('#capitationPaymentsTable').dataTable().fnDestroy()
        }
        capitationPaymentsTable = getCapitationPaymentsTable('capitationPaymentsTable', null, null, capitationDatesDiv.querySelector('#capitationMonth').value)
    })

    searchTPSSummaryByDatesBtn.addEventListener('click', function () {
        TPSSummaryDatesDiv.querySelector('#TPSSummaryMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#TPSSummaryTable' )){
            $('#TPSSummaryTable').dataTable().fnDestroy()
        }
        TPSSummaryTable = getTPSSummaryTable('TPSSummaryTable', TPSSummaryDatesDiv.querySelector('#startDate').value, TPSSummaryDatesDiv.querySelector('#endDate').value)
    })

    searchTPPSSummaryMonthBtn.addEventListener('click', function () {
        TPSSummaryDatesDiv.querySelector('#startDate').value = ''; TPSSummaryDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#TPSSummaryTable' )){
            $('#TPSSummaryTable').dataTable().fnDestroy()
        }
        TPSSummaryTable = getTPSSummaryTable('TPSSummaryTable', null, null, TPSSummaryDatesDiv.querySelector('#TPSSummaryMonth').value)
    })

    searchExpenseSummaryByDatesBtn.addEventListener('click', function () {
        expenseSummaryDatesDiv.querySelector('#expenseMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#expenseSummaryTable' )){
            $('#expenseSummaryTable').dataTable().fnDestroy()
        }
        expenseSummaryTable = getExpenseSummaryTable('expenseSummaryTable', expenseSummaryDatesDiv.querySelector('#startDate').value, expenseSummaryDatesDiv.querySelector('#endDate').value)
    })

    searchExpenseSummaryByMonthBtn.addEventListener('click', function () {
        expenseSummaryDatesDiv.querySelector('#startDate').value = ''; expenseSummaryDatesDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#expenseSummaryTable' )){
            $('#expenseSummaryTable').dataTable().fnDestroy()
        }
        expenseSummaryTable = getExpenseSummaryTable('expenseSummaryTable', null, null, expenseSummaryDatesDiv.querySelector('#expenseMonth').value)
    })

    searchVisitsByDatesBtn1.addEventListener('click', function () {
        visistSummaryDiv1.querySelector('#visitSummaryMonth1').value = ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable1' )){
            $('#visitSummaryTable1').dataTable().fnDestroy()
        }
        visitSummaryTable1 = getVisitSummaryTable1('visitSummaryTable1', visistSummaryDiv1.querySelector('#startDate').value, visistSummaryDiv1.querySelector('#endDate').value)
    })
    
    searchVisitsByMonthBtn1.addEventListener('click', function () {
        visistSummaryDiv1.querySelector('#startDate').value = ''; visistSummaryDiv1.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable1' )){
            $('#visitSummaryTable1').dataTable().fnDestroy()
        }
        visitSummaryTable1 = getVisitSummaryTable1('visitSummaryTable1', null, null, visistSummaryDiv1.querySelector('#visitSummaryMonth1').value)
    })
    
    searchVisitsByDatesBtn2.addEventListener('click', function () {
        visistSummaryDiv2.querySelector('#visitSummaryMonth2').value = ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable2' )){
            $('#visitSummaryTable2').dataTable().fnDestroy()
        }
        visitSummaryTable2 = getVisitSummaryTable2('visitSummaryTable2', visistSummaryDiv2.querySelector('#startDate').value, visistSummaryDiv2.querySelector('#endDate').value)
    })

    searchVisitsByMonthBtn2.addEventListener('click', function () {
        visistSummaryDiv2.querySelector('#startDate').value = ''; visistSummaryDiv2.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable2' )){
            $('#visitSummaryTable2').dataTable().fnDestroy()
        }
        visitSummaryTable2 = getVisitSummaryTable2('visitSummaryTable2', null, null, visistSummaryDiv2.querySelector('#visitSummaryMonth2').value)
    })

    searchIncomeAndExpenseByYearBtn.addEventListener('click', function () {
        const year = yearlyIncomeAndExpenseDiv.querySelector('#incomeAndExpenseyear').value
        if (yearlyIncomeAndExpenseTable){
            yearlyIncomeAndExpenseTable.destroy()
            yearlyIncomeAndExpenseTable = getYearlyIncomeAndExpenseTable('yearlyIncomeAndExpenseTable', year)
        } else {
            yearlyIncomeAndExpenseTable = getYearlyIncomeAndExpenseTable('yearlyIncomeAndExpenseTable', year)
        }

        http.get(`/reports/accounts/yearlysummary`, {params:{year: year, chart: 'chart'}}).then((response) => {
            if (yearlyIncomeAndExpenseChart){
                yearlyIncomeAndExpenseChart?.destroy()
                yearlyIncomeAndExpenseChart = getYearlySummaryChart(yearlySummaryChart, response.data)
            } else {
                yearlyIncomeAndExpenseChart = getYearlySummaryChart(yearlySummaryChart, response.data)
            }
        })
    })

    searchIncomeAndExpense2ByYearBtn.addEventListener('click', function () {
        const year = yearlyIncomeAndExpenseDiv2.querySelector('#incomeAndExpenseyear2').value
    
        if (yearlyIncomeAndExpenseTable2){
            yearlyIncomeAndExpenseTable2.destroy()
            yearlyIncomeAndExpenseTable2 = getYearlyIncomeAndExpenseTable2('yearlyIncomeAndExpenseTable2', year)
        } else {
            yearlyIncomeAndExpenseTable2 = getYearlyIncomeAndExpenseTable2('yearlyIncomeAndExpenseTable2', year)
        }

        http.get(`/reports/accounts/yearlysummary2`, {params:{year: year, chart: 'chart'}}).then((response) => {
            if (yearlyIncomeAndExpenseChart2){
                yearlyIncomeAndExpenseChart2?.destroy()
                yearlyIncomeAndExpenseChart2 = getYearlySummaryChart2(yearlySummaryChart2, response.data)
            } else {
                yearlyIncomeAndExpenseChart2 = getYearlySummaryChart2(yearlySummaryChart2, response.data)
            }
        })
    })

    searchIncomeAndExpense3ByYearBtn.addEventListener('click', function () {
        const year = yearlyIncomeAndExpenseDiv3.querySelector('#incomeAndExpenseyear3').value
    
        if (yearlyIncomeAndExpenseTable3){
            yearlyIncomeAndExpenseTable3.destroy()
            yearlyIncomeAndExpenseTable3 = getYearlyIncomeAndExpenseTable3('yearlyIncomeAndExpenseTable3', year)
        } else {
            yearlyIncomeAndExpenseTable3 = getYearlyIncomeAndExpenseTable3('yearlyIncomeAndExpenseTable3', year)
        }

        http.get(`/reports/accounts/yearlysummary3`, {params:{year: year, chart: 'chart'}}).then((response) => {
            if (yearlyIncomeAndExpenseChart3){
                yearlyIncomeAndExpenseChart3?.destroy()
                yearlyIncomeAndExpenseChart3 = getYearlySummaryChart3(yearlySummaryChart3, response.data)
            } else {
                yearlyIncomeAndExpenseChart3 = getYearlySummaryChart3(yearlySummaryChart3, response.data)
            }
        })
    })

    payDirectBtn.addEventListener('click', function () {
        payDirectModal.show()
    })

    savePaymentBtn.addEventListener('click', function () {
        savePaymentBtn.setAttribute('disabled', 'disabled')
        http.post('/billing/pay', {...getDivData(payDirectModal._element)}, {"html": payDirectModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                payDirectModal.hide()
                    clearDivValues(payDirectModal._element)
                    payMethodsSummmaryTable ? payMethodsSummmaryTable.draw() : ''
                }
                savePaymentBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            savePaymentBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    document.querySelector('#payMethodSummaryTable').addEventListener('click', function (event) {
        const showPaymentsBtn    = event.target.closest('.showPaymentsBtn')
        const payMethodfrom      = payMethodDiv.querySelector('#startDate').value
        const payMethodTo        = payMethodDiv.querySelector('#endDate').value
        const payMethodDate      = payMethodDiv.querySelector('#payMethodMonth').value

        if (showPaymentsBtn){
            showPaymentsBtn.setAttribute('disabled', true)
            const id = showPaymentsBtn.getAttribute('data-id')
            byPayMethodModal._element.querySelector('#paymethod').value = showPaymentsBtn.getAttribute('data-paymethod')

            if (payMethodDate){
                byPayMethodModal._element.querySelector('#payMethodMonth').value = payMethodDate
                byPayMethodTable = getByPayMethodsTable('byPayMethodTable', id, byPayMethodModal, null, null, payMethodDate)
                byPayMethodModal.show()
                return
            }

            if(payMethodfrom && payMethodTo){
                byPayMethodModal._element.querySelector('#from').value = payMethodfrom
                byPayMethodModal._element.querySelector('#to').value = payMethodTo
                byPayMethodTable = getByPayMethodsTable('byPayMethodTable', id, byPayMethodModal, payMethodfrom, payMethodTo)
                byPayMethodModal.show()
                return
            }

            byPayMethodModal._element.querySelector('#payMethodMonth').value = new Date().toISOString().slice(0,7)
            byPayMethodTable = getByPayMethodsTable('byPayMethodTable', id, byPayMethodModal)
            byPayMethodModal.show()
        }
    })

    document.querySelector('#byPayMethodTable').addEventListener('click', function (event) {
        const deletePaymentBtn   = event.target.closest('.deletePaymentBtn')

        if (deletePaymentBtn){
            const id = deletePaymentBtn.getAttribute('data-id')
            
            if (confirm('Are you sure you want to delete this payment?')) {
                deletePaymentBtn.setAttribute('disabled', 'disabled')
                http.delete(`/billing/payment/delete/${id}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            byPayMethodTable ? byPayMethodTable.draw() : ''
                        }
                        deletePaymentBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        if (error.response.status === 403){
                            alert(error.response.data.message); 
                        }
                        console.log(error)
                        deletePaymentBtn.removeAttribute('disabled')
                    })
            }
            
        }

    })

    document.querySelector('#TPSSummaryTable').addEventListener('click', function (event) {
        const showThirdPartyServicesBtn = event.target.closest('.showThirdPartyServicesBtn')
        const serviceFrom     = TPSSummaryDatesDiv.querySelector('#startDate').value
        const serviceTo       = TPSSummaryDatesDiv.querySelector('#endDate').value
        const serviceDate     = TPSSummaryDatesDiv.querySelector('#TPSSummaryMonth').value

        if (showThirdPartyServicesBtn){
            showThirdPartyServicesBtn.setAttribute('disabled', true)
            const id = showThirdPartyServicesBtn.getAttribute('data-id')
            TPSByThirdPartyModal._element.querySelector('#thirdParty').value = showThirdPartyServicesBtn.getAttribute('data-thirdparty')

            if (serviceDate){
                TPSByThirdPartyModal._element.querySelector('#TPSByMonth').value = serviceDate
                TPSByThirdPartyTable = getTPSByThirdPartyTable('TPSByThirdPartyTable', id, TPSByThirdPartyModal, null, null, serviceDate)
                TPSByThirdPartyModal.show()
                return
            }

            if(serviceFrom && serviceTo){
                TPSByThirdPartyModal._element.querySelector('#from').value = serviceFrom
                TPSByThirdPartyModal._element.querySelector('#to').value = serviceTo
                TPSByThirdPartyTable = getTPSByThirdPartyTable('TPSByThirdPartyTable', id, TPSByThirdPartyModal, serviceFrom, serviceTo)
                TPSByThirdPartyModal.show()
                return
            }

            TPSByThirdPartyModal._element.querySelector('#TPSByMonth').value = new Date().toISOString().slice(0,7)
            TPSByThirdPartyTable = getTPSByThirdPartyTable('TPSByThirdPartyTable', id, TPSByThirdPartyModal)
            TPSByThirdPartyModal.show()
        }
    })

    document.querySelector('#expenseSummaryTable').addEventListener('click', function (event) {
        const showExpensesBtn   = event.target.closest('.showExpensesBtn')
        const expensesFrom      = expenseSummaryDatesDiv.querySelector('#startDate').value
        const expensesTo        = expenseSummaryDatesDiv.querySelector('#endDate').value
        const expensesDate      = expenseSummaryDatesDiv.querySelector('#expenseMonth').value

        if (showExpensesBtn){
            showExpensesBtn.setAttribute('disabled', true)
            const id = showExpensesBtn.getAttribute('data-id')
            byExpenseCategoryModal._element.querySelector('#expenseCategory').value = showExpensesBtn.getAttribute('data-expensecategory')

            if (expensesDate){
                byExpenseCategoryModal._element.querySelector('#expenseMonth').value = expensesDate
                byExpenseCategoryTable = getExpensesTable('#byExpenseCategoryTable', 'byExpenseCategory', id, byExpenseCategoryModal, null, null, expensesDate)
                byExpenseCategoryModal.show()
                return
            }

            if(expensesFrom && expensesTo){
                byExpenseCategoryModal._element.querySelector('#from').value = expensesFrom
                byExpenseCategoryModal._element.querySelector('#to').value = expensesTo
                byExpenseCategoryModal._element.querySelector('#expenseMonth').value = ''
                byExpenseCategoryTable = getExpensesTable('#byExpenseCategoryTable', 'byExpenseCategory', id, byExpenseCategoryModal, expensesFrom, expensesTo)
                byExpenseCategoryModal.show()
                return
            }

            byExpenseCategoryModal._element.querySelector('#expenseMonth').value = new Date().toISOString().slice(0,7)
            byExpenseCategoryTable = getExpensesTable('#byExpenseCategoryTable', 'byExpenseCategory', id, byExpenseCategoryModal)
            byExpenseCategoryModal.show()
        }
    })

    document.querySelector('#capitationPaymentsTable').addEventListener('click', function (event) {
        const deletePaymentBtn    = event.target.closest('.deletePaymentBtn')

        if (deletePaymentBtn){
            deletePaymentBtn.setAttribute('disabled', 'disabled')
            if (confirm('Are you sure you want to delete this Capitation Payment?')) {
                const capitationPayment = deletePaymentBtn.getAttribute('data-id')
                http.delete(`/capitation/${capitationPayment}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300){
                            capitationPaymentsTable ? capitationPaymentsTable.draw() : ''
                        }
                        deletePaymentBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        alert(error)
                    })
            }
        }
    })

    document.querySelector('#visitSummaryTable2').addEventListener('click', function (event) {
        const showVisitsBtn = event.target.closest('.showVisitsBtn')
        const visitFrom     = visistSummaryDiv2.querySelector('#startDate').value
        const visitTo       = visistSummaryDiv2.querySelector('#endDate').value
        const visitDate     = visistSummaryDiv2.querySelector('#visitSummaryMonth2').value

        if (showVisitsBtn){
            showVisitsBtn.setAttribute('disabled', true)
            const id = showVisitsBtn.getAttribute('data-id')
            
            visitsBySponsorModal._element.querySelector('#sponsor').value = showVisitsBtn.getAttribute('data-sponsor')
            visitsBySponsorModal._element.querySelector('#sponsorCategory').value = showVisitsBtn.getAttribute('data-category')
            visitsBySponsorModal._element.querySelector('#sponsor').setAttribute('data-sponsorid', id)

            if (visitDate){
                visitsBySponsorModal._element.querySelector('#visitMonth').value = visitDate
                visitsBySponsorTable = getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, null, null, visitDate)
                visitsBySponsorModal.show()
                return
            }

            if(visitFrom && visitTo){
                visitsBySponsorModal._element.querySelector('#from').value = visitFrom
                visitsBySponsorModal._element.querySelector('#to').value = visitTo
                visitsBySponsorTable = getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, visitFrom, visitTo)
                visitsBySponsorModal.show()
                return
            }

            visitsBySponsorModal._element.querySelector('#visitMonth').value = new Date().toISOString().slice(0,7)
            visitsBySponsorTable = getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal)
            visitsBySponsorModal.show()
        }
    })

    newExpenseBtn.addEventListener('click', function () {
        newExpenseModal.show()
    })

    saveExpenseBtn.addEventListener('click', function () {
        saveExpenseBtn.setAttribute('disabled', 'disabled')
        http.post('/expenses', {...getDivData(newExpenseModal._element)}, {"html": newExpenseModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                newExpenseModal.hide()
                    clearDivValues(newExpenseModal._element)
                    expensesTable ? expensesTable.draw() : ''
                }
                saveExpenseBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            saveExpenseBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    document.querySelectorAll('#expensesTable, #byExpenseCategoryTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const editExpenseBtn    = event.target.closest('.editExpenseBtn')
            const deleteExpenseBtn    = event.target.closest('.deleteExpenseBtn')
    
            if (editExpenseBtn) {
                editExpenseBtn.setAttribute('disabled', 'disabled')
                const expense = editExpenseBtn.getAttribute('data-id')
                http.get(`/expenses/${ expense }`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            openModals(updateExpenseModal, updateExpenseBtn, response.data.data)
                        }
                        editExpenseBtn.removeAttribute('disabled')
                    })
                    .catch((error) => {
                        editExpenseBtn.removeAttribute('disabled')
                        alert(error.response.data.data.message)
                    })
            }
    
            if (deleteExpenseBtn){
                deleteExpenseBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this Expense?')) {
                    const expense = deleteExpenseBtn.getAttribute('data-id')
                    http.delete(`/expenses/${expense}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                expensesTable ? expensesTable.draw() : ''
                            }
                            deleteExpenseBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            console.log(error)
                            deleteExpenseBtn.removeAttribute('disabled')
                        })
                }
            }
        })
    })

    updateExpenseBtn.addEventListener('click', function (event) {
        const expense = event.currentTarget.getAttribute('data-id')
        updateExpenseBtn.setAttribute('disabled', 'disabled')
        http.post(`/expenses/${expense}`, getDivData(updateExpenseModal._element), {"html": updateExpenseModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateExpenseModal.hide()
                expensesTable ? expensesTable.draw() : ''
                byExpenseCategoryTable ? byExpenseCategoryTable.draw() : ''
            }
            updateExpenseBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            updateExpenseBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })

    document.querySelectorAll('#visitsBySponsorTable').forEach(table => {
        table.addEventListener('click', function (event) {
            const reviewSpan            = event.target.closest('.reviewSpan')
            const markAsResolvedBtn     = event.target.closest('.markAsResolvedBtn')
            const sortByreviewed        = event.target.closest('.sortByreviewed')
            const sortByresolved        = event.target.closest('.sortByresolved')
            const resetSorting          = event.target.closest('.resetSorting')
            const id                    = visitsBySponsorModal._element.querySelector('#sponsor').getAttribute('data-sponsorid')
            
            if (reviewSpan){
                const visitId       = reviewSpan.getAttribute('data-id')
                const reviewInput   = reviewSpan.parentElement.querySelector('.reviewInput')
                reviewSpan.classList.add('d-none')
                reviewInput.classList.remove('d-none')
                resetFocusEndofLine(reviewInput)
                
                reviewInput.addEventListener('blur', function () {
                    http.patch(`/visits/review/${visitId}`, {review: reviewInput.value})
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            visitsBySponsorTable ?  visitsBySponsorTable.draw(false) : ''
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        visitsBySponsorTable ?  visitsBySponsorTable.draw(false) : ''
                    })                
                })
            }

            if (markAsResolvedBtn){
                const visitId   = markAsResolvedBtn.getAttribute('data-id')
                const state     = +markAsResolvedBtn.getAttribute('data-state')
                const string    = state ? 'unmark' : 'mark'        

                if (confirm(`Are you sure you want to ${string} as Resolved?`)) {

                    const modalBody = $('#visitsBySponsorModal .modal-body');
                    const scrollTop = modalBody.scrollTop();
            
                    visitsBySponsorTable.one('draw', function() {
                        modalBody.scrollTop(scrollTop);
                        if (/Chrome/.test(navigator.userAgent) && !/Edg/.test(navigator.userAgent)) {
                            requestAnimationFrame(() => {
                                modalBody.scrollTop(scrollTop);
                            });
                        }
                    });

                    http.patch(`/visits/resolve/${visitId}`)
                    .then((response) => {
                        if (response.status >= 200 || response.status <= 300) {
                            visitsBySponsorTable ? visitsBySponsorTable.draw(false) : '';
                        }
                    })
                    .catch((error) => {
                        console.log(error)
                        visitsBySponsorTable ?  visitsBySponsorTable.draw(false) : ''
                    })                
                }
            }

            if (sortByreviewed){
                const visitDate = visitsBySponsorModal._element.querySelector('#visitMonth').value
                const visitFrom = visitsBySponsorModal._element.querySelector('#from').value
                const visitTo   = visitsBySponsorModal._element.querySelector('#to').value

                if (visitDate){
                    if ($.fn.DataTable.isDataTable( '#visitsBySponsorTable' )){
                        $('#visitsBySponsorTable').dataTable().fnDestroy()
                    }
                    getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, null, null, visitDate, 'reviewed')
                }

                if(visitFrom && visitTo){
                    if ($.fn.DataTable.isDataTable( '#visitsBySponsorTable' )){
                        $('#visitsBySponsorTable').dataTable().fnDestroy()
                    }
                    getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, visitFrom, visitTo, null, 'reviewed')
                }
            }

            if (sortByresolved){
                const visitDate = visitsBySponsorModal._element.querySelector('#visitMonth').value
                const visitFrom = visitsBySponsorModal._element.querySelector('#from').value
                const visitTo   = visitsBySponsorModal._element.querySelector('#to').value

                if (visitDate){
                    if ($.fn.DataTable.isDataTable( '#visitsBySponsorTable' )){
                        $('#visitsBySponsorTable').dataTable().fnDestroy()
                    }
                    getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, null, null, visitDate, 'resolved')
                }

                if(visitFrom && visitTo){
                    if ($.fn.DataTable.isDataTable( '#visitsBySponsorTable' )){
                        $('#visitsBySponsorTable').dataTable().fnDestroy()
                    }
                    getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, visitFrom, visitTo, null, 'resolved')
                }
            }

            if (resetSorting){
                const visitDate = visitsBySponsorModal._element.querySelector('#visitMonth').value
                const visitFrom = visitsBySponsorModal._element.querySelector('#from').value
                const visitTo   = visitsBySponsorModal._element.querySelector('#to').value

                if (visitDate){
                    if ($.fn.DataTable.isDataTable( '#visitsBySponsorTable' )){
                        $('#visitsBySponsorTable').dataTable().fnDestroy()
                    }
                    getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, null, null, visitDate)
                }

                if(visitFrom && visitTo){
                    if ($.fn.DataTable.isDataTable( '#visitsBySponsorTable' )){
                        $('#visitsBySponsorTable').dataTable().fnDestroy()
                    }
                    getVisitsBySponsorTable('visitsBySponsorTable', id, visitsBySponsorModal, visitFrom, visitTo, null)
                }
            }
        })
    })

    visitsBySponsorModal._element.addEventListener('hidden.bs.modal', function () {
        visitSummaryTable2 ? visitSummaryTable2.draw() : ''
    })

    byPayMethodModal._element.addEventListener('hidden.bs.modal', function () {
        payMethodsSummmaryTable ? payMethodsSummmaryTable.draw() : ''
    })
})