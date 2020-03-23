import Action from "../../objects/Action/Action";
import Subject from "../../objects/Subject/Subject";
import Feature from "../../objects/Feature/Feature";
import Permission from "../../objects/Permission/Permission";

class PermissionHandlerTool
{

    constructor()
    {
        this.__$container = $("#permissions");
    }

    onClickOnButtonSendPermissionsModifications(active)
    {

        if(active)
        {
            this.__$container.find("button#updatePermissionsButton").on("click.onClickOnButtonSendPermissionsModifications", e => {

                let data = {
                    user_id: $("#profile").data('user'),
                    permissions: []
                };

                this.__$container.find("input[type='checkbox'].permission").each( (index, input) => {

                    let permission = new Permission();
                    /*let action = new Action();
                    let subject = new Subject();

                    let actionName = this.__$container.find(`table thead th[data-id='${ $(input).data('action') }']`).text();
                    let subjectName = this.__$container.find(`table tbody td[data-id='${ $(input).data('subject') }']`).text();

                    action.setId( $(input).data('action') )
                          .setName( actionName )
                    ;

                    subject.setId( $(input).data('subject') )
                           .setName( subjectName )
                    ;*/

                    permission.setId( $(input).data('permission_id') )
                              .setName( $(input).data('permission_name') )
                              //.setAction(action)
                              //.setSubject(subject)
                              .setState( $(input).is(':checked') )
                    ;

                    data.permissions.push(permission);

                } );

                $("#result").empty();
                $("#result").hide();

                $.ajax({
                    type: 'post',
                    url: `/edit/user/${ data.user_id }/permissions`,
                    data: {
                        permissions: JSON.stringify(data.permissions)
                    }
                })

                    .done( (response) => {

                        if(response.status === 200)
                        {
                            if( $("#result").find("p").length < 1 )
                            {

                                $("<p>", {
                                    text: 'Permission updated !'
                                }).appendTo($("#result"));

                            }
                        }
                        else
                        {
                            if( $("#result").find("p").length < 1 )
                            {

                                $("<p>", {
                                    text: 'Error !'
                                }).appendTo($("#result"));

                            }
                        }

                        $("#result").show();
                        console.log(response); //debugger

                    } )

                    .fail( (errorType, errorStatus, errorThrown ) => {

                        if( $("#result").find("p").length < 1 )
                        {

                            $("<p>", {
                                text: 'Error !'
                            }).appendTo($("#result"));

                        }
                        $("#result").show();
                        console.error(errorType, errorStatus, errorThrown);

                    } );

            })
        }

        else
        {
            this.__$container.find("button#updatePermissionsButton").off("click.onClickOnButtonSendPermissionsModifications")
        }

    }





    enable()
    {
        this.onClickOnButtonSendPermissionsModifications(true);
    }

    disable()
    {
        this.onClickOnButtonSendPermissionsModifications(false);
    }

}

export default PermissionHandlerTool;