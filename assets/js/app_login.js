require('../css/app_login.css');


const $ = require('jquery');
global.$ = global.jQuery = $;


if($('#message').data('message') !== undefined)
{

    if($('#message').data('message') === "Utilisateur introuvable")
    {

        $("input[name='username']").prev("span.error").text("Utilisateur introuvable").css({'color': 'red'});

        $("input[name='username']").css({'box-shadow': '0 0 0 2pt red'})
                                   .focus();

        const end = parseInt($("input[name='username']").val().length);

        const input = document.getElementById("username");

        input.setSelectionRange(0, end);

        $("button[type='submit']").css({'top': '52%'});

    }
    else
    {

        $("input[name='user_password']").prev("span.error").text("Mauvais mot de passe").css({'color': 'red'});

        $("input[name='user_password']").css({'box-shadow': '0 0 0 2pt red'})
                                        .focus();

        $("button[type='submit']").css({'top': '52%'});

    }

    $('#message').remove();

}