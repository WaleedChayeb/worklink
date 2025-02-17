/**
 * Create job page component
 */
"use strict";
/* global CreateHelper, Company, JobCreate */

$(function () {

    CreateHelper.initSelectize();

    const draftData = CreateHelper.populateDraftData();
    if(draftData !== null){
        // Populating draft data, if available
        CreateHelper.initDraft(draftData);
    }

    CreateHelper.initFormSave('create');
    Company.initCompanyLogoUploader('.company-logo-wrapper','/attachment/upload/company');
    CreateHelper.initDraftClearButton();
    CreateHelper.restoreCompanySelectorState(draftData);

});


// Saving draft data before unload
window.addEventListener('beforeunload', function () {
    if(!JobCreate.isSavingRedirect){
        CreateHelper.saveDraftData();
    }
});
