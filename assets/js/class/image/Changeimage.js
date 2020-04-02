class Changeimage  {
    changeimage() {	

        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
            $('#upload-image').attr('src', e.target.result);
            $('.image-title').html(input.files[0].name);
            $('.img-update p').hide();
            console.log(e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
        }

        $("#file-input").change(function() {
            readURL(this);
        });
            
    }
    
}
  
export {Changeimage}