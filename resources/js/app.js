import './bootstrap';
import '../css/app.scss';
//require('bootstrap');
//import "datatables.net-bs5/js/dataTables.bootstrap5";
import "../../node_modules/jquery/dist/jquery.min.js";
import '../../node_modules/datatables.net/js/jquery.dataTables.min.mjs';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// npm install --save jszip, pdfmake, datatables.net-dt, datatables.net-buttons-dt, datatables.net-fixedcolumns-dt, datatables.net-fixedheader-dt, datatables.net-select-dt, datatables.net-staterestore-dt