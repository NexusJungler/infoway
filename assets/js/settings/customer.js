/** SCSS **/
import '../../css/settings/customer/create_customer.scss';
import '../../css/settings/customer/edit_customer.scss';
import '../../css/settings/customer/list_customer.scss';

// jquery 
const $ = require('jquery');
global.$ = global.jQuery = $;

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
         
        reader.onload = function (e) {
            $('.update-image').attr('src', e.target.result);
        }
         
        reader.readAsDataURL(input.files[0]);
    }
}
 
$(".inputfile").change(function(){
    readURL(this);
})