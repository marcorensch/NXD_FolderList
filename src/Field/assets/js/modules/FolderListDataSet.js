import {FolderElement} from "./FolderElement.js";

class FolderListDataSet {
    constructor(currentFolderPath = null) {
        this.children = [];
        this.currentFolderPath = currentFolderPath;
        this.parentFolderPath = null;
        this.container = null;
    }

    setContainer($container) {
        this.container = $container;
    }

    setParentFolder(parentFolderPath) {
        this.parentFolderPath = parentFolderPath;
    }

    setCurrentFolder(currentFolderPath) {
        this.currentFolderPath = currentFolderPath;
    }

    async updateDataSet(newFolderPath) {
        if(!newFolderPath) return;
        console.log('Updating Data Set')
        this.setCurrentFolder(newFolderPath);
        this.parentFolderPath = null;
        this.children = [];
        await this.getDataSet();
        this.renderChildren();
    }
    addChildren(folders) {
        if(!folders) return;
        for (const folder of folders) {
            this.addChild(folder);
        }
    }
    addChild(folder) {
        const folderElement = new FolderElement(folder.name);
        folderElement.relativePath = folder.relativePath;
        folderElement.realPath = folder.realPath;
        this.children.push(folderElement);
    }

    getChildren() {
        return this.children;
    }

    getRelativePathOfChild(childName) {
        childName = childName.toString();
        const filtered = this.children.filter(f => f.name === childName);
        return filtered[0].relativePath;
    }

    getRealPathOfChild(childName) {
        childName = childName.toString();
        const filtered = this.children.filter(f => f.name === childName);
        return filtered[0].realPath;
    }

    /**
     * @function renderChildren
     */
    renderChildren($customContainer = null) {
        const renderingContainer = $customContainer ?? this.container;
        if(!renderingContainer){
            console.error('No Container Defined');
            return;
        }
        const folderIconHtml = '<span class="folder-icon me-2"><i class="fas fa-folder"></i></span>';
        let folderListHtml = '';

        if (this.children.length === 0) {
            folderListHtml += 'No Folders Found';
        }

        let folderUpHtml = '';
        if (this.parentFolderPath) {
            folderUpHtml = '<div class="folder-item" data-folder-name=".."><span class="folder-icon me-2"><i class="fas fa-folder"></i></span>..</div>';
        }
        for ( const folderListElement of this.getChildren() ) {
            folderListHtml += '<div class="folder-item" data-folder-name="' + folderListElement.name + '">' + folderIconHtml + folderListElement.name + '</div>';
        }

        renderingContainer.html(folderUpHtml + folderListHtml);

        this._addEventListenersToFolderItems(renderingContainer);

    }

    _addEventListenersToFolderItems($container) {
        // Add Event Listeners to Folder Items
        const folders = $container.find('.folder-item');
        for (const folder of folders) {
            const folderName = jQuery(folder).data('folder-name');

            if(folderName === '..'){
                jQuery(folder).click(() => {
                    this._showWaiting($container);
                    this.updateDataSet(this.parentFolderPath).then(r => { this._hideWaiting($container) });
                });
                continue;
            }

            const relativePath = this.getRelativePathOfChild(folderName);
            const realPath = this.getRealPathOfChild(folderName);
            jQuery(folder).click(() => {
                this._showWaiting($container);
                this.updateDataSet(relativePath).then(r => { this._hideWaiting($container) });
            });

        }
    }

    _hideWaiting($container) {
        // Hide the Waiting if spinner is visible
        const spinner = $container.find('.loading-spinner-container');
        spinner.fadeOut('slow');
    }

    _showWaiting($container) {
        // Show the Waiting spinner
        const spinner = $container.find('.loading-spinner-container');
        spinner.fadeIn('slow');
    }

    async getDataSet() {

        let request = {
            'data': JSON.stringify({"path": this.currentFolderPath }),
            'format': 'json',
            'option': 'com_ajax',
            'group': 'fields',
            'plugin': 'nxdfolderlist'
        };

        const res = await this._ajaxCall(request);
        const responseData = JSON.parse(res.data);

        if(res){
            this.addChildren(responseData.children);
            this.setParentFolder(responseData.relativeParentFolderPath);
            return true;
        }

        return false;
    }

    async _ajaxCall(request) {
        return await jQuery.ajax({
            url: '/index.php',
            data: request,
            type: "GET",
            success: function (response) {
                try{
                    return response;
                }catch (e) {
                    console.error(e)
                    return false;
                }
            },
            error: function (data) {
                //handle error
                console.error(data)
                return false;
            }
        });
    }
}

export {FolderListDataSet} ;