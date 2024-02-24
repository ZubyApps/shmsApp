import { MaskInput } from "maska"

window.addEventListener('DOMContentLoaded', function(){
    const birthWeight       = new MaskInput("#birthWeight", {mask:["#.#kg"], eager:true})
    // const lengthOfParity    = new MaskInput("#lengthOfParity", {mask: ["##.#cm"], eager:true, })
    const headCircumference = new MaskInput("#headCircumference", {mask: ["##cm"], eager:true, })
    const ebl               = new MaskInput("#ebl", {mask: ["##ml(s)", "###ml(s)", "####ml(s)"], eager:true, })
})
