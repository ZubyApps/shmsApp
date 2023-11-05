import "../css/app.scss";
import "bootstrap";
import "./bootstrap";
import "../../node_modules/jquery/dist/jquery.min.js";
import "../../node_modules/datatables.net/js/jquery.dataTables.min.mjs";
import "../../node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.mjs"
import 'datatables.net-fixedheader-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-fixedcolumns-bs5';
import 'datatables.net-select-bs5';
import 'datatables.net-staterestore-bs5';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
