import { Modal } from "bootstrap";
import $ from 'jquery';
import http from "./http";
import { clearDivValues, clearValidationErrors, displayItemsList, getDivData, handleValidationErrors } from "./helpers";
import { getCurrentBalance, getListOfSentSmsTable, getWalletFundingTable } from "./tables/CommunicationTables";
import { showToast } from "./toasts/globalNotificationToasts";

window.addEventListener('DOMContentLoaded', function () {
    const buyUnitsModal       = new Modal(document.getElementById('buyUnitsModal'))
    const sendSmsModal        = new Modal(document.getElementById('sendSmsModal'))

    const listOfSentSmsTab    = document.querySelector('#nav-listOfSentSms-tab')

    const buyUnitsBtn         = document.getElementById('buyUnitsBtn')
    const buyBtn              = document.getElementById('buyBtn')
    const openSendSmsBtn      = document.getElementById('openSendSmsBtn')
    
    const amountInput         = document.getElementById('amount') 
    const unitsInput          = document.getElementById('units') 
    
    const sendingCategoryEl   = document.getElementById('sendingCategory')
    const singleSourceEL      = document.getElementById('singleSource')
    const multiSourceEL       = document.getElementById('multiSource')
    
    const unitsDiv            = document.getElementById('unitsDiv')
    const singleReceiverDiv   = document.querySelector('.singleReceiverDiv')
    const multipleReceiversDiv = document.querySelector('.multipleReceiversDiv')

    const singleSourceDiv = document.querySelector('.singleSourceDiv')
    const multiSourceDiv = document.querySelector('.multiSourceDiv')

    const searchPatientDiv = document.querySelector('.searchPatientDiv')
    const searchStaffDiv = document.querySelector('.searchStaffDiv')
    const typeNumberDiv = document.querySelector('.typeNumberDiv')
    const sendSmsBtn      = document.querySelector('#sendSmsBtn')
    
    const hmsPatientsDiv = document.querySelector('.hmsPatientsDiv')
    const hmsStaffDiv = document.querySelector('.hmsStaffDiv')
    const numbersDiv = document.querySelector('.numbersDiv')

    const searchPatientInput = document.querySelector('#patient')
    const searchStaffInput = document.querySelector('#staff')
    
    let walletFundingTable
    
    const listOfSentSmsTable  = getListOfSentSmsTable('listOfSentSmsTable')

    const currentBalance = (show) => {getCurrentBalance(unitsDiv, show);}

    currentBalance(false)

    sendingCategoryEl.addEventListener('change', function(){
        if (sendingCategoryEl.value == 'single'){
            singleReceiverDiv.classList.remove('d-none');
            multipleReceiversDiv.classList.add('d-none');
            multiSourceEL.value = '';
        }
        if (sendingCategoryEl.value == 'multiple'){
            multipleReceiversDiv.classList.remove('d-none');
            singleReceiverDiv.classList.add('d-none');
            singleSourceEL.value = '';
        }
        if (sendingCategoryEl.value == ''){
            singleReceiverDiv.classList.add('d-none');
            multipleReceiversDiv.classList.add('d-none');
            singleSourceEL.value = '';
             multiSourceEL.value = '';
        }
    })

    singleSourceEL.addEventListener('change', function(){

        if (singleSourceEL.value == ''){
            singleSourceDiv.classList.add('d-none')
            searchPatientDiv.classList.add('d-none')
            searchStaffDiv.classList.add('d-none')
            typeNumberDiv.classList.add('d-none')
        }

        if (singleSourceEL.value == 'patient'){
            singleSourceDiv.classList.remove('d-none')
            searchPatientDiv.classList.remove('d-none')
            searchStaffDiv.classList.add('d-none')
            typeNumberDiv.classList.add('d-none')
        }
        if (singleSourceEL.value == 'staff'){
            singleSourceDiv.classList.remove('d-none')
            searchStaffDiv.classList.remove('d-none')
            searchPatientDiv.classList.add('d-none')   
            typeNumberDiv.classList.add('d-none')   
        }
        if (singleSourceEL.value == 'number'){
            singleSourceDiv.classList.remove('d-none')
            typeNumberDiv.classList.remove('d-none')
            searchPatientDiv.classList.add('d-none')   
            searchStaffDiv.classList.add('d-none')   
        }
        
    })

    multiSourceEL.addEventListener('change', function(){

        if (multiSourceEL.value == ''){
            multiSourceDiv.classList.add('d-none')
            hmsPatientsDiv.classList.add('d-none')
            hmsStaffDiv.classList.add('d-none')
            numbersDiv.classList.add('d-none')
        }

        if (multiSourceEL.value == 'hmsPatients'){
            multiSourceDiv.classList.remove('d-none')
            hmsPatientsDiv.classList.remove('d-none')
            hmsStaffDiv.classList.add('d-none')
            numbersDiv.classList.add('d-none')
        }
        if (multiSourceEL.value == 'hmsStaff'){
            multiSourceDiv.classList.remove('d-none')
            hmsStaffDiv.classList.remove('d-none')
            hmsPatientsDiv.classList.add('d-none')   
            numbersDiv.classList.add('d-none')   
        }
        if (multiSourceEL.value == 'numbers'){
            multiSourceDiv.classList.remove('d-none')
            numbersDiv.classList.remove('d-none')
            hmsPatientsDiv.classList.add('d-none')   
            hmsStaffDiv.classList.add('d-none')   
        }
        
    })

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

    openSendSmsBtn.addEventListener('click', function(){
        sendSmsModal.show();
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

    searchPatientInput.addEventListener('input', function () {
        const datalistEl = searchPatientDiv.querySelector(`#patientList`)
            if (searchPatientInput.value <= 2) {
            datalistEl.innerHTML = ''
            }
            if (searchPatientInput.value.length > 2) {
                http.get(`/patients/list`, { params: { fullId: searchPatientInput.value } })
                .then((response) => {
                    displayPatients(datalistEl, response.data);
                })
                .catch((error) => {
                    console.error('Error fetching patients:', error);
                });
            }
    })

    searchStaffInput.addEventListener('input', function () {
        const datalistEl = searchStaffDiv.querySelector(`#staffList`)
            if (searchStaffInput.value.length <= 2) {
                datalistEl.innerHTML = ''
            }
            if (searchStaffInput.value.length > 2) {
                http.get(`/users/list/staff`, { params: { username: searchStaffInput.value } })
                .then((response) => {
                    displayStaff(datalistEl, response.data);
                })
                .catch((error) => {
                    console.error('Error fetching users:', error);
                });
            }
    })

    sendSmsBtn.addEventListener('click', function () {
            let patient, staff, data, url
            const modalData = {...getDivData(sendSmsModal._element)};
            const smsDetails = modalData.smsDetails;

            if (singleSourceEL.value == '' && multiSourceEL.value == ''){
                alert('Please selecte a source');
                return;
            }

            if (smsDetails == ''){
                handleValidationErrors({"smsDetails" : ['Cannot send an empty text', ]}, sendSmsModal._element);
                return;
            }
             
            if (singleSourceEL.value === 'patient'){
                if (modalData.patient == ''){
                    handleValidationErrors({"patient" : ['Please pick a patient from the list', ]}, sendSmsModal._element);
                    return;
                }
                patient = parsePatientData(modalData.patient);
                data    = {smsDetails, ...patient};
                url     = 'singlepatient';
            }

            if (singleSourceEL.value == 'staff'){
                if (modalData.staff == ''){
                    handleValidationErrors({"staff" : ['Please pick a staff from the list', ]}, sendSmsModal._element);
                    return;
                }
                staff   = parseStaffData(modalData.staff);
                data    = {smsDetails, ...staff};
                url     = 'singlestaff';
            }

            if (singleSourceEL.value == 'number'){
                if (modalData.phone == ''){
                    handleValidationErrors({"phone" : ['Please type a number', ]}, sendSmsModal._element);
                    return;
                }
                data    = {...modalData};
                url     = 'number';
            }

            if (multiSourceEL.value == 'numbers'){
                if (modalData.phones == ''){
                    handleValidationErrors({"phones" : ['Please type atleast one or more numbers separated by commas', ]}, sendSmsModal._element);
                    return;
                }
                data    = {...modalData};
                url     = 'number';
            }

            if (multiSourceEL.value == 'hmsPatients'){
                if (modalData.patientCategory == ''){
                    handleValidationErrors({"patientCategory" : ['Please select category', ]}, sendSmsModal._element);
                    return;
                }
                if (modalData.startDate == ''){
                    handleValidationErrors({"startDate" : ['Please fill both start date', ]}, sendSmsModal._element);
                    return;
                }
                if (modalData.endDate == ''){
                    handleValidationErrors({"endDate" : ['Please fill end date', ]}, sendSmsModal._element);
                    return;
                }
                
                data    = {...modalData};
                url     = 'multipatients';
            }

            if (multiSourceEL.value == 'hmsStaff'){
                 if (modalData.designation == ''){
                    handleValidationErrors({"designation" : ['Please select designation', ]}, sendSmsModal._element);
                    return;
                }
                data    = {...modalData};
                url     = 'multistaff';
            }
           
            sendSmsBtn.setAttribute('disabled', 'disabled')
            http.post(`/customsms/${url}`, {...data}, {"html": sendSmsModal._element})
            .then((response) => {
                if (response.status >= 200 || response.status <= 300){
                        sendSmsModal.hide()
                        singleReceiverDiv.classList.add('d-none');
                        multipleReceiversDiv.classList.add('d-none');
                        clearDivValues(sendSmsModal._element)
                        clearValidationErrors(sendSmsModal._element)
                    }
                    const message = response.data.message;
                    showToast(message, 'success');
                    sendSmsBtn.removeAttribute('disabled')
                    console.log(message)
            })
            .catch((error) => {
                sendSmsBtn.removeAttribute('disabled')
                const message = error.response.data.message;
                showToast(message, 'warning');
            })
        })

});

function displayPatients(datalistEl, data) {
    data.forEach(patient => {
        const fullId = patient.fullId;
        if (!datalistEl.querySelector(`option[value="${fullId}"]`)) {
            const option = document.createElement("option");
            option.setAttribute('value', fullId);
            option.setAttribute('data-cardNo', patient.cardNo);
            option.setAttribute('data-id', patient.id);
            datalistEl.appendChild(option);
        }
    });
}

function displayStaff(datalistEl, data) {
    data.forEach(user => {
        const staff = user.staff
        if (!datalistEl.querySelector(`option[value="${staff}"]`)) {
            const option = document.createElement("option");
            option.setAttribute('value', staff);
            datalistEl.appendChild(option);
        }
    });
}

function parsePatientData(inputString) {
  // Regex Breakdown:
  // ^\S+\s+             <- Matches CardNo (any non-space characters) followed by spaces
  // (?<firstname>\S+)   <- Captures Firstname (first continuous block of text)
  // (?:\s+\S+)*?        <- Non-greedy match for optional middle name(s)
  // \s+(?<lastname>\S+) <- Captures Lastname right before the phone number
  // \s+\((?<phone>[^)]+)\) <- Captures everything inside the first set of parentheses
  // \s+\((?<sponsor>[^)]+)\) <- Captures everything inside the second set of parentheses
  
  const regex = /^\S+\s+(?<firstname>\S+)(?:\s+\S+)*?\s+(?<lastname>\S+)\s+\((?<phone>[^)]+)\)\s+\((?<sponsor>[^)]+)\)/;
  
  const match = inputString.trim().match(regex);
  
  if (!match) {
    return null; // Return null if the string format doesn't match
  }
  
  // Extract just the specific fields you need from the named groups
  const { firstname, phone } = match.groups;
  
  return { firstname, phone };
}

function parseStaffData(inputString) {
  // Regex Breakdown:
  // ^(?<username>.+?)    <- Matches the username (non-greedy, allows spaces for "Nurse Blessing")
  // \s+                  <- Matches the spaces separating the username and phone number
  // (?<phone>\+?[\d()-\s]+)$ <- Matches the phone number at the strict end of the string
  const regex = /^(?<username>.+?)\s+(?<phone>\+?[\d()-\s]+)$/;

  const match = inputString.trim().match(regex);

  if (!match) {
    return null; // Returns null if the string doesn't end with a recognizable phone number
  }

  // Extract and clean up the captured groups
  const username = match.groups.username.trim();
  const phone = match.groups.phone.trim();

  return { username, phone };
}