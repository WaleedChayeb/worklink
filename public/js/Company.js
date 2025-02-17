/*
* Post create page
 */
"use strict";
/* global JobCreate, FileUpload, mediaSettings, app,  */
/* global copyToClipboard, launchToast, trans, redirect */

$(function () {

});


var Company = {

    companyIDToDelete: null,

    /**
     * Copies the job URL to clipboard
     * @param text
     */
    copyJobUrl: function (text) {
        copyToClipboard(text);
        launchToast('success', trans('Success'), trans('Link copied to clipboard')+'.', 'now');
    },

    /**
     * Company delete confirmation dialog
     * @param id
     */
    confirmDelete: function(id) {
        Company.companyIDToDelete = id;
        $('#company-delete-dialog').modal('show');
    },

    /**
     * Company deletion function
     */
    delete: function () {
        $('#company-delete-dialog').modal('hide');
        $.ajax({
            type: 'DELETE',
            data: {
                'id': Company.companyIDToDelete
            },
            dataType: 'json',
            url: app.baseUrl+'/my/companies/delete',
            success: function (result) {
                if(result.success){
                    redirect(app.baseUrl+'/my/companies');
                }
                else{
                    launchToast('danger',trans('Error'),result.errors[0]);
                }
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    },

    /**
     * Company logo uploader init function
     * @param selector
     * @param uploadPath
     */
    initCompanyLogoUploader: function (selector, uploadPath) {
        JobCreate.dropzones = new window.Dropzone(selector, {
            url: app.baseUrl + uploadPath,
            previewTemplate: document.querySelector('.dz-preview').innerHTML.replace('d-none', ''),
            paramName: "file", // The name that will be used to transfer the file
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            clickable:[`${selector}`,`${selector} .card-img-top`],
            maxFilesize: mediaSettings.max_file_upload_size, // MB
            addRemoveLinks: true,
            dictRemoveFile: "x",
            acceptedFiles: mediaSettings.allowed_file_extensions,
            autoDiscover: false,
            init: function() {
                if(FileUpload.attachaments.length > 0){
                    $(selector + ' .card-img-top').attr('src',FileUpload.attachaments[0].thumbnail);
                }
                var _this = this;
                $(".draft-clear-button").on("click", function() {
                    _this.removeAllFiles(true);
                });
            },
            sending: function(file) {
                file.previewElement.innerHTML = "";
            },
            success: function(file, response) {
                $(selector + ' .card-img-top').attr('src',response.path);
                FileUpload.attachaments = [{id: response.attachmentID, path: response.path, type:response.type, thumbnail:response.thumbnail}];
                file.previewElement.innerHTML = "";
            },
            error: function(file, errorMessage) {
                if(typeof errorMessage === 'string'){
                    launchToast('danger','Error ',errorMessage,'now');
                }
                else{
                    launchToast('danger','Error ',errorMessage.errors.file,'now');
                }
                file.previewElement.innerHTML = "";
            }
        });
    },


    /**
     * Instantiates & applies selectize on the new conversation modal
     */
    initSelectizeUserList: function(draftData){
        if(typeof Selectize !== 'undefined') {
            // TODO: Move this selectize instance to to JobCreate
            JobCreate.companySelectizeInstance = $('#company_id').selectize({
                valueField: 'id',
                labelField: [],
                searchField: 'label',
                preload: true,
                options: [],
                create: false,
                sortField: [
                    {
                        field: 'id',
                        direction: 'desc'
                    },
                ],
                render: {
                    option: function (item, escape) {
                        return '<div class="selectize-row  mb-1 d-flex align-items-center">' +
                            '<img class="selectize-avatar ml-3 mr-2 my-1" src="' + escape(item.avatar) + '" alt="">' +
                            '<span class="ml-1 text-truncate">' + escape(item.name) + '</span>' +
                            '</div>';
                    },
                    item: function (item, escape) {
                        return '<div class="selectize-row d-flex align-items-center">' +
                            '<img class="selectize-avatar mx-2 my-1" src="' + escape(item.avatar) + '" alt="">' +
                            '<span class="ml-1 text-truncate">' + escape(item.name) + '</span>' +
                            '</div>';
                    }
                },
                load: function (query, callback) {
                    // if (!query.length) return callback();
                    $.ajax({
                        url:  app.baseUrl + '/my/companies/getSelectizedCompanies',
                        type: 'POST',
                        data: {q: encodeURIComponent(query)},
                        dataType: 'json',
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            callback(Object.values(res));
                            let key = null;
                            // Restoring company per draft data, if exists
                            if(draftData){
                                draftData.map(function (field) {
                                    if (field.name === 'company_id' && field.value !== null) {
                                        key = field.value;
                                    }
                                });
                            }
                            if(!key){
                                let options = $('#company_id')[0].selectize.options;
                                key = Object.keys(options)[Object.keys(options).length - 1];
                            }
                            Company.populateCompaniesSelector(key);
                        }
                    });
                },
            });
        }
    },

    /**
     * Pupulates the companies selector field with data
     * @param key
     */
    populateCompaniesSelector: function (key) {
        JobCreate.companySelectizeInstance[0].selectize.setValue(key);
    }

};
