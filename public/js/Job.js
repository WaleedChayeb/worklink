/**
 * Purchase job listing page component
 */
"use strict";
/* global app, launchToast, trans, redirect, copyJobUrl */

$(function () {

});
/**
 * Jobs create class
 */
var Job = {

    jobIDToDelete: null,

    /**
     * Copies the job URL to clipboard
     * @param text
     */
    copyJobUrl: function (text) {
        copyJobUrl(text);
    },

    /**
     * Job delete confirm dialog
     * @param id
     */
    confirmDelete: function(id) {
        Job.jobIDToDelete = id;
        $('#job-delete-dialog').modal('show');
    },

    /**
     * Job deletion function
     */
    delete: function () {
        $('#job-delete-dialog').modal('hide');

        $.ajax({
            type: 'DELETE',
            data: {
                'id': Job.jobIDToDelete
            },
            dataType: 'json',
            url: app.baseUrl+'/my/jobs/delete',
            success: function (result) {
                if(result.success){
                    redirect(app.baseUrl+'/my/jobs');
                }
                else{
                    launchToast('danger',trans('Error'),result.errors[0]);
                }
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    }
};
