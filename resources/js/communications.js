import { Modal } from "bootstrap";
import $ from 'jquery';
import http from "./http";
import { clearDivValues, getDivData } from "./helpers";
import { getCurrentBalance, getListOfSentSmsTable, getWalletFundingTable } from "./tables/CommunicationTables";
import { showToast } from "./toasts/globalNotificationToasts";

window.addEventListener('DOMContentLoaded', function () {
    const buyUnitsModal       = new Modal(document.getElementById('buyUnitsModal'))
    const listOfSentSmsTab    = document.querySelector('#nav-listOfSentSms-tab')

    const buyUnitsBtn         = document.getElementById('buyUnitsBtn')
    const buyBtn              = document.getElementById('buyBtn')
    
    const amountInput         = document.getElementById('amount') 
    const unitsInput          = document.getElementById('units') 
    const unitsDiv            = document.getElementById('unitsDiv') 
    
    let walletFundingTable
    
    const listOfSentSmsTable  = getListOfSentSmsTable('listOfSentSmsTable')

    const currentBalance = (show) => {getCurrentBalance(unitsDiv, show);}

    currentBalance(false)

    listOfSentSmsTab.addEventListener('click', function() {listOfSentSmsTable.draw()})

    document.querySelector('#listOfSentSmsTable').addEventListener('click', function (event) {
            const deleteSmsBtn = event.target.closest('.deleteSmsBtn')
    
            if (deleteSmsBtn){
                deleteSmsBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this sms record?')) {
                    const smsId = deleteSmsBtn.getAttribute('data-id')
                    http.delete(`/communication-services/${smsId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                showToast('SMS deleted successfully', 'success')
                                listOfSentSmsTable.draw()
                            }
                            deleteSmsBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            deleteSmsBtn.removeAttribute('disabled')
                            showToast('SMS was not deleted successfully', 'error')
                            console.log(error)
                        })
                }
                deleteSmsBtn.removeAttribute('disabled')
            }
    })

    buyUnitsBtn.addEventListener('click', function(){
        buyUnitsModal.show();
        walletFundingTable = getWalletFundingTable('walletFundingTable');
    })

    amountInput.addEventListener('input', function(){
        unitsInput.value = amountInput.value / 2;
    })

    buyBtn.addEventListener('click', function(){
        buyBtn.setAttribute('disabled', 'disabled')
        http.post(`/wallet-funding`, getDivData(buyUnitsModal._element), {"html": buyUnitsModal._element})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        clearDivValues(buyUnitsModal._element)
                        walletFundingTable ? walletFundingTable.draw(false) : ''
                        showToast('Units order placed successfully', 'success')
                    }
                    buyBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    buyBtn.removeAttribute('disabled')
                    console.log(error.response)
                })
    })

    document.querySelector('#walletFundingTable').addEventListener('click', function (event) {
        const deleteFundBtn    = event.target.closest('.deleteFundBtn')
        const updatePaymentStatusBtn    = event.target.closest('.updatePaymentStatusBtn')
    
            if (deleteFundBtn){
                deleteFundBtn.setAttribute('disabled', 'disabled')
                if (confirm('Are you sure you want to delete this fund?')) {
                    const smsId = deleteFundBtn.getAttribute('data-id')
                    http.delete(`/wallet-funding/${smsId}`)
                        .then((response) => {
                            if (response.status >= 200 || response.status <= 300){
                                showToast('Fund deleted successfully', 'success')
                                walletFundingTable? walletFundingTable.draw(false) : ''
                            }
                            deleteFundBtn.removeAttribute('disabled')
                        })
                        .catch((error) => {
                            deleteFundBtn.removeAttribute('disabled')
                            showToast('SMS was not deleted successfully', 'error')
                            console.log(error)
                        })
                }
                deleteFundBtn.removeAttribute('disabled')
            }

            if (updatePaymentStatusBtn){
                updatePaymentStatusBtn.setAttribute('disabled', 'disabled')
                const fundId = updatePaymentStatusBtn.getAttribute('data-id')
                http.patch(`/wallet-funding/${fundId}`, {paymentStatus : updatePaymentStatusBtn.innerHTML}, {})
                .then((response) => {
                    if (response.status >= 200 || response.status <= 300){
                        walletFundingTable ? walletFundingTable.draw(false) : ''
                        showToast(response.data.message, 'success')
                    }
                    updatePaymentStatusBtn.removeAttribute('disabled')
                })
                .catch((error) => {
                    updatePaymentStatusBtn.removeAttribute('disabled')
                    showToast('Status updated failed', 'error')
                    console.log(error.response)
                })
            }
    });

    buyUnitsModal._element.addEventListener('hide.bs.modal', function () {
        listOfSentSmsTable.draw()
        walletFundingTable ? walletFundingTable.destroy() : ''
        currentBalance(true)
    });

});