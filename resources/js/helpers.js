function clearDivValues(div) {
    const tagName = div.querySelectorAll('input, select, textarea')

        tagName.forEach(tag => {
            tag.value = ''
        });        
}

export {clearDivValues}