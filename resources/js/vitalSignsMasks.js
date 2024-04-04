import { MaskInput } from "maska"

window.addEventListener('DOMContentLoaded', function(){
    const temperature       = new MaskInput("#temperature", {eager:true})
    const bloodPressure     = new MaskInput("#bloodPressure", {mask: ['##/##mmHg', '###/##mmHg', '###/###mmHg'], eager: true}) 
    const pulseRate         = new MaskInput("#pulseRate", {mask:["##bpm", "###bpm"], eager:true})
    const respirationRate   = new MaskInput("#respiratoryRate", {eager:true})
    const spO2              = new MaskInput("#spO2", {mask:["##%", "###%"], eager:true})
    const weight            = new MaskInput("#weight", {mask:["#kg", "##kg", "###kg"], eager:true})
    const height            = new MaskInput("#height", {mask: ['#.##m'], eager:true, })
    const headCircumference = new MaskInput("#headCircumference", {mask: ["##.#cm"], eager:true, })
    const midArmCircuference = new MaskInput("#midArmCircuference", {mask: ["##.#cm"], eager:true, })
})
