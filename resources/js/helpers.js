function clearDivValues(div) {
    const tagName = div.querySelectorAll('input, select, textarea')

        tagName.forEach(tag => {
            tag.value = ''
        });        
}

function clearItemsList(element){
    element.innerHTML = ''
}

export {clearDivValues, clearItemsList}