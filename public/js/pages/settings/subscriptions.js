/**
 * Subscription settings component
 */
"use strict";
/* global app */

var SubscriptionsSettings = {
    selectedSubID: null,

    /**
     * Confirms subs to be canceled
     * @param subIDToCancel
     */
    confirmSubCancelation: function (subIDToCancel) {
        SubscriptionsSettings.selectedSubID = subIDToCancel;
        $('#subscription-cancel-dialog').modal('show');
    },

    /**
     * Cancels the actual subscription
     */
    cancelSubscription: function () {
        window.location.href = app.baseUrl + '/subscriptions/'+SubscriptionsSettings.selectedSubID+'/cancel';
    }
};
