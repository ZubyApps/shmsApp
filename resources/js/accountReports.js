import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getPayMethodsSummmaryTable, getCapitationPaymentsTable, getExpenseSummaryTable, getVisitSummaryTable1, getVisitSummaryTable2, getByPayMethodsTable, getVisitsBySponsorTable, getYearlyIncomeAndExpenseTable } from "./tables/accountReportTables";
import { getExpensesTable } from "./tables/billingTables";
import { clearDivValues, getDivData } from "./helpers";

window.addEventListener('DOMContentLoaded', function () {
    const byPayMethodModal           = new Modal(document.getElementById('byPayMethodModal'))
    const newExpenseModal            = new Modal(document.getElementById('newExpenseModal'))
    const updateExpenseModal         = new Modal(document.getElementById('updateExpenseModal'))
    const byExpenseCategoryModal     = new Modal(document.getElementById('byExpenseCategoryModal'))
    const visitsBySponsorModal       = new Modal(document.getElementById('visitsBySponsorModal'))

    const payMethodDiv               = document.querySelector('.payMethodDiv')
    const capitationDatesDiv         = document.querySelector('.capitationDatesDiv')
    const expenseSummaryDatesDiv     = document.querySelector('.expenseSummaryDatesDiv')
    const visistSummaryDiv1          = document.querySelector('.visistSummaryDiv1')
    const visistSummaryDiv2          = document.querySelector('.visistSummaryDiv2')
    const yearlyIncomeAndExpenseDiv  = document.querySelector('.yearlyIncomeAndExpenseDiv')

    const payMethodSummaryTab       = document.querySelector('#nav-payMethodSummary-tab')
    const capitationPaymentsTab     = document.querySelector('#nav-capitationPayments-tab')
    const expensesTab               = document.querySelector('#nav-expenses-tab')
    const expenseSummaryTab         = document.querySelector('#nav-expenseSummary-tab')
    const visitSummaryTab1          = document.querySelector('#nav-visitSummary1-tab')
    const visitSummaryTab2          = document.querySelector('#nav-visitSummary2-tab')
    const yearlyIncomeAndExpense    = document.querySelector('#nav-yearlyIncomeAndExpense-tab')

    const searchPayMethodByDatesBtn  = document.querySelector('.searchPayMethodByDatesBtn')
    const searchPayMethodByMonthBtn  = document.querySelector('.searchPayMethodByMonthBtn')
    const searchByCapitationDatesBtn = document.querySelector('.searchByCapitationDatesBtn')
    const searchByCapitationMonthBtn = document.querySelector('.searchByCapitationMonthBtn')
    const searchExpenseSummaryByDatesBtn = document.querySelector('.searchExpenseSummaryByDatesBtn')
    const searchExpenseSummaryByMonthBtn = document.querySelector('.searchExpenseSummaryByMonthBtn')
    const searchVisitsByDatesBtn1        = document.querySelector('.searchVisitsByDatesBtn1')
    const searchVisitsByMonthBtn1        = document.querySelector('.searchVisitsByMonthBtn1')
    const searchVisitsByDatesBtn2        = document.querySelector('.searchVisitsByDatesBtn2')
    const searchVisitsByMonthBtn2        = document.querySelector('.searchVisitsByMonthBtn2')
    const searchIncomeAndExpenseByYearBtn = document.querySelector('.searchIncomeAndExpenseByYearBtn')


    const newExpenseBtn                 = document.querySelector('#newExpenseBtn')
    const saveExpenseBtn                = newExpenseModal._element.querySelector('#saveExpenseBtn')
    const updateExpenseBtn              = updateExpenseModal._element.querySelector('#updateExpenseBtn')

    let payMethodsSummmaryTable, capitationPaymentsTable, visitSummaryTable1, visitSummaryTable2, expensesTable, expenseSummaryTable, byPayMethodTable, byExpenseCategoryTable, visitsBySponsorTable, yearlyIncomeAndExpenseTable

    payMethodsSummmaryTable = getPayMethodsSummmaryTable('payMethodSummaryTable')
    payMethodDiv.querySelector('#payMethodMonth').value = new Date().toISOString().slice(0,7)

    payMethodSummaryTab.addEventListener('click', function() {
        payMethodsSummmaryTable.draw()
    })

    capitationPaymentsTab.addEventListener('click', function() {
        capitationDatesDiv.querySelector('#capitationMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#capitationPaymentsTable' )){
            $('#capitationPaymentsTable').dataTable().fnDraw()
        } else {
            capitationPaymentsTable = getCapitationPaymentsTable('capitationPaymentsTable')
        }
    })

    expensesTab.addEventListener('click', function () {
        if ($.fn.DataTable.isDataTable( '#expensesTable' )){
            $('#expensesTable').dataTable().fnDraw()
        } else {
            expensesTable = getExpensesTable('expensesTable')
        }
    })

    expenseSummaryTab.addEventListener('click', function () {
        expenseSummaryDatesDiv.querySelector('#expenseMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#expenseSummaryTable' )){
            $('#expenseSummaryTable').dataTable().fnDraw()
        } else {
            expensesTable = getExpenseSummaryTable('expenseSummaryTable')
        }
    })

    visitSummaryTab1.addEventListener('click', function () {
        visistSummaryDiv1.querySelector('#visitSummaryMonth1').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable1' )){
            $('#visitSummaryTable1').dataTable().fnDraw()
        } else {
            visitSummaryTable1 = getVisitSummaryTable1('visitSummaryTable1')
        }
    })

    visitSummaryTab2.addEventListener('click', function () {
        visistSummaryDiv2.querySelector('#visitSummaryMonth2').value = new Date().toISOString().slice(0,7)
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
        if ($.fn.DataTable.isDataTable( '#expenseSummaryTable' )){
            $('#expenseSummaryTable').dataTable().fnDestroy()
        }
        capitationPaymentsTable = getCapitationPaymentsTable('expenseSummaryTable', null, null, capitationDatesDiv.querySelector('#capitationMonth').value)
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
        visitSummaryTable1 = getVisitSummaryTable1('visitSummaryTable', visistSummaryDiv1.querySelector('#startDate').value, visistSummaryDiv1.querySelector('#endDate').value)
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
        if ($.fn.DataTable.isDataTable( '#yearlyIncomeAndExpenseTable' )){
            $('#yearlyIncomeAndExpenseTable').dataTable().fnDestroy()
        }
        yearlyIncomeAndExpenseTable = getYearlyIncomeAndExpenseTable('yearlyIncomeAndExpenseTable', yearlyIncomeAndExpenseDiv.querySelector('#incomeAndExpenseyear').value)
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
                byExpenseCategoryTable = getExpensesTable('byExpenseCategoryTable', 'byExpenseCategory', id, byExpenseCategoryModal, null, null, expensesDate)
                byExpenseCategoryModal.show()
                return
            }

            if(expensesFrom && expensesTo){
                byExpenseCategoryModal._element.querySelector('#from').value = expensesFrom
                byExpenseCategoryModal._element.querySelector('#to').value = expensesTo
                byExpenseCategoryTable = getExpensesTable('byExpenseCategoryTable', 'byExpenseCategory', id, byExpenseCategoryModal, expensesFrom, expensesTo)
                byExpenseCategoryModal.show()
                return
            }

            byExpenseCategoryModal._element.querySelector('#expenseMonth').value = new Date().toISOString().slice(0,7)
            byExpenseCategoryTable = getExpensesTable('byExpenseCategoryTable', 'byExpenseCategory', id, byExpenseCategoryModal)
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
            console.log(error.response.data.message)
            saveExpenseBtn.removeAttribute('disabled')
        })
    })

    document.querySelector('#expensesTable').addEventListener('click', function (event) {
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

    updateExpenseBtn.addEventListener('click', function (event) {
        const expense = event.currentTarget.getAttribute('data-id')
        updateExpenseBtn.setAttribute('disabled', 'disabled')
        http.post(`/expenses/${expense}`, getDivData(updateExpenseModal._element), {"html": updateExpenseModal._element})
        .then((response) => {
            if (response.status >= 200 || response.status <= 300){
                updateExpenseModal.hide()
                expensesTable ? expensesTable.draw() : ''
            }
            updateExpenseBtn.removeAttribute('disabled')
        })
        .catch((error) => {
            updateExpenseBtn.removeAttribute('disabled')
            console.log(error.response.data.message)
        })
    })
})