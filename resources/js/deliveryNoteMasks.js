import { MaskInput } from "maska"

window.addEventListener('DOMContentLoaded', function(){
    const birthWeight       = new MaskInput("#birthWeight", {mask:["#.#kg"], eager:true})
    const headCircumference = new MaskInput("#headCircumference", {mask: ["##.#cm"], eager:true, })
    const ebl               = new MaskInput("#ebl", {mask: ["#ml(s)", "##ml(s)", "###ml(s)", "####ml(s)"], eager:true, })
    const descent           = new MaskInput("#descent", {mask: ["#/5"], eager: true});
})
