/**
 * Jobs create class
 */
"use strict";
/* global app */

// eslint-disable-next-line no-unused-vars
var JobCreate = {

    debugJobCreation: app.debug, // If true - asks for permission before redirecting to next steps in job/company creation flows

    companySelectizeInstance: null,
    skillsSelectizeInstance: null,

    state : {
        skillsData: [],
        companyLogo: '',
        companyType: 'new'
    },

    isSavingRedirect: false,
};
