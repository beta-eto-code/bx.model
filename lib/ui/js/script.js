function loadExtendedGrid(gridName) {
    let extendedGrid = function (){};
    extendedGrid.prototype = BX.Main.gridManager.getById(gridName).instance;
    extendedGrid.prototype.sendGroupAction = function (method, actionCode, gridId) {
        let data = {
            action: actionCode,
            type: 'group',
            grid_id: gridId,
            id: this.getRows().getSelectedIds(),
        };

        this.reloadTableOnCurrentPage(method, data, gridId);
    };

    extendedGrid.prototype.reloadTableOnCurrentPage = function (method, data, gridId) {
        let page = 1;
        if ((!!this.pageSize.parent.data) === true) {
            const pageList = this.pageSize.parent.data.pagination.firstElementChild.lastElementChild.children || [];
            for(let i = 0; i < pageList.length; i++) {
                let item = pageList.item(i);
                if ( (!!item.href) === false ) {
                    page = item.innerText;
                }
            }
        }

        this.reloadTable(method, data, null, `?nav_${gridId}=page-${page}-size-`);
    };

    return new extendedGrid();
}