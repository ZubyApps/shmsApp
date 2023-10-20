import { Offcanvas, Modal } from "bootstrap";
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList, getOrdinal, getDivData, textareaHeightAdjustment, clearValidationErrors} from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treamentsPharmacy";
import http from "./http";


window.addEventListener('DOMContentLoaded', function () {
    const newSponsorCatgegoryModal          = new Modal(document.getElementById('newSponsorCategoryModal'))
    const updateSponsorCatgegoryModal       = new Modal(document.getElementById('updateSponsorCategoryModal'))

    const addSponsorCategoryBtn             = document.querySelector('#addSponsnorCategoryBtn')

    const createSponsorCategoryBtn          = document.querySelector('#createSponsorCategoryBtn')
    const updateSponsorCategoryBtn          = document.querySelector('#updateSponsorCategoryBtn')

    addSponsorCategoryBtn.addEventListener('click', function () {
        newSponsorCatgegoryModal.show()
    })

    createSponsorCategoryBtn.addEventListener('click', function () {
        http.post('/sponsor/category', getDivData(newSponsorCatgegoryModal._element), {"html": newSponsorCatgegoryModal._element})
            .then((response) => {
                if (response.status >= 200 || response.status <= 300){
                    newSponsorCatgegoryModal.hide()
                    clearDivValues(newSponsorCatgegoryModal._element)
                }
            })
            .catch((error) => {
                alert(error.response.data.message)
            })
    })

    newSponsorCatgegoryModal._element.addEventListener('hidden.bs.modal', function () {
        clearValidationErrors(newSponsorCatgegoryModal._element)
    })
})