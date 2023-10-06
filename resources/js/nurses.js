import { Offcanvas, Modal } from "bootstrap";
import { consultationDetails, items } from "./data"
import { clearDivValues, clearItemsList } from "./helpers"
import { InitialRegularConsultation, review } from "./dynamicHTMLfiles/treaments";


window.addEventListener('DOMContentLoaded', function () {
    const medicationCanvasTable = new Offcanvas(document.getElementById('offcanvasWithBothOptions'))

    const reviewDetailsModal    = new Modal(document.getElementById('reviewDetailsModal'))

    const detailsBtn            = document.querySelector('.detailsBtn')

    const consultationReviewDiv = document.querySelector('#consultationReviewDiv')
    
    

    medicationCanvasTable._element.addEventListener('shown.bs.offcanvas', function () {
        
    })

    detailsBtn.addEventListener('click', function () {

        let iteration   = 0
        let count       = 0
        consultationDetails.data.forEach(line => {
            iteration++

            if (iteration > 1) {
                count++
                consultationReviewDiv.innerHTML += review(iteration, stringToRoman, count, consultationDetails, line)
            } else {
                consultationReviewDiv.innerHTML += InitialRegularConsultation(iteration, stringToRoman, count, consultationDetails, line)
            } 
             
        })

        reviewDetailsModal.show()
    })
})

function stringToRoman(num) { 
    const values =  
        [1000, 900, 500, 400, 100,  
         90, 50, 40, 10, 9, 5, 4, 1]; 
    const symbols =  
        ['M', 'CM', 'D', 'CD', 'C',  
         'XC', 'L', 'XL', 'X', 'IX',  
         'V', 'IV', 'I']; 
    let result = ''; 
  
    for (let i = 0; i < values.length; i++) { 
        while (num >= values[i]) { 
            result += symbols[i]; 
            num -= values[i]; 
        } 
    } 
  
    return result; 
}