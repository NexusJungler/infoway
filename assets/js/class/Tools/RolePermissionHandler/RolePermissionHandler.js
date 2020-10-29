import ParentTool from "../ParentTool";
import RoleDeletingSubTool from "./RolePermissionSubTool/RoleDeletingSubTool/RoleDeletingSubTool";

class RolePermissionHandler extends ParentTool
{

    constructor() {
        super();
        this.__name = this.constructor.name;
        this.__$rolesList = $('.roles_list');
        this.__$rolePermissionsList = $('.roles_permissions_list');
        this.__subTools = [
            new RoleDeletingSubTool(),
        ];
    }

    onClickOnDisplayBtnShowPermissionTable(active)
    {
        if(active)
        {

            $('.show_role_permissions_table_btn').on('click.onClickOnDisplayBtnShowPermissionTable', e => {

                if( this.__$rolePermissionsList.hasClass('hidden') )
                {
                    $('.show_role_permissions_table_btn').text('Cacher');
                    this.__$rolePermissionsList.removeClass('hidden');
                }

                else
                {
                    $('.show_role_permissions_table_btn').text('Afficher');
                    this.__$rolePermissionsList.addClass('hidden');
                }

            })

        }
        else
        {
            $('.show_role_permissions_table_btn').off('click.onClickOnDisplayBtnShowPermissionTable');
        }

        return this;
    }

    enable() {
        super.enable();
        this.onClickOnDisplayBtnShowPermissionTable(true)
        ;
    }

    disable() {
        super.disable();
        this.onClickOnDisplayBtnShowPermissionTable(false)
        ;
    }

}

export default RolePermissionHandler;