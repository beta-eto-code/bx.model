function loadExtendedGrid(gridName) {
    let extendedGrid = function (){};
    extendedGrid.prototype = BX.Main.gridManager.getById(gridName).instance;
    extendedGrid.prototype.sendGroupAction = function (method, actionCode, gridId) {
        const data = {
            action: actionCode,
            type: 'group',
            grid_id: gridId,
            id: this.getRows().getSelectedIds(),
        };

        this.reloadTable(method, data)
    };

    return new extendedGrid();
}