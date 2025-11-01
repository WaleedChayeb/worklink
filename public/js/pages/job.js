/**
 * Purchase job listing page component
 */
"use strict";
/* global jobData, app, launchToast, trans */

$(function () {

    $('.application-link').on('click', function () {
        $.ajax({
            type: 'POST',
            data: {
                'id': jobData.jobID
            },
            dataType: 'json',
            url: app.baseUrl+'/jobs/add/applicant',
            success: function () {
                // Silent request
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.message);
            }
        });
    });

});
