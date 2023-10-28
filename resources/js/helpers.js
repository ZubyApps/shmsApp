function clearDivValues(div) {
    const tagName = div.querySelectorAll('input, select, textarea')

        tagName.forEach(tag => {
            tag.value = ''
        });        
}

function clearItemsList(element){
    element.innerHTML = ''
}

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

function getOrdinal(n) {
    let ord = 'th';
  
    if (n % 10 == 1 && n % 100 != 11)
    {
      ord = 'st';
    }
    else if (n % 10 == 2 && n % 100 != 12)
    {
      ord = 'nd';
    }
    else if (n % 10 == 3 && n % 100 != 13)
    {
      ord = 'rd';
    }
  
    return ord;
  }

  function getDivData(div) {
    let data = {}
    const fields = [
        ...div.getElementsByTagName('input'),
        ...div.getElementsByTagName('select'),
        ...div.getElementsByTagName('textarea')
    ]

    fields.forEach(select => {
        select.hasAttribute('name') ?
            data[select.name] = select.value : ''
    })

    return data
}

function removeAttributeLoop(element, attribute, value) {
    element.forEach(tag => {
        tag.removeAttribute(attribute)
    })
}

function toggleAttributeLoop(element, attribute, value) {
    element.forEach(tag => {
        tag.toggleAttribute(attribute)
    })
}

function querySelectAllTags(div, ...tags){
    return div.querySelectorAll(tags)
}

function textareaHeightAdjustment(setHeight, tag){
    for (let i = 0; i < tag.length; i++) {
        if (tag[i].value == '') {
            tag[i].setAttribute("style", "height:" + setHeight + "px;overflow-y:hidden;");
        } else {
            tag[i].setAttribute("style", "height:" + (tag[i].scrollHeight) + "px;overflow-y:hidden;");
        }
        tag[i].addEventListener("input", OnInput, false);
    }
}

function OnInput(e){
    let setHeight = 65
    this.scrollHeight < setHeight || this.value === '' ? this.style.height = setHeight + "px" : this.style.height = this.scrollHeight + "px";
}

function dispatchEvent(tag, event) {
    for (let i = 0; i < tag.length; i++) {
        tag[i].dispatchEvent(new Event(event, { bubbles: true }))
    }
}

function handleValidationErrors(errors, domElement) {
    console.log(errors)
    for (const name in errors) {
        console.log(name)
        const element = domElement.querySelector(`[name="${ name }"]`)
        
        element.classList.add('is-invalid')

        const errorDiv = document.createElement('div')

        errorDiv.classList.add('invalid-feedback')
        errorDiv.textContent = errors[name][0]

        element.parentNode.append(errorDiv)
    }
}

function clearValidationErrors(domElement) {
    domElement.querySelectorAll('.is-invalid').forEach(function (element) {
        element.classList.remove('is-invalid')

        element.parentNode.querySelectorAll('.invalid-feedback').forEach(function (e) {
            e.remove()
        })
    })
}

const getSelctedText = (selectEl) => {
    return selectEl.options[selectEl.selectedIndex]
}

function displayList(dataListEl, optionsId, data) {
    
    dataListEl.innerHTML = ''
    
    
    data.forEach(line => {
        const option = document.createElement("OPTION")
        option.setAttribute('id', optionsId)
        option.setAttribute('value', line.name)
        option.setAttribute('data-id', line.id)
        option.setAttribute('name', line.name)
        dataListEl.appendChild(option)
    })

    }

function getDatalistOptionId(modal, inputEl, datalistEl) {    
    const selectedOption = datalistEl.options.namedItem(inputEl.value)
    
        if (selectedOption) {
            return selectedOption.getAttribute('data-id')
        } else {
            return ""
        }
    }

function openModals(modal, button, {id, ...data}) {
    for (let name in data) {

        const nameInput = modal._element.querySelector(`[name="${ name }"]`)
        
        nameInput.value = data[name]
    }
    
    button.setAttribute('data-id', id)
    modal.show()
}
    
export {clearDivValues, clearItemsList, stringToRoman, getOrdinal, getDivData, removeAttributeLoop, toggleAttributeLoop, querySelectAllTags, textareaHeightAdjustment, dispatchEvent, handleValidationErrors, clearValidationErrors, getSelctedText, displayList, getDatalistOptionId, openModals}