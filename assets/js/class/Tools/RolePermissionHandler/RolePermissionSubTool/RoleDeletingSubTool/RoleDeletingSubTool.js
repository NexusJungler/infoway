import RolePermissionSubTool from "../RolePermissionSubTool";

class RoleDeletingSubTool extends RolePermissionSubTool
{

    constructor() {
        super();
        this.__name = this.constructor.name;
        this.__$container = $(".popup_delete_container");
        this.__$location = $(".popup_delete");
        this.__$rolesList = $('.roles_list');
        this.__$deleteRoleBtn = $('.delete_role_btn');
        this.__rolesToDelete = [];
    }

    addRoleInDeleteList(roleInfos = {id: null, name: null})
    {

        if(typeof roleInfos !== "object")
            throw new Error(`Invalid parameter given to ${this.__name}.addRoleInDeleteList() ! Parameter must be object but '${ typeof roleInfos }' given`);

        else if(!roleInfos.hasOwnProperty('id'))
            throw new Error(`Missing 'id' property in ${this.__name}.addRoleInDeleteList() parameter`);

        else if(!roleInfos.hasOwnProperty('name'))
            throw new Error(`Missing 'name' property in ${this.__name}.addRoleInDeleteList() parameter`);

        else
        {

            let roleId = parseInt(roleInfos.id);

            if( (!isNaN(roleId)) && (roleId > 0) && (this.__rolesToDelete.findIndex( roleToDelete => roleToDelete.id === roleId ) === -1))
                this.__rolesToDelete.push(roleInfos);

        }

        return this;
    }

    removeRoleFromDeleteList(roleId)
    {
        roleId = parseInt(roleId);
        if( (!isNaN(roleId)) && (roleId > 0))
            this.__rolesToDelete.splice(this.__rolesToDelete.findIndex(roleToDelete => roleToDelete.id === roleId), 1);

        return this;
    }

    onRoleSelection(active)
    {
        if(active)
        {
            this.__$rolesList.on('change.onRoleSelection', '.select_role_checkbox', e => {

                this.__$deleteRoleBtn.removeAttr('disabled');

                if(this.__$rolesList.find('.select_role_checkbox:checked').length <= 0)
                {
                    this.__$deleteRoleBtn.attr('disabled', true);
                    this.__rolesToDelete = [];
                }

                else if( $(e.currentTarget).is(':checked') )
                {
                    this.addRoleInDeleteList( {id: $(e.currentTarget).parents('tr').data('role_id'), name: $(e.currentTarget).parents('tr').find('.role_name').val()} );
                }

                else
                {
                    this.removeRoleFromDeleteList( $(e.currentTarget).parents('tr').data('role_id') );
                }

                //console.table( this.__rolesToDelete ); debugger

            })
        }
        else
        {
            this.__$rolesList.off('change.onRoleSelection', '.select_role_checkbox');
        }

        return this;
    }

    onClickAllSelectionBtn(active)
    {
        if(active)
        {
            $('.select_all_roles_checkbox').on('change.onClickAllSelectionBtn', e => {

                if( $(e.currentTarget).is(':checked') )
                {
                    this.__$deleteRoleBtn.removeAttr('disabled');
                    this.__$rolesList.find('.select_role_checkbox').each( (index, element) => {

                        $(element).prop('checked', true);
                        this.addRoleInDeleteList( {id: $(element).parents('tr').data('role_id'), name: $(element).parents('tr').find('.role_name').val()} );

                    } );
                }
                else
                {
                    this.__$rolesList.find('.select_role_checkbox').prop('checked', false);
                    this.__$deleteRoleBtn.attr('disabled', true);
                    this.__$location.find('.delete_list_container .delete_list').empty();
                    this.__rolesToDelete = [];
                }

            })
        }
        else
        {
            $('.select_all_roles_checkbox').off('change.onClickAllSelectionBtn');
        }

        return this;
    }

    onClickOnDeleteBtn(active)
    {
        if(active)
        {

            this.__$deleteRoleBtn.on('click.onClickOnDeleteBtn', e => {

                this.__rolesToDelete.forEach( roleToDelete => {

                    $(`<li>${roleToDelete.name}</li>`).appendTo( this.__$location.find('.delete_list') );

                } )

                this.__$container.addClass('is_open');

            })

        }
        else
        {
            this.__$deleteRoleBtn.off('click.onClickOnDeleteBtn');
        }

        return this;
    }

    onClickOnPopupCloseBtn(active)
    {
        if(active)
        {

            this.__$location.on('click.onClickOnPopupCloseBtn', '.close_popup_button_container', e => {

                this.__$container.removeClass('is_open');
                this.__$location.find('.delete_list_container .delete_list').empty();

            })

        }
        else
        {
            this.__$location.off('click.onClickOnPopupCloseBtn', '.close_popup_button_container');
        }

        return this;
    }

    onClickOnDeleteValidationBtn(active)
    {
        if(active)
        {
            this.__$location.on('click.onClickOnDeleteValidationBtn', '.deleting_confirmation_btn', e => {

                if(this.__rolesToDelete.length > 0)
                {

                    super.changeLoadingPopupText("Suppression des roles en cours...");
                    super.showLoadingPopup();

                    //let ids = this.__rolesToDelete.map( roleToDelete => roleToDelete.id );
                    //console.log(ids); debugger

                    $.ajax({
                        url: (this.__rolesToDelete.length  < 2) ? `/delete/role/${ this.__rolesToDelete[0].id }` : `/delete/roles`,
                        type: "POST",
                        data: (this.__rolesToDelete.length  < 2) ? {} : { rolesToDelete: this.__rolesToDelete.map( roleToDelete => roleToDelete.id ) },
                        success: (response) => {

                            if(response.status !== '200 OK')
                            {

                                if(this.__rolesToDelete.length  > 1)
                                    alert(`Erreur durant la suppression des roles : ${response.errors}`)

                                else
                                    alert(`Erreur durant la suppression du role`)

                            }
                            else
                            {

                                this.__rolesToDelete.forEach( roleToDelete => {

                                    this.__$rolesList.find(`tr[data-role_id='${ roleToDelete.id }']`).remove();

                                } )

                                this.__rolesToDelete = [];

                            }

                        },
                        error: (response, status, error) => {

                            console.error(response); debugger

                        },
                        complete: () => {

                            super.hideLoadingPopup();

                        }
                    });

                }


            })
        }
        else
        {
            this.__$location.off('click.onClickOnDeleteValidationBtn', '.deleting_confirmation_btn');
        }

        return this;
    }

    enable() {
        super.enable();
        this.onRoleSelection(true)
            .onClickOnDeleteBtn(true)
            .onClickOnPopupCloseBtn(true)
            .onClickAllSelectionBtn(true)
            .onClickOnDeleteValidationBtn(true)
        ;
    }

    disable() {
        super.disable();
        this.onRoleSelection(false)
            .onClickOnDeleteBtn(false)
            .onClickOnPopupCloseBtn(false)
            .onClickAllSelectionBtn(false)
            .onClickOnDeleteValidationBtn(false)
        ;
    }

}

export default RoleDeletingSubTool;