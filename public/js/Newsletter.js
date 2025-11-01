/**
 * Purchase job listing page component
 */
"use strict";
/* global app, launchToast, trans */

$(function () {
    $('#subscriber-email-field').keypress(function (e) {
        if (e.which === 13) {
            if(window.location.href.indexOf('unsubscribe') >= 0){
                NewsLetter.removeEmailSubscriber();
            }
            else{
                NewsLetter.addEmailSubscriber();
            }
            return false;
        }
    });
});

/**
 * Jobs create class
 */
var NewsLetter = {

    /**
     * Adds new email to the newsletter
     */
    addEmailSubscriber: function () {
        $.ajax({
            type: 'POST',
            data: {
                email: $('#subscriber-email-field').val(),
            },
            url: app.baseUrl + '/newsletter/add',
            success: function (result) {
                launchToast('success',trans('Success'),result.message);
            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.errors.email);
            }
        });
    },

    /**
     * Removes new email to the newsletter
     */
    removeEmailSubscriber: function () {
        $.ajax({
            type: 'DELETE',
            data: {
                email: $('#subscriber-email-field').val(),
            },
            url: app.baseUrl + '/newsletter/remove',
            success: function (result) {
                if(result.success){
                    launchToast('success', trans('Success'), result.message);
                    $('.unsubscribe-button').attr('onclick','');
                    $('.unsubscribe-button').addClass('disabled');
                    $('.unsubscribe-button').removeClass('btn-grow');
                }
                else{
                    launchToast('danger', trans('Error'), result.error);
                }

            },
            error: function (result) {
                launchToast('danger',trans('Error'),result.responseJSON.errors.email);
            }
        });
    }

};
