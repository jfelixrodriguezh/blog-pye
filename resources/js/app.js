import * as bootstrap from 'bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css'
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.css';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

// El JS de CORK (resources/layouts/horizontal-light-menu/app.js) espera
// encontrar "bootstrap" como variable global (así estaba armado en el
// proyecto original, cargándolo con un <script> suelto). Como ahora lo
// instalamos como paquete npm, lo exponemos manualmente en window.
window.bootstrap = bootstrap;
window.Swal = Swal;
window.TomSelect = TomSelect;
window.Quill = Quill;
