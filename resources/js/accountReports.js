import {Modal } from "bootstrap";
import http from "./http";
import $ from 'jquery';
import { getPayMethodsSummmaryTable, getCapitationPaymentsTable, getExpenseSummaryTable, getVisitSummaryTable, getByPayMethodsTable } from "./tables/accountReportTables";
import { getExpensesTable } from "./tables/billingTables";
import { clearDivValues, getDivData } from "./helpers";

window.addEventListener('DOMContentLoaded', function () {
    const byPayMethodModal           = new Modal(document.getElementById('byPayMethodModal'))
    const newExpenseModal            = new Modal(document.getElementById('newExpenseModal'))
    const updateExpenseModal         = new Modal(document.getElementById('updateExpenseModal'))
    const byExpenseCategoryModal     = new Modal(document.getElementById('byExpenseCategoryModal'))

    const payMethodDiv                  = document.querySelector('.payMethodDiv')
    const capitationDatesDiv        = document.querySelector('.capitationDatesDiv')
    const expenseSummaryDatesDiv    = document.querySelector('.expenseSummaryDatesDiv')
    const visistSummaryDiv          = document.querySelector('.visistSummaryDiv')

    const payMethodSummaryTab       = document.querySelector('#nav-payMethodSummary-tab')
    const capitationPaymentsTab     = document.querySelector('#nav-capitationPayments-tab')
    const expensesTab               = document.querySelector('#nav-expenses-tab')
    const expenseSummaryTab         = document.querySelector('#nav-expenseSummary-tab')
    const visitSummaryTab           = document.querySelector('#nav-visitSummary-tab')

    const searchPayMethodByDatesBtn  = document.querySelector('.searchPayMethodByDatesBtn')
    const searchPayMethodByMonthBtn  = document.querySelector('.searchPayMethodByMonthBtn')
    const searchByCapitationDatesBtn = document.querySelector('.searchByCapitationDatesBtn')
    const searchByCapitationMonthBtn = document.querySelector('.searchByCapitationMonthBtn')
    const searchExpenseSummaryByDatesBtn = document.querySelector('.searchExpenseSummaryByDatesBtn')
    const searchExpenseSummaryByMonthBtn = document.querySelector('.searchExpenseSummaryByMonthBtn')
    const searchVisitsByDatesBtn        = document.querySelector('.searchVisitsByDatesBtn')
    const searchVisitsByMonthBtn        = document.querySelector('.searchVisitsByMonthBtn')
    const newExpenseBtn                 = document.querySelector('#newExpenseBtn')
    const saveExpenseBtn                = newExpenseModal._element.querySelector('#saveExpenseBtn')
    const updateExpenseBtn              = updateExpenseModal._element.querySelector('#updateExpenseBtn')

    let payMethodsSummmaryTable, capitationPaymentsTable, visitSummaryTable, expensesTable, expenseSummaryTable, byPayMethodTable, byExpenseCategoryTable
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

    visitSummaryTab.addEventListener('click', function () {
        visistSummaryDiv.querySelector('#visitSummaryMonth').value = new Date().toISOString().slice(0,7)
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable' )){
            $('#visitSummaryTable').dataTable().fnDraw()
        } else {
            visitSummaryTable = getVisitSummaryTable('visitSummaryTable')
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

    searchVisitsByDatesBtn.addEventListener('click', function () {
        visistSummaryDiv.querySelector('#visitSummaryMonth').value = ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable' )){
            $('#visitSummaryTable').dataTable().fnDestroy()
        }
        visitSummaryTable = getVisitSummaryTable('visitSummaryTable', visistSummaryDiv.querySelector('#startDate').value, visistSummaryDiv.querySelector('#endDate').value)
    })

    searchVisitsByMonthBtn.addEventListener('click', function () {
        visistSummaryDiv.querySelector('#startDate').value = ''; visistSummaryDiv.querySelector('#endDate').value = ''
        if ($.fn.DataTable.isDataTable( '#visitSummaryTable' )){
            $('#visitSummaryTable').dataTable().fnDestroy()
        }
        visitSummaryTable = getVisitSummaryTable('visitSummaryTable', null, null, visistSummaryDiv.querySelector('#visitSummaryMonth').value)
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
            }

            if(payMethodfrom && payMethodTo){
                byPayMethodModal._element.querySelector('#from').value = payMethodfrom
                byPayMethodModal._element.querySelector('#to').value = payMethodTo
                byPayMethodTable = getByPayMethodsTable('byPayMethodTable', id, byPayMethodModal, payMethodfrom, payMethodTo)
                byPayMethodModal.show()
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
            }

            if(expensesFrom && expensesTo){
                byExpenseCategoryModal._element.querySelector('#from').value = expensesFrom
                byExpenseCategoryModal._element.querySelector('#to').value = expensesTo
                byExpenseCategoryTable = getExpensesTable('byExpenseCategoryTable', 'byExpenseCategory', id, byExpenseCategoryModal, expensesFrom, expensesTo)
                byExpenseCategoryModal.show()
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