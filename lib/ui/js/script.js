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
        const pageSelector = document.getElementsByClassName('main-ui-pagination-active');
        if (pageSelector.length > 0) {
            page = pageSelector.item(0).innerText;
        }

        this.reloadTable(method, data, null, `?nav_${gridId}=page-${page}-size-`);
    };

    return new extendedGrid();
}