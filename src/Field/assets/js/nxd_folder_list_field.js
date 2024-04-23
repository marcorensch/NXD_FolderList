import {FolderListDataSet} from "./modules/FolderListDataSet.js";

async function nxdFolderListBtnClickHandler(e, params) {
    /**
     * @function jQuery - jQuery
     *
     * @type {null}
     */
    const inputFieldPath = jQuery('#' + params.inputFieldId).val();
    const definedRootFolder = params.definedRootFolder;
    const path = inputFieldPath ? inputFieldPath : definedRootFolder;
    const folderListDataSet = new FolderListDataSet(path);

    const $modal = jQuery('#modal-nxdFolderSelect_' + params.inputFieldId);
    const $modalInnerContainer = $modal.find('.nxd-modal-inner');

    folderListDataSet.setContainer($modalInnerContainer);

    // Get the Folders
    const response = await folderListDataSet.getDataSet();

    if(response){
        folderListDataSet.renderChildren();
    }

    // Add event listener to save button
    $modal.find('.nxd-modal-save-btn').click(function () {
        jQuery('#' + params.inputFieldId).val(folderListDataSet.currentFolderPath);
    });
}

export {nxdFolderListBtnClickHandler};